<?php

namespace Kinikit\Core\Asynchronous;

use Kinikit\Core\DependencyInjection\Container;
use Kinikit\Core\Exception\NoneExistentClassException;
use Kinikit\Core\Exception\NoneExistentMethodException;
use Kinikit\Core\Logging\Logger;
use Kinikit\Core\Reflection\ClassInspector;

class AsynchronousClassMethod extends Asynchronous {


    /**
     * @var string
     */
    private $className;

    /**
     * @var string
     */
    private $methodName;


    /**
     * @var mixed[string]
     */
    private $parameters;

    /**
     * @var string[string]
     */
    private $parameterTypes = [];

    /**
     * @var string
     */
    private $returnValueType = null;

    /**
     * @param string $className
     * @param string $methodName
     *
     * @param mixed $parameters
     */
    public function __construct($className, $methodName, $parameters = []) {
        $this->className = $className;
        $this->methodName = $methodName;
        $this->parameters = $parameters;
        $this->processArguments();
    }

    /**
     * @return string
     */
    public function getClassName() {
        return $this->className;
    }

    /**
     * @return string
     */
    public function getMethodName() {
        return $this->methodName;
    }

    /**
     * @return mixed[string]
     */
    public function getParameters() {
        return $this->parameters;
    }

    /**
     * @return string[string]
     */
    public function getParameterTypes() {
        return $this->parameterTypes;
    }

    /**
     * @return string
     */
    public function getReturnValueType() {
        return $this->returnValueType;
    }


    /**
     * Run the method on the class obtained from the container.
     *
     * @return mixed
     */
    public function run() {

        // Get class instance
        $classInstance = Container::instance()->get($this->className);

        // Get an inspector and call the method
        $classInspector = new ClassInspector($this->className);

        // Get the method and call
        $method = $classInspector->getPublicMethod($this->methodName);
        return $method->call($classInstance, $this->parameters);

    }


    // Calculate parameter types
    private function processArguments() {

        try {
            $classInspector = new ClassInspector($this->className);
        } catch (\ReflectionException $e) {
            throw new NoneExistentClassException($this->className);
        }

        try {
            $method = $classInspector->getPublicMethod($this->methodName);

            // Process the method arguments
            $method->__processMethodArguments($this->parameters);

            $methodParams = $method->getIndexedParameters();
            foreach ($methodParams as $name => $methodParam) {
                $this->parameterTypes[$name] = ltrim($methodParam->getType(), '\\');
            }

            // Set the return type
            $this->returnValueType = $method->getReturnType()->getType();

        } catch (\ReflectionException $e) {
            throw new NoneExistentMethodException($this->className, $this->methodName);
        }


    }

}