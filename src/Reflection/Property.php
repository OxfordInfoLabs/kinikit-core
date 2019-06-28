<?php


namespace Kinikit\Core\Reflection;


use Kinikit\Core\Annotation\Annotation;
use Kinikit\Core\Util\Primitive;

class Property {

    /**
     * @var \ReflectionProperty
     */
    private $reflectionProperty;

    /**
     * @var Annotation[]
     */
    private $propertyAnnotations;

    /**
     * @var ClassInspector
     */
    private $declaringClassInspector;


    /**
     * @var string
     */
    private $type;


    const VISIBILITY_PUBLIC = "public";
    const VISIBILITY_PROTECTED = "protected";
    const VISIBILITY_PRIVATE = "private";

    /**
     * Property constructor.
     * @param \ReflectionProperty $reflectionProperty
     * @param Annotation[] $propertyAnnotations
     * @param ClassInspector $declaringClassInspector
     */
    public function __construct($reflectionProperty, $propertyAnnotations, $declaringClassInspector) {
        $this->reflectionProperty = $reflectionProperty;
        $this->propertyAnnotations = $propertyAnnotations;
        $this->declaringClassInspector = $declaringClassInspector;


        $propertyAnnotations = isset($propertyAnnotations["var"]) ? $propertyAnnotations["var"] : [];

        $declaredNamespaceClasses = $declaringClassInspector->getDeclaredNamespaceClasses();

        $type = "mixed";
        if (sizeof($propertyAnnotations) > 0) {
            $annotation = $propertyAnnotations[0];

            $type = trim($annotation->getValue());

            list($type, $arraySuffix) = $this->stripArrayTypeSuffix($type);

            if (!in_array($type, Primitive::TYPES)) {
                if (isset($declaredNamespaceClasses[$type]))
                    $type = $declaredNamespaceClasses[$type];
                else {
                    if (substr($type, 0, 1) != "\\") {
                        $type = "\\" . $reflectionProperty->getDeclaringClass()->getNamespaceName() . "\\" . $type;
                    }
                }

            }

        }

        $this->type = $type;

    }


    /**
     * Get the property name
     *
     * @return string
     */
    public function getPropertyName() {
        return $this->reflectionProperty->getName();
    }

    /**
     * Get the reflection property
     *
     * @return \ReflectionProperty
     */
    public function getReflectionProperty() {
        return $this->reflectionProperty;
    }

    /**
     * Get the property annotations
     *
     * @return Annotation[]
     */
    public function getPropertyAnnotations() {
        return $this->propertyAnnotations;
    }

    /**
     * Get the declaring class inspector for this property.
     *
     * @return ClassInspector
     */
    public function getDeclaringClassInspector() {
        return $this->declaringClassInspector;
    }

    /**
     * Get the type for this property
     *
     * @return string
     */
    public function getType() {
        return $this->type;
    }


    /**
     * Get the visibility for this property.
     */
    public function getVisibility() {
        return $this->reflectionProperty->isPublic() ? self::VISIBILITY_PUBLIC : ($this->reflectionProperty->isProtected() ? self::VISIBILITY_PROTECTED : self::VISIBILITY_PRIVATE);
    }


    // Strip Array type suffix
    private function stripArrayTypeSuffix($type) {
        $strippedType = trim(preg_replace("/\[.*\]$/", "", $type));
        $arraySuffix = substr($type, strlen($strippedType));
        return array($strippedType, $arraySuffix);
    }


}
