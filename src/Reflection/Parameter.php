<?php


namespace Kinikit\Core\Reflection;


use Kinikit\Core\Util\Primitive;

class Parameter {


    /**
     * The reflection parameter object
     *
     * @var \ReflectionParameter
     */
    private $reflectionParameter;


    /**
     * The method inspector for the method which this parameter is called upon.
     *
     * @var Method
     */
    private $method;


    /**
     * The type for this parameter
     *
     * @var string
     */
    private $type;


    /**
     * An indicator as to whether or not this parameter is explicitly typed.
     *
     * @var bool
     */
    private $explicitlyTyped;


    /**
     * Construct with the reflection parameter and the ownning method inspector.
     *
     * Parameter constructor.
     * @param \ReflectionParameter $reflectionParameter
     * @param Method $method
     */
    public function __construct($reflectionParameter, $method) {
        $this->reflectionParameter = $reflectionParameter;
        $this->method = $method;

        $declaredNamespaceClasses = $method->getDeclaringClassInspector()->getDeclaredNamespaceClasses();

        // Evaluate the parameter type according to whether or not this is an explicitly typed param or annotated.
        $type = "mixed";
        $nullablePrefix = "";
        $arraySuffix = "";

        $this->explicitlyTyped = false;
        if ($reflectionParameter->getType()) {

            if ($reflectionParameter->getType() instanceof \ReflectionNamedType) {
                list($type, $arraySuffix) = $this->stripArrayTypeSuffix($reflectionParameter->getType()->getName());

                if (!Primitive::isStringPrimitiveType($type))
                    $type = "\\" . ltrim(trim($type), "\\");
            } else {
                list($type, $arraySuffix) = $this->stripArrayTypeSuffix($reflectionParameter->getType());
            }
            $this->explicitlyTyped = true;
        } else {

            $methodAnnotations = isset($method->getMethodAnnotations()["param"]) ? $method->getMethodAnnotations()["param"] : [];

            foreach ($methodAnnotations as $annotation) {
                if (preg_match("/.+?\\$" . $reflectionParameter->getName() . "($|\\[| )/", $annotation->getValue())) {

                    // Knock off the parameter name and use the first word to derive the type
                    $type = trim(str_replace('$' . $reflectionParameter->getName(), "", $annotation->getValue()));
                    $type = explode(" ", $type)[0];

                    list($type, $arraySuffix) = $this->stripArrayTypeSuffix($type);

                    if (!Primitive::isStringPrimitiveType($type)) {
                        if (isset($declaredNamespaceClasses[$type]))
                            $type = $declaredNamespaceClasses[$type];
                        else {
                            if (substr($type, 0, 1) != "\\") {
                                $type = "\\" . $method->getReflectionMethod()->getDeclaringClass()->getNamespaceName() . "\\" . $type;
                            }
                        }

                    }
                    break;
                }
            }
        }

        if ($this->explicitlyTyped && $reflectionParameter->allowsNull()){
            $nullablePrefix = "?";
        }

        $this->type = $nullablePrefix . $type . $arraySuffix;

    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->reflectionParameter->getName();
    }

    /**
     * @return mixed
     */
    public function getType() {
        return $this->type;
    }

    public function isNullable() {
        return str_starts_with(trim($this->type), "?");
    }

    /**
     * Is this parameter an array type
     */
    public function isArray() {
        return preg_match("/\[.*\]$/", $this->type) || str_contains($this->type, "array");
//        return $this->type != $this->stripArrayTypeSuffix($this->type);
    }

    /**
     * @return mixed
     */
    public function isRequired() {
        return (!$this->reflectionParameter->isVariadic()) && ((!$this->reflectionParameter->isOptional()) || (!$this->reflectionParameter->isDefaultValueAvailable()));
    }

    /**
     * @return mixed
     */
    public function getDefaultValue() {
        return $this->isRequired() ? null : ($this->reflectionParameter->isDefaultValueAvailable() ? $this->reflectionParameter->getDefaultValue() : null);
    }


    /**
     * @return bool
     */
    public function isVariadic() {
        return $this->reflectionParameter->isVariadic();
    }


    /**
     * @return bool
     */
    public function isPassedByReference() {
        return $this->reflectionParameter->isPassedByReference();
    }

    /**
     * @return bool
     */
    public function isExplicitlyTyped(): bool {
        return $this->explicitlyTyped;
    }


    /**
     * @return bool
     */
    public function isPrimitive() {
        $type = trim($this->getType(), "?");
        return in_array($type, Primitive::TYPES);
    }


    // Strip Array type suffix
    // If "string[int]" is the input, output is ["string", "[int]"]
    private function stripArrayTypeSuffix($type) {
        $strippedType = trim(preg_replace("/\[.*\]$/", "", $type));
        $arraySuffix = substr($type, strlen($strippedType));
        return array($strippedType, $arraySuffix);
    }


}
