<?php

namespace Kinikit\Core\DependencyInjection;

use PHPMailer\PHPMailer\Exception;

/**
 * Wrapper proxy class - this is injected instead of the real object to avoid circular dependency issues
 * and to allow for pre and post method interceptors for method calls.
 *
 * Class Proxy
 */
class Proxy {

    private $object;

    /**
     * @var \Kinikit\Core\DependencyInjection\ObjectInterceptors
     */
    private $interceptors;

    /**
     * @var \Kinikit\Core\Util\Annotation\ClassAnnotations
     */
    private $classAnnotations;


    /**
     * Internal function called by Container to populate with bits required.
     *
     * @param $object
     * @param \Kinikit\Core\DependencyInjection\ObjectInterceptor[] $interceptors
     * @param \Kinikit\Core\Util\Annotation\ClassAnnotations $classAnnotations
     */
    public function __populate($object, $interceptors, $classAnnotations) {
        $this->object = $object;
        $this->interceptors = $interceptors;
        $this->classAnnotations = $classAnnotations;

//        $interceptorAnnotations = $classAnnotations->getClassAnnotationForMatchingTag("interceptor");
//
//        if ($interceptorAnnotations) {
//            foreach ($interceptorAnnotations as $interceptor) {
//                $interceptorClass = $interceptor->getValue();
//                $this->interceptors[] = new $interceptorClass();
//            }
//        }

        $interceptors = $this->interceptors->getInterceptors();
        foreach ($interceptors as $interceptor) {
            $interceptor->afterCreate($this->object);
        }
    }


    /**
     * Internal framwork use and for testing to get the dependency object.
     *
     * @return mixed
     */
    public function __getObject() {
        return $this->object;
    }


    /**
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

        $reflectionClass = new \ReflectionClass($this->object);
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
            $params = $interceptor->beforeMethod($this->object, $name, $params, $this->classAnnotations);
        }

        // Make the main call, wrap in exception handling
        try {

            $callable = function () use ($name, $params) {
                return call_user_func_array(array($this->object, $name), array_values($params));
            };

            // Evaluate after method interceptors.
            foreach ($interceptors as $interceptor) {
                $callable = $interceptor->methodCallable($callable, $name, $params, $this->classAnnotations);
            }

            // Actually call the callable and get the return value.
            $returnValue = $callable();

            // Evaluate after method interceptors, return a return value
            foreach ($interceptors as $interceptor) {
                $returnValue = $interceptor->afterMethod($this->object, $name, $params, $returnValue, $this->classAnnotations);
            }

            return $returnValue;

        } catch (\Throwable $e) {
            foreach ($interceptors as $interceptor) {
                $interceptor->onException($this->object, $name, $params, $e, $this->classAnnotations);
            }

            throw ($e);
        }
    }


}
