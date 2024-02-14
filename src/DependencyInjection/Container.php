<?php

namespace Kinikit\Core\DependencyInjection;

use Kinikit\Core\Annotation\ClassAnnotationParser;
use Kinikit\Core\Proxy\ProxyGenerator;
use Kinikit\Core\Reflection\ClassInspectorProvider;
use Kinikit\Core\Util\Primitive;


/**
 * Standard Inversion of Control (IOC) dependency injection container.  This is a singleton class which
 * maintains a registry of dependency objects which are auto wired into other classes as constructor arguments
 * as required.
 *
 * The only API method required to be called is the createInstance method.  All objects are lazy loaded as required.
 *
 */
class Container {

    private static $instance;

    /**
     * @var mixed[string]
     */
    private $instances = array();


    /**
     * Global interceptors
     *
     * @var \Kinikit\Core\DependencyInjection\ContainerInterceptors
     */
    private $interceptors;


    /**
     * Array of explicit interface mappings (defined in e.g. Bootstrap scripts)
     * for providing concrete implementation to interface.
     *
     * @var string[string]
     */
    private $classMappings = array();


    /**
     * Proxy generator
     *
     * @var ProxyGenerator
     */
    private $proxyGenerator;


    /**
     * @var ClassInspectorProvider
     */
    private $classInspectorProvider;

    /**
     * @var InterfaceResolver
     */
    private $interfaceResolver;


    // Constructor
    public function __construct() {
        $this->interceptors = new ContainerInterceptors();
        $this->proxyGenerator = new ProxyGenerator();
        $this->classInspectorProvider = new ClassInspectorProvider();
        $this->interfaceResolver = new InterfaceResolver($this->classInspectorProvider);

        // Add required class inspector classes to the container upfront to avoid recursion problems.
        $this->instances["\\" . ClassInspectorProvider::class] = $this->classInspectorProvider;
        $this->instances["\\" . ClassAnnotationParser::class] = new ClassAnnotationParser();
    }


    /**
     * Get the singleton instance of this container.
     *
     * @return Container
     */
    public static function instance() {
        if (!self::$instance) {
            self::$instance = new Container();
        }

        return self::$instance;
    }


    /**
     * Get an instance of a supplied class.
     *
     * @param string $className
     */
    public function get($className) {
        return $this->__doGet($className);

    }


    /**
     * Create a new instance of a class - if allowProxy is set this
     * will create a proxy unless noProxy attribute has been set on the class (defaults to false).
     *
     * @param $className
     * @param bool $allowProxy
     */
    public function new($className, $allowProxy = false) {
        return $this->__doGet($className, true, $allowProxy);
    }


    /**
     * Set an instance for a specific class name.  Useful for testing purposes.
     *
     * @param $className
     * @param $instance
     */
    public function set($className, $instance) {
        $className = "\\" . ltrim(trim($className), "\\");
        $this->instances[$className] = $instance;
    }


    /**
     * Get an instance of a particular interface implementation (useful in cases where
     * we need to use multiple types in the same application).
     *
     * @param $interfaceClass
     * @param $implementationKey
     */
    public function getInterfaceImplementation($interfaceClass, $implementationKey) {
        $interfaceClass = $this->interfaceResolver->getImplementationClassForKey($interfaceClass, $implementationKey);
        return $this->get($interfaceClass);
    }


    /**
     * Get the underlying class for an interface implementation.  Particularly useful when creating
     * new instances via the new method
     *
     * @param $interfaceClass
     * @param $implementationKey
     */
    public function getInterfaceImplementationClass($interfaceClass, $implementationKey) {
        return $this->interfaceResolver->getImplementationClassForKey($interfaceClass, $implementationKey);
    }


    /**
     * Add a new interface implementation (useful as part of bootstrapping apps).  Allow for
     * a short key to be associated with an implementation if required.
     *
     * @param $interfaceClass
     * @param $implementationKey
     * @param $implementationClass
     */
    public function addInterfaceImplementation($interfaceClass, $implementationKey, $implementationClass) {
        $this->interfaceResolver->addImplementationClassForKey($interfaceClass, $implementationKey, $implementationClass);
    }


