<?php

namespace Kinikit\Core\DependencyInjection;

use Kinikit\Core\Annotation\ClassAnnotationParser;
use Kinikit\Core\Configuration\Configuration;
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
    private $interfaceMappings = array();


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


    // Constructor
    public function __construct() {
        $this->interceptors = new ContainerInterceptors();
        $this->proxyGenerator = new ProxyGenerator();
        $this->classInspectorProvider = new ClassInspectorProvider();

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
     *
     * @param ContainerInterceptor $interceptor
     */
    public function addInterceptor($interceptor) {
        $this->interceptors->addInterceptor($interceptor);
    }


    /**
     * Add an explicit interface mapping for a given interface to a concrete implementation.
     *
     * @param string $interfaceClassName
     * @param string $implementationClassName
     */
    public function addInterfaceMapping($interfaceClassName, $implementationClassName) {

        $interfaceClassName = "\\" . ltrim(trim($interfaceClassName), "\\");
        $implementationClassName = "\\" . ltrim(trim($implementationClassName), "\\");

        $this->interfaceMappings[$interfaceClassName] = $implementationClassName;
    }


    /**
     * @param $className
     * @return object
     * @throws \ReflectionException
     */
    public function __doGet($className, $dependentClasses = array()) {

        // Remove leading \'s.
        $className = "\\" . ltrim(trim($className), "\\");

        if (isset($this->interfaceMappings[$className])) {
            $className = $this->interfaceMappings[$className];
        }


        $dependentClasses[] = $className;

        // Shortcut if we already have this instance.
        if (isset($this->instances[$className])) {
            return $this->instances[$className];
        }

        // Create a new proxy and ensure that we add it to our collection up front.
        $classInspector = $this->classInspectorProvider->getClassInspector($className);


        // If interface, attempt to resolve interface via annotations
        $newClass = $className;
        if ($classInspector->isInterface()) {
            $newClass = $this->resolveInterface($classInspector);
            $classInspector = $this->classInspectorProvider->getClassInspector($newClass);
        }

        // Create a proxy class provided the noProxy annotation is not set.
        $proxy = false;
        if (!isset($classInspector->getClassAnnotations()["noProxy"])) {
            $proxy = true;
            $newClass = $this->proxyGenerator->generateProxy($newClass);
        }

        $params = array();
        if ($constructor = $classInspector->getConstructor()) {
            foreach ($constructor->getParameters() as $param) {
                if (!in_array($param->getType(), Primitive::TYPES)) {
                    if (in_array($param->getType(), $dependentClasses))
                        throw new RecursiveDependencyException($param->getType());

                    $params[] = $this->get($param->getType(), $dependentClasses);
                }
            }
        }

        // Create the proxy object
        $reflectionClass = new \ReflectionClass($newClass);
        $instance = $reflectionClass->newInstanceArgs($params);

        // Populate with base functionality if a proxy.
        if ($proxy)
            $instance->__populate($this->interceptors, $classInspector);

        // Store for future efficiency.
        $this->instances[$className] = $instance;

        return $instance;
    }


    // Resolve an interface to a concrete class or throw
    private function resolveInterface($classInspector) {

        $classAnnotations = $classInspector->getClassAnnotations();

        $className = null;

        if (isset($classAnnotations["implementationConfigParam"]) &&
            isset($classAnnotations["implementation"])) {
            $configParam = $classAnnotations["implementationConfigParam"][0]->getValue();
            $configValue = Configuration::readParameter($configParam);


            if ($configValue) {
                $implementations = $classAnnotations["implementation"];
                foreach ($implementations as $implementation) {
                    $explodedImp = explode(" ", trim($implementation->getValue()));
                    if ($explodedImp[0] == $configValue) {
                        return "\\" . ltrim(trim($explodedImp[1]), "\\");
                    }
                }

                // if no mapping found, simply return the value as explicit class mapping.
                return "\\" . ltrim(trim($configValue), "\\");

            }

        }

        if (isset($classAnnotations["defaultImplementation"])) {
            return "\\" . ltrim(trim($classAnnotations["defaultImplementation"][0]->getValue()), "\\");
        }

        throw new MissingInterfaceImplementationException($classInspector->getClassName());


    }


}
