<?php

namespace Kinikit\Core\Testing;

use Kinikit\Core\Exception\NoneExistentMethodException;
use Kinikit\Core\Reflection\ClassInspector;
use Kinikit\Core\Util\Primitive;

trait MockObject {


    /**
     * The class inspector for the underlying class which this mock represents
     *
     * @var mixed
     */
    private $underlyingClassInspector;

    /**
     * Array of programmed return values
     *
     * @var array
     */
    private $returnValues = [];


    /**
     * Array of programmed exceptions
     *
     * @var array
     */
    private $exceptions = [];


    /**
     * History of method call arguments as an array by method.
     *
     * @var array
     */
    private $methodCallArguments = [];




    /**
     * Set the return value for a method, if matching args are supplied
     * this return value will only be returned if the args match otherwise
     * it will be returned for all invocations.
     *
     * Returns itself for convenient chaining of these methods.
     *
     * @param string $methodName
     * @param mixed $returnValue
     * @param array $matchingArgs
     *
     * @return MockObject
     */
    public function returnValue($methodName, $returnValue, $matchingArgs = null) {

        $this->ensureMethodExists($methodName);

        $this->setArrayMethodValue($methodName, $returnValue, $matchingArgs, $this->returnValues);
        return $this;

    }


    /**
     * Throw an exception when this method is invoked.  If matching args are supplied
     * this will only throw if the args match otherwise it will always be thrown.
     *
     * Returns itself for convenient chaining of these methods
     *
     * @param $methodName
     * @param \Exception $exception
     * @param array $matchingArgs
     *
     * @return MockObject
     */
    public function throwException($methodName, $exception, $matchingArgs = null) {

        $this->ensureMethodExists($methodName);

        $this->setArrayMethodValue($methodName, $exception, $matchingArgs, $this->exceptions);

        return $this;
    }


    /**
     * Get the method call history as an array of passed arguments for a given method
     *
     * @return mixed[][]
     */
    public function getMethodCallHistory($methodName) {
        return $this->methodCallArguments[$methodName] ?? [];
    }


    /**
     * Reset the method call history for a method name
     *
     * @param $methodName
     */
    public function resetMethodCallHistory($methodName) {
        $this->methodCallArguments[$methodName] = [];
    }

    /**
     * Return a boolean indicating whether a method was called (optionally with arguments)
     *
     * @param $methodName
     * @param $withArguments
     *
     * @return bool
     */
    public function methodWasCalled($methodName, $withArguments = null) {

        $methodCallHistory = $this->getMethodCallHistory($methodName);

        // Return false if never called
        if (sizeof($methodCallHistory) == 0) {
            return false;
        }

        // Return depending on arguments
        return is_array($withArguments) ? array_search($withArguments, $methodCallHistory) !== false : true;


    }

    /**
     * Process behaviour for the passed method
     *
     * @param $methodName
     * @param $arguments
     */
    public function __call($methodName, $arguments) {

        // Check method exists
        $this->ensureMethodExists($methodName);

        // Add calling arguments to history
        if (!isset($this->methodCallArguments[$methodName]))
            $this->methodCallArguments[$methodName] = [];

        $this->methodCallArguments[$methodName][] = $arguments;

        if ($exception = $this->getArrayMethodValue($methodName, $arguments, $this->exceptions)) {
            throw $exception;
        } else {
            return $this->getArrayMethodValue($methodName, $arguments, $this->returnValues);
        }


    }

    // Ensure method exists
    private function ensureMethodExists($methodName) {
        if ($this->underlyingClassInspector &&
            !($this->underlyingClassInspector->getPublicMethods()[$methodName] ?? null)
            && !($this->underlyingClassInspector->getPublicMethods()["__call"] ?? null)
        ) {
            throw new NoneExistentMethodException($this->underlyingClassInspector->getClassName(), $methodName);
        }
    }


    // Set an array method value.
    private function setArrayMethodValue($methodName, $returnValue, $matchingArgs, &$array) {

        if ($matchingArgs === null) {
            $matchingArgs = ["!!!!!"];
        } else if (!is_array($matchingArgs)) {
            $matchingArgs = [$matchingArgs];
        }


        if (!isset($array[$methodName])) {
            $array[$methodName] = [];
        }

        foreach ($array[$methodName] as $index => list($args, $currentReturnValue)) {

            $matches = sizeof($args) == sizeof($matchingArgs);


            if ($matches) {
                foreach ($args as $argIndex => $arg) {
                    $matches = $matches && Primitive::isPrimitive($arg) == Primitive::isPrimitive($matchingArgs[$argIndex]);
                    $matches = $matches && $arg == $matchingArgs[$argIndex];
                }
            }


            if ($matches) {
                $array[$methodName][$index][1] = $returnValue;
                return;
            }
        }

        $array[$methodName][] = [$matchingArgs, $returnValue];

    }


    // Get an array method value
    private function getArrayMethodValue($methodName, $matchingArgs, $array) {

        if (isset($array[$methodName])) {

            $catchAll = null;
            foreach ($array[$methodName] as list($args, $returnValue)) {

                $matches = sizeof($args) == sizeof($matchingArgs);

                if ($matches) {
                    foreach ($args as $index => $arg) {
                        $matches = $matches && Primitive::isPrimitive($arg) == Primitive::isPrimitive($matchingArgs[$index]);
                        $matches = $matches && $arg == $matchingArgs[$index];
                    }
                }


                if ($matches)
                    return $returnValue;


                if ($args == ["!!!!!"]) {
                    $catchAll = $returnValue;
                }

            }

            return $catchAll;


        }

        return null;

    }

}