    /**
     * @return ContainerInterceptors
     */
    public function getInterceptors() {
        return $this->interceptors;
    }

    /**
     * @param ContainerInterceptors $interceptors
     */
    public function setInterceptors($interceptors) {
        $this->interceptors = $interceptors;
    }


    /**
     * Add a method interceptor to the list defined for this container.
     * If an array of applicable classes supplied limit to these classes otherwise
     * assume global
     *
     * @param ContainerInterceptor $interceptor
     * @param string[] $applicableClasses
     */
    public function addInterceptor($interceptor, $applicableClasses = []) {
        $this->interceptors->addInterceptor($interceptor, $applicableClasses);
    }


    /**
     * Add a mapping for a class to another class.  This will affect calls made
     * to get and new.
     *
     * @param string $sourceClass
     * @param string $targetClass
     */
    public function addClassMapping($sourceClass, $targetClass) {

        $sourceClass = "\\" . ltrim(trim($sourceClass), "\\");
        $targetClass = "\\" . ltrim(trim($targetClass), "\\");

        $this->classMappings[$sourceClass] = $targetClass;
    }


    /**
     * Return either a mapped class name or the original class if no mapping has been created.
     *
     * @param $className
     */
    public function getClassMapping($className) {
        $sourceClass = "\\" . ltrim(trim($className), "\\");
        return $this->classMappings[$sourceClass] ?? $className;
    }

    /**
     * @param $className
     * @param bool $createNew
     * @param bool $allowProxy
     * @param array $dependentClasses
     * @return object
     * @throws \ReflectionException
     */
    public function __doGet($className, $createNew = false, $allowProxy = true, $dependentClasses = array()) {

        // Remove leading \'s.
        $className = "\\" . ltrim(trim($className), "\\");

        if (isset($this->classMappings[$className])) {
            $className = $this->classMappings[$className];
        }


        $dependentClasses[] = $className;

        // Shortcut if we already have this instance.
        if (!$createNew && isset($this->instances[$className])) {
            return $this->instances[$className];
        }

        // Create a new proxy and ensure that we add it to our collection up front.
        $classInspector = $this->classInspectorProvider->getClassInspector($className);

        // If interface, attempt to resolve interface via annotations
        $newClass = $className;
        if ($classInspector->isInterface() || $classInspector->isAbstract()) {
            $newClass = $this->interfaceResolver->getCurrentlyConfiguredImplementationClass($newClass);
            $classInspector = $this->classInspectorProvider->getClassInspector($newClass);
        }

        $originalClassInspector = $classInspector;


        // Sort out parameters
        $params = array();
        if (!$createNew) {
            if ($constructor = $classInspector->getConstructor()) {
                foreach ($constructor->getParameters() as $param) {

                    if (trim($param->getType(), "[]") == $param->getType() && !in_array($param->getType(), Primitive::TYPES)) {
                        if (in_array($param->getType(), $dependentClasses))
                            throw new RecursiveDependencyException($param->getType());

                        $params[$param->getName()] = $this->__doGet($param->getType(), $createNew, $allowProxy, $dependentClasses);
                    }
                }
            }
        }


        // Create a proxy class provided the noProxy annotation is not set and there is not already a proxy applied
        $proxy = false;
        if ($allowProxy && !isset($classInspector->getClassAnnotations()["noProxy"]) && !isset($classInspector->getPublicMethods()["__call"])) {
            $proxy = true;
            $newClass = $this->proxyGenerator->generateProxy($newClass, "Proxy", [Proxy::class]);
            $classInspector = $this->classInspectorProvider->getClassInspector($newClass);
        }


        // Create the proxy object
        $instance = $classInspector->createInstance($params);

        // Populate with base functionality if a proxy.
        if ($proxy)
            $instance->__populate($this->interceptors, $originalClassInspector);

        // Store for future efficiency if not create new
        if (!$createNew)
            $this->instances[$className] = $instance;

        return $instance;
    }


}
