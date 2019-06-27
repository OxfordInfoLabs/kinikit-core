<?php

namespace Kinikit\Core\DependencyInjection;

use Kinikit\Core\Exception\RecursiveDependencyException;
use Kinikit\Core\Annotation\ClassAnnotationParser;
use Kinikit\Core\Util\ArrayUtils;
use Kinikit\Core\Util\Primitive;
use Kinikit\Core\Reflection\ClassInspector;
use Kinikit\Core\Reflection\MethodInspector;
use Kinikit\Core\Reflection\Parameter;

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
     * @var \Kinikit\Core\DependencyInjection\Proxy[string]
     */
    private $proxies = array();


    /**
     * Global interceptors
     *
     * @var \Kinikit\Core\DependencyInjection\ObjectInterceptors
     */
    private $methodInterceptors;

    /**
     * Proxy generator
     *
     * @var ProxyGenerator
     */
    private $proxyGenerator;


    // Constructor
    public function __construct() {
        $this->methodInterceptors = new ObjectInterceptors();
        $this->proxyGenerator = new ProxyGenerator();
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
     * @return ObjectInterceptors
     */
    public function getMethodInterceptors() {
        return $this->methodInterceptors;
    }

    /**
     * @param ObjectInterceptors $methodInterceptors
     */
    public function setMethodInterceptors($methodInterceptors) {
        $this->methodInterceptors = $methodInterceptors;
    }


    /**
     * Add a method interceptor to the list defined for this container.
     *
     * @param $methodInterceptor
     */
    public function addMethodInterceptor($methodInterceptor) {
        $this->methodInterceptors->addInterceptor($methodInterceptor);
    }

    /**
     * @param $className
     * @return object
     * @throws \ReflectionException
     */
    public function __doGet($className, $dependentClasses = array()) {

        // Remove leading \'s.
        $className = "\\" . ltrim(trim($className), "\\");

        $dependentClasses[] = $className;

        // Shortcut if we already have this instance.
        if (isset($this->proxies[$className])) {
            return $this->proxies[$className];
        }

        // Create a new proxy and ensure that we add it to our collection up front.
        $classInspector = new ClassInspector($className);

        // Create a proxy class
        $proxyClass = $this->proxyGenerator->generateProxy($className);

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
        $reflectionClass = new \ReflectionClass($proxyClass);
        $proxy = $reflectionClass->newInstanceArgs($params);

        // Populate with base functionality.
        $proxy->__populate($this->methodInterceptors, $classInspector);

        // Store for future efficiency.
        $this->proxies[$className] = $proxy;

        return $proxy;
    }


}
