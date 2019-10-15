<?php

namespace Kinikit\Core\DependencyInjection;

use Kinikit\Core\Reflection\ClassInspector;


/**
 * Wrapper proxy class - this is injected instead of the real object to avoid circular dependency issues
 * and to allow for pre and post method interceptors for method calls.
 *
 * Class Proxy
 */
trait Proxy {

    /**
     * @var ContainerInterceptor[]
     */
    private $interceptors;


    /**
     * @var ClassInspector
     */
    private $classInspector;


    /**
     * Internal function called by Container to populate with bits required.
     *
     * @param ContainerInterceptor[] $interceptors
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
                $params[$param->getName()] = isset($arguments[$index]) ? ($param->isPassedByReference() ? $arguments[$index] : $arguments[$index]) :
                    ($param->isOptional() ? $param->getDefaultValue() : null);
            }
        }


        $methodInspector = $this->classInspector->getPublicMethod($name);


        // Evaluate before method interceptors - return input parameters
        foreach ($interceptors as $interceptor) {
            $params = $interceptor->beforeMethod($this, $name, $params, $methodInspector);
        }

        // Make the main call, wrap in exception handling
        try {

            $callable = function () use ($name, $params, $reflectionClass) {

                $reflectionMethod = $reflectionClass->getParentClass()->getMethod($name);

                $paramValues = array_values($params);
                $invocationParams = [];
                foreach ($reflectionMethod->getParameters() as $index => $parameter) {
                    if ($parameter->isPassedByReference()) {
                        $invocationParams[] = &$paramValues[$index];
                    } else {
                        $invocationParams[] = $paramValues[$index];
                    }
                }

                return $reflectionMethod->invokeArgs($this, $invocationParams);
            };

            // Evaluate after method interceptors.
            foreach ($interceptors as $interceptor) {
                $callable = $interceptor->methodCallable($callable, $name, $params, $methodInspector);
            }

            // Actually call the callable and get the return value.
            $returnValue = $callable();


            // Evaluate after method interceptors, return a return value
            foreach ($interceptors as $interceptor) {
                $returnValue = $interceptor->afterMethod($this, $name, $params, $returnValue, $methodInspector);
            }

            return $returnValue;

        } catch (\Throwable $e) {
            foreach ($interceptors as $interceptor) {
                $interceptor->onException($this, $name, $params, $e, $methodInspector);
            }

            throw ($e);
        }
    }


}
