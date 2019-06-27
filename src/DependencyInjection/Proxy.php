<?php

namespace Kinikit\Core\DependencyInjection;

use Kinikit\Core\Util\Reflection\ClassInspector;


/**
 * Wrapper proxy class - this is injected instead of the real object to avoid circular dependency issues
 * and to allow for pre and post method interceptors for method calls.
 *
 * Class Proxy
 */
trait Proxy {

    /**
     * @var ObjectInterceptor[]
     */
    private $interceptors;


    /**
     * @var ClassInspector
     */
    private $classInspector;


    /**
     * Internal function called by Container to populate with bits required.
     *
     * @param ObjectInterceptor[] $interceptors
     * @param ClassInspector $classInspector
     */
    public function __populate($interceptors, $classInspector) {
        $this->interceptors = $interceptors;
        $this->classInspector = $classInspector;

        $interceptorAnnotations = isset($classInspector->getClassAnnotations()["interceptor"]) ?
            $classInspector->getClassAnnotations()["interceptor"] : array();

        if ($interceptorAnnotations) {

            foreach ($interceptorAnnotations as $interceptor) {
                $interceptorClass = $interceptor->getValue();
                $this->interceptors->addInterceptor(new $interceptorClass());
            }
        }

        $interceptors = $this->interceptors->getInterceptors();
        foreach ($interceptors as $interceptor) {
            $interceptor->afterCreate($this, $classInspector);
        }
    }


    /**
     *
     *
     * /**
     * Catch every method call for this dependency and forward to the object invoking any interceptors
     * as defined.
     *
     * @param $name
     * @param $arguments
     *
     * @throws \Throwable
     */
    public function __call($name, $arguments) {

        $interceptors = $this->interceptors->getInterceptors();

        // If we have interceptors, calculate the parameters
        $params = array();

        $reflectionClass = new \ReflectionClass($this);
        $method = $reflectionClass->getMethod($name);
        if ($method) {
            $reflectionParams = $method->getParameters();
            foreach ($reflectionParams as $index => $param) {
                $params[$param->getName()] = isset($arguments[$index]) ? $arguments[$index] :
                    ($param->isOptional() ? $param->getDefaultValue() : null);
            }
        }


        // Evaluate before method interceptors - return input parameters
        foreach ($interceptors as $interceptor) {
            $params = $interceptor->beforeMethod($this, $name, $params, $this->classInspector);
        }

        // Make the main call, wrap in exception handling
        try {

            $callable = function () use ($name, $params, $reflectionClass) {
                return $reflectionClass->getParentClass()->getMethod($name)->invokeArgs($this, array_values($params));
            };

            // Evaluate after method interceptors.
            foreach ($interceptors as $interceptor) {
                $callable = $interceptor->methodCallable($callable, $name, $params, $this->classInspector);
            }

            // Actually call the callable and get the return value.
            $returnValue = $callable();


            // Evaluate after method interceptors, return a return value
            foreach ($interceptors as $interceptor) {
                $returnValue = $interceptor->afterMethod($this, $name, $params, $returnValue, $this->classInspector);
            }

            return $returnValue;

        } catch (\Throwable $e) {
            foreach ($interceptors as $interceptor) {
                $interceptor->onException($this, $name, $params, $e, $this->classInspector);
            }

            throw ($e);
        }
    }


}
