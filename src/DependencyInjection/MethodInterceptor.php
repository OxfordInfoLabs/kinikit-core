<?php

namespace Kinikit\Core\DependencyInjection;


/**
 * Method Interceptor base class for intercepting method calls on the proxy object.
 */
class MethodInterceptor {

    /**
     * Method level interceptor for objects.  This is called before every method
     * is invoked to allow vetoing for e.g. permission issues.
     *
     * This should throw a suitable exception or simply return if no issues.
     *
     * @param object $objectInstance - The object being called
     * @param string $methodName - The method name
     * @param array $params - The parameters passed to the method
     * @param \Kinikit\Core\Util\Annotation\ClassAnnotations $classAnnotations - The class annotations for convenience for e.g. role based enforcement.
     *
     */
    public function beforeMethod($objectInstance, $methodName, $params, $classAnnotations) {

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
     */
    public function afterMethod($objectInstance, $methodName, $params, $returnValue, $classAnnotations) {

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
