<?php

namespace Kinikit\Core\DependencyInjection;


/**
 * Object Interceptor base class for intercepting object creations and method calls on the proxy object.
 */
class ObjectInterceptor {


    /**
     * Intercept the creation of this object.  This should either do nothing or throw an exception to
     * veto this create.  This is particularly useful when we want whole objects to be permission restricted.
     *
     * @param $objectInstance
     */
    public function afterCreate($objectInstance) {
    }


    /**
     * Method level interceptor for objects.  This is called before every method
     * is invoked to allow vetoing for e.g. permission issues.
     *
     * This should throw a suitable exception or simply return if no issues.
     *
     * @param object $objectInstance - The object being called
     * @param string $methodName - The method name
     * @param string[string] $params - The parameters passed to the method as name => value pairs.
     * @param \Kinikit\Core\Util\Annotation\ClassAnnotations $classAnnotations - The class annotations for convenience for e.g. role based enforcement.
     *
     * @return string[string] - The params array either intact or modified if required.
     */
    public function beforeMethod($objectInstance, $methodName, $params, $classAnnotations) {
        return $params;
    }


    /**
     * Intercept the method callable just before it is executed and return another callable if required.
     *
     * @param callable $callable
     * @param \Kinikit\Core\Util\Annotation\ClassAnnotations $classAnnotations
     *
     * @return callable
     */
    public function methodCallable($callable, $methodName, $params, $classAnnotations) {
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
     * @param $classAnnotations - The class annotations for the controller class for convenience.
     *
     * @return $string - The return value, modified if required.
     *
     */
    public function afterMethod($objectInstance, $methodName, $params, $returnValue, $classAnnotations) {
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
     * @param $classAnnotations - The class annotations for the controller class for convenience.
     */
    public function onException($objectInstance, $methodName, $params, $exception, $classAnnotations) {

    }

}