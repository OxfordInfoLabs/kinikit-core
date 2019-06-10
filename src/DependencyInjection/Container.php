<?php

namespace Kinikit\Core\DependencyInjection;

use Kinikit\Core\Util\Annotation\ClassAnnotationParser;
use Kinikit\Core\Util\ArrayUtils;

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


    // Constructor
    public function __construct() {
        $this->methodInterceptors = new ObjectInterceptors();
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

        // Remove leading \'s.
        $className = ltrim($className, "\\");

        // Shortcut if we already have this instance.
        if (isset($this->proxies[$className])) {
            return $this->proxies[$className];
        }

        // Create a new proxy and ensure that we add it to our collection up front.
        $newProxy = new Proxy();
        $this->proxies[$className] = $newProxy;

        $reflectionClass = new \ReflectionClass($className);

        $classAnnotations = ClassAnnotationParser::instance()->parse($className);

        $params = array();
        if ($reflectionClass->getConstructor() && $reflectionClass->getConstructor()->getNumberOfRequiredParameters() > 0) {
            $constructorParams = $classAnnotations->getMethodAnnotationsForMatchingTag("param", "__construct");
            foreach ($constructorParams as $param) {
                $dependentClass = trim(preg_replace("/\\$[a-zA-Z0-9_]+/", "", $param->getValue()));
                $params[] = $this->get($dependentClass);
            }
        }

        $newObject = $reflectionClass->newInstanceArgs($params);



        $newProxy->__populate($newObject, $this->methodInterceptors, $classAnnotations);

        return $newProxy;

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



}
