<?php

namespace Kinikit\Core\DependencyInjection;

use Kinikit\Core\Exception\InsufficientParametersException;
use Kinikit\Core\Reflection\ClassInspector;


/**
 * Wrapper proxy class - this is injected instead of the real object to avoid circular dependency issues
 * and to allow for pre and post method interceptors for method calls.
 *
 * Class Proxy
 */
trait Proxy {

    /**
     * @var ContainerInterceptors
     */
    private $interceptors;


    /**
     * @var ClassInspector
     */
    private $classInspector;

    // Undefined valud
    private $UNDEFINED_VALUE = "<<<<<UNDEFINED>>>>>";


    /**
     * Internal function called by Container to populate with bits required.
     *
     * @param ContainerInterceptors $containerInterceptors
     * @param ClassInspector $classInspector
     */
    public function __populate($containerInterceptors, $classInspector) {
        $this->classInspector = $classInspector;
        $this->interceptors = $containerInterceptors;


        // Add explicitly defined interceptors from annotations
        $interceptorAnnotations = isset($classInspector->getClassAnnotations()["interceptor"]) ?
            $classInspector->getClassAnnotations()["interceptor"] : array();

        if ($interceptorAnnotations) {

            foreach ($interceptorAnnotations as $interceptor) {
                $interceptorClass = $interceptor->getValue();
                $newInterceptor = Container::instance()->get($interceptorClass);
                $this->interceptors->addInterceptor($newInterceptor, [$classInspector->getClassName()]);
            }
        }

        // Call after create omn each interceptor
        foreach ($this->interceptors->getInterceptorsForClass($classInspector->getClassName()) as $interceptor) {
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


        // If we have interceptors, calculate the parameters
        $params = array();

        $reflectionClass = new \ReflectionClass($this);
        $method = $reflectionClass->getMethod($name);
        if ($method) {
            $reflectionParams = $method->getParameters();
            foreach ($reflectionParams as $index => $param) {
                $params[$param->getName()] = array_key_exists($index, $arguments) ? ($param->isPassedByReference() ? $arguments[$index] : $arguments[$index]) :
                    ($param->isOptional() ? $param->getDefaultValue() : $this->UNDEFINED_VALUE);
            }
        }


        $methodInspector = $this->classInspector->getPublicMethod($name);

        // Grab the class interceptors
        $classInterceptors = $this->interceptors->getInterceptorsForClass($this->classInspector->getClassName());

        // Evaluate before method interceptors - return input parameters
        foreach ($classInterceptors as $interceptor) {
            $params = $interceptor->beforeMethod($this, $name, $params, $methodInspector);
        }


        // Make the main call, wrap in exception handling
        try {

            $callable = function () use ($name, $params, $reflectionClass) {

                $reflectionMethod = $reflectionClass->getParentClass()->getMethod($name);

                $paramValues = array_values($params);

                $invocationParams = [];
                foreach ($reflectionMethod->getParameters() as $index => $parameter) {

                    if ($paramValues[$index] === $this->UNDEFINED_VALUE) {
                        throw new InsufficientParametersException("Insufficient parameters passed to method $name");
                    }

                    if ($parameter->isPassedByReference()) {
                        $invocationParams[] = &$paramValues[$index];
                    } else {
                        $invocationParams[] = $paramValues[$index];
                    }


                }


                return $reflectionMethod->invokeArgs($this, $invocationParams);
            };

            // Evaluate after method interceptors.
            foreach ($classInterceptors as $interceptor) {
                $callable = $interceptor->methodCallable($callable, $this, $name, $params, $methodInspector);
            }

            // Actually call the callable and get the return value.
            $returnValue = $callable();


            // Evaluate after method interceptors, return a return value
            foreach ($classInterceptors as $interceptor) {
                $returnValue = $interceptor->afterMethod($this, $name, $params, $returnValue, $methodInspector);
            }

            return $returnValue;

        } catch (\Throwable $e) {
            foreach ($classInterceptors as $interceptor) {
                $interceptor->onException($this, $name, $params, $e, $methodInspector);
            }

            throw ($e);
        }
    }


}
