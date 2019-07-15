<?php

namespace Kinikit\Core\DependencyInjection;


use Kinikit\Core\Reflection\Method;
use Kinikit\Core\Reflection\ClassInspector;

/**
 * Object Interceptor base class for intercepting object creations and method calls on the proxy object.
 */
class ContainerInterceptor {


    /**
     * Intercept the creation of this object.  This should either do nothing or throw an exception to
     * veto this create.  This is particularly useful when we want whole objects to be permission restricted.
     *
     * @param $objectInstance
     * @param ClassInspector $classInspector
     */
    public function afterCreate($objectInstance, $classInspector) {
    }


    /**
     * Method level interceptor for objects.  This is called before every method
     * is invoked to allow vetoing for e.g. permission issues.
     *
     * This should throw a suitable exception or simply return if no issues.
     *
     * @param object $objectInstance - The object being called
     * @param string $methodName - The method name
     * @param mixed[string] $params - The parameters passed to the method as name => value pairs.
     * @param Method $methodInspector - The class inspector instance for this class
     *
     * @return string[string] - The params array either intact or modified if required.
     */
    public function beforeMethod($objectInstance, $methodName, $params, $methodInspector) {
        return $params;
    }


    /**
     * Intercept the method callable just before it is executed and return another callable if required.
     *
     * @param callable $callable - The callable function being called
     * @param string $methodName - The method namne
     * @param mixed[string] $params - The parameters passed to the method as name => value pairs.
     * @param Method $methodInspector - The reflection method object for this method.
     *
     * @return callable
     */
    public function methodCallable($callable, $methodName, $params, $methodInspector) {
        return $callable;
    }


    /**
     * After method interceptor for objects.  This is called after every method
     * is invoked.  Useful for logging etc or final checking based upon results of method.
     *
     * This should throw a suitable exception or simply return if no issues.
     *
     * @param $object The object instance being called
     * @param $methodName - The method name being called.
     * @param $params - The input params to the method
     * @param $returnValue - The return value from the method
     * @param Method $methodInspector - The reflection method object for this method.
     *
     * @return $string - The return value, modified if required.
     *
     */
    public function afterMethod($objectInstance, $methodName, $params, $returnValue, $methodInspector) {
        return $returnValue;
    }


    /**
     * Exception interceptor.  This is called when an exception is raised in an object method.
     * Useful for logging etc.
     *
     * This doesn't return a value but throws the exception back to the client after completion.
     *
     * @param $objectInstance - The object instance being called.
     * @param $methodName - The method being called
     * @param $params - The input params to the method.
     * @param \Throwable $exception - The exception object thrown
     * @param Method $methodInspector - The reflection method object for this method.
     */
    public function onException($objectInstance, $methodName, $params, $exception, $methodInspector) {

    }

}
