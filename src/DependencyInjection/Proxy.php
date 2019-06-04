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
     * @var \Kinikit\Core\DependencyInjection\MethodInterceptor[]
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
     * @param \Kinikit\Core\DependencyInjection\MethodInterceptor[] $interceptors
     * @param \Kinikit\Core\Util\Annotation\ClassAnnotations $classAnnotations
     */
    public function __populate($object, $interceptors, $classAnnotations) {
        $this->object = $object;
        $this->interceptors = $interceptors;
        $this->classAnnotations = $classAnnotations;

        $interceptorAnnotations = $classAnnotations->getClassAnnotationForMatchingTag("interceptor");

        if ($interceptorAnnotations) {
            foreach ($interceptorAnnotations as $interceptor) {
                $interceptorClass = $interceptor->getValue();
                $this->interceptors[] = new $interceptorClass();
            }
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

        // Evaluate before method interceptors.
        foreach ($this->interceptors as $interceptor) {
            $interceptor->beforeMethod($this->object, $name, $arguments, $this->classAnnotations);
        }

        // Make the main call, wrap in exception handling
        try {
            $returnValue = call_user_func_array(array($this->object, $name), $arguments);

            // Evaluate after method interceptors.
            foreach ($this->interceptors as $interceptor) {
                $interceptor->afterMethod($this->object, $name, $arguments, $returnValue, $this->classAnnotations);
            }

            return $returnValue;

        } catch (\Throwable $e) {
            foreach ($this->interceptors as $interceptor) {
                $interceptor->onException($this->object, $name, $arguments, $e, $this->classAnnotations);
            }

            throw ($e);
        }
    }


}
