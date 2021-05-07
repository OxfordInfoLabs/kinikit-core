<?php


namespace Kinikit\Core\Reflection;


use Kiniauth\Objects\Security\UserRole;
use Kinikit\Core\Annotation\Annotation;
use Kinikit\Core\Exception\InsufficientParametersException;
use Kinikit\Core\Exception\WrongParametersException;
use Kinikit\Core\Util\Primitive;

/**
 * Method inspector.
 *
 * Class MethodInspector
 * @package Kinikit\Core\Util\Reflection
 */
class Method {

    /**
     * @var \ReflectionMethod
     */
    private $reflectionMethod;

    /**
     * @var Annotation[]
     */
    private $methodAnnotations;


    /**
     * @var ClassInspector
     */
    private $declaringClassInspector;


    /**
     * @var Parameter[]
     */
    private $parameters = array();


    /**
     * @var string
     */
    private $returnType;


    /**
     * Construct with a reflection method and method annotations.
     *
     * MethodInspector constructor.
     *
     * @param \ReflectionMethod $reflectionMethod
     * @param Annotation[] $methodAnnotations
     * @param ClassInspector $declaringClassInspector
     */
    public function __construct($reflectionMethod, $methodAnnotations, $declaringClassInspector) {
        $this->reflectionMethod = $reflectionMethod;
        $this->methodAnnotations = $methodAnnotations;
        $this->declaringClassInspector = $declaringClassInspector;
    }

    /**
     * Get the class inspector for this method.
     *
     * @return ClassInspector
     */
    public function getDeclaringClassInspector() {
        return $this->declaringClassInspector;
    }


    /**
     * Get the method name for this method
     *
     * @return string
     */
    public function getMethodName() {
        return $this->reflectionMethod->getName();
    }


    /**
     * Get all method annotations for this method - indexed by annotation key and index
     *
     * @return Annotation[][]
     */
    public function getMethodAnnotations() {
        return $this->methodAnnotations;
    }

    /**
     * @return \ReflectionMethod
     */
    public function getReflectionMethod() {
        return $this->reflectionMethod;
    }


    /**
     * Return boolean indicating static
     *
     * @return boolean
     */
    public function isStatic() {
        return $this->reflectionMethod->isStatic();
    }


    /**
     * Return boolean indicating final
     *
     * @return bool
     */
    public function isFinal(){
        return $this->reflectionMethod->isFinal();
    }


    /**
     * Call this method on the supplied object using arguments which are
     * in key / value format for convenience.
     *
     * @param mixed $object
     * @param mixed[string] $arguments
     */
    public function call($object, $arguments, $allowMissingArgs = false) {

        $orderedArguments = $this->__processMethodArguments($arguments, $allowMissingArgs);
        return $this->reflectionMethod->invokeArgs($object, $orderedArguments);

    }


    /**
     * Get the parameters for this method
     *
     * @return Parameter[]
     */
    public function getParameters() {
        return array_values($this->getIndexedParameters());
    }


    /**
     * Get the parameters indexed by string key for this method.
     */
    public function getIndexedParameters() {
        if (!$this->parameters) {
            $this->parameters = array();

            foreach ($this->reflectionMethod->getParameters() as $parameter) {
                $this->parameters[$parameter->getName()] = new Parameter($parameter, $this);
            }

        }

        return $this->parameters;
    }


    /**
     * Get the return type for this method
     *
     * @return ReturnType
     */
    public function getReturnType() {
        if (!isset($this->returnType)) {
            $this->returnType = new ReturnType($this);
        }

        return $this->returnType;
    }


    /**
     * Process arguments passed as key/value pairs and order into correct
     * order for invoking the method by reflection.
     *
     * @param mixed[string] $arguments
     */
    public function __processMethodArguments($arguments, $allowMissingArgs = false) {


        // Loop through each parameter
        $orderedArgs = [];
        $missingRequired = [];
        $wrongParams = [];
        foreach ($this->getParameters() as $parameter) {
            if (array_key_exists($parameter->getName(), $arguments)) {
                $parameterValue = $arguments[$parameter->getName()];

                // If a primitive and not of right type, throw now.
                if ($parameter->isPrimitive()) {
                    if ($arguments[$parameter->getName()] && !Primitive::isOfPrimitiveType($parameter->getType(), $parameterValue))
                        $wrongParams[] = $parameter->getName();
                } else if ($arguments[$parameter->getName()] && !is_array($parameterValue) && (!is_object($parameterValue)
                        || !(get_class($parameterValue) == trim($parameter->getType(), "\\")
                            || is_subclass_of($parameterValue, trim($parameter->getType(), "\\")))))
                    $wrongParams[] = $parameter->getName();


                $orderedArgs[] = $parameterValue;
            } else {

                if ($parameter->isRequired()) {
                    if ($parameter->isExplicitlyTyped() || !$allowMissingArgs)
                        $missingRequired[] = $parameter->getName();
                    else
                        $orderedArgs[] = $parameter->getDefaultValue();

                } else if (!$parameter->isExplicitlyTyped()) {
                    $orderedArgs[] = $parameter->getDefaultValue();
                }

            }
        }

        // If missing required, throw an exception
        if (sizeof($missingRequired)) {
            $joinedMissing = join(", ", $missingRequired);
            throw new InsufficientParametersException("Too few arguments were supplied to the method {$this->getMethodName()} on the class {$this->getDeclaringClassInspector()->getClassName()}.  Expected $joinedMissing");
        }

        if (sizeof($wrongParams)) {
            $joinedWrong = join(", ", $wrongParams);
            throw new WrongParametersException("The values for the fields $joinedWrong supplied to the method {$this->getMethodName()} on the class {$this->getDeclaringClassInspector()->getClassName()} are of the wrong type");
        }

        return $orderedArgs;


    }


}
