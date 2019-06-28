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
        $arraySuffix = "";

        $this->explicitlyTyped = false;
        if ($reflectionParameter->getType()) {

            if ($reflectionParameter->getType() instanceof \ReflectionNamedType) {
                list($type, $arraySuffix) = $this->stripArrayTypeSuffix($reflectionParameter->getType()->getName());

                if (!in_array($type, Primitive::TYPES))
                    $type = "\\" . ltrim(trim($type), "\\");
            } else {
                list($type, $arraySuffix) = $this->stripArrayTypeSuffix($reflectionParameter->getType());
            }
            $this->explicitlyTyped = true;
        } else {

            $methodAnnotations = isset($method->getMethodAnnotations()["param"]) ? $method->getMethodAnnotations()["param"] : [];

            foreach ($methodAnnotations as $annotation) {
                if (strpos($annotation->getValue(), '$' . $reflectionParameter->getName())) {
                    $type = trim(str_replace('$' . $reflectionParameter->getName(), "", $annotation->getValue()));

                    list($type, $arraySuffix) = $this->stripArrayTypeSuffix($type);

                    if (!in_array($type, Primitive::TYPES)) {
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

        $this->type = $type . $arraySuffix;

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

    /**
     * @return mixed
     */
    public function isRequired() {
        return !$this->reflectionParameter->isOptional();
    }

    /**
     * @return mixed
     */
    public function getDefaultValue() {
        return $this->isRequired() ? null : $this->reflectionParameter->getDefaultValue();
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
        return in_array($this->getType(), Primitive::TYPES);
    }


    // Strip Array type suffix
    private function stripArrayTypeSuffix($type) {
        $strippedType = trim(preg_replace("/\[.*\]$/", "", $type));
        $arraySuffix = substr($type, strlen($strippedType));
        return array($strippedType, $arraySuffix);
    }


}
