<?php


namespace Kinikit\Core\Reflection;


use Kinikit\Core\Annotation\Annotation;
use Kinikit\Core\Util\Primitive;

/**
 * Method inspector.
 *
 * Class MethodInspector
 * @package Kinikit\Core\Util\Reflection
 */
class MethodInspector {

    /**
     * @var \ReflectionMethod
     */
    private $reflectionMethod;

    /**
     * @var Annotation[]
     */
    private $methodAnnotations;


    /**
     * @var string
     */
    private $declaredNamespaceClasses;


    /**
     * @var Parameter[]
     */
    private $parameters;


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
     */
    public function __construct($reflectionMethod, $methodAnnotations, $declaredNamespaceClasses) {
        $this->reflectionMethod = $reflectionMethod;
        $this->methodAnnotations = $methodAnnotations;
        $this->declaredNamespaceClasses = $declaredNamespaceClasses;
    }

    /**
     * Get the declaring class name for this method.
     *
     * @return string
     */
    public function getDeclaringClassName() {
        return $this->reflectionMethod->getDeclaringClass()->getName();
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
     * Get the method annotations for this method - indexed by annotation key and index
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
     * Get the parameters for this method
     *
     * @return Parameter[]
     */
    public function getParameters() {

        if (!isset($this->parameters)) {
            $this->parameters = array();

            $methodAnnotations = isset($this->methodAnnotations["param"]) ? $this->methodAnnotations["param"] : [];

            foreach ($this->reflectionMethod->getParameters() as $parameter) {

                $type = "mixed";
                $arraySuffix = "";

                $explicitlyTyped = false;
                if ($parameter->getType()) {

                    if ($parameter->getType() instanceof \ReflectionNamedType) {
                        list($type, $arraySuffix) = $this->stripArrayTypeSuffix($parameter->getType()->getName());

                        if (!in_array($type, Primitive::TYPES))
                            $type = "\\" . ltrim(trim($type), "\\");
                    } else {
                        list($type, $arraySuffix) = $this->stripArrayTypeSuffix($parameter->getType());
                    }
                    $explicitlyTyped = true;
                } else {

                    foreach ($methodAnnotations as $annotation) {
                        if (strpos($annotation->getValue(), '$' . $parameter->getName())) {
                            $type = trim(str_replace('$' . $parameter->getName(), "", $annotation->getValue()));

                            list($type, $arraySuffix) = $this->stripArrayTypeSuffix($type);

                            if (!in_array($type, Primitive::TYPES)) {
                                if (isset($this->declaredNamespaceClasses[$type]))
                                    $type = $this->declaredNamespaceClasses[$type];
                                else {
                                    if (substr($type, 0, 1) != "\\") {
                                        $type = "\\" . $this->reflectionMethod->getDeclaringClass()->getNamespaceName() . "\\" . $type;
                                    }
                                }

                            }
                            break;
                        }
                    }
                }

                $defaultValue = $parameter->isOptional() ? $parameter->getDefaultValue() : Parameter::NO_DEFAULT_VALUE;

                $this->parameters[] = new Parameter($parameter->getName(), $type . $arraySuffix, !$parameter->isOptional(), $defaultValue, $explicitlyTyped);

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

            $type = null;
            $explicitlyTyped = false;
            if ($this->reflectionMethod->getReturnType()) {
                $type = $this->reflectionMethod->getReturnType();
                if ($type instanceof \ReflectionNamedType) {
                    $type = $type->getName();
                    if (!in_array($type, Primitive::TYPES))
                        $type = "\\" . ltrim($type, "\\");
                }
                $explicitlyTyped = true;
            } else {
                if (isset($this->methodAnnotations["return"])) {
                    $type = trim($this->methodAnnotations["return"][0]->getValue());
                    if (!in_array($type, Primitive::TYPES)) {
                        if (isset($this->declaredNamespaceClasses[$type]))
                            $type = $this->declaredNamespaceClasses[$type];
                        else
                            $type = "\\" . $this->reflectionMethod->getDeclaringClass()->getNamespaceName() . "\\" . $type;
                    }
                }
            }

            $this->returnType = $type ? new ReturnType($type, $explicitlyTyped) : null;

        }

        return $this->returnType;
    }


    // Strip Array type suffix
    private function stripArrayTypeSuffix($type) {
        $strippedType = trim(preg_replace("/\[.*\]$/", "", $type));
        $arraySuffix = substr($type, strlen($strippedType));
        return array($strippedType, $arraySuffix);
    }


}
