<?php


namespace Kinikit\Core\Reflection;


use Kinikit\Core\Annotation\Annotation;
use Kinikit\Core\Exception\WrongPropertyTypeException;
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
        $arraySuffix = "";
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

        $this->type = $type . $arraySuffix;

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
     * Return a boolean indicating whether or not this is a primitive type.
     */
    public function isPrimitive() {
        return in_array($this->type, Primitive::TYPES);
    }


    /**
     * Return a static indicator
     */
    public function isStatic() {
        return $this->reflectionProperty->isStatic();
    }

    /**
     * Get the visibility for this property.
     */
    public function getVisibility() {
        return $this->reflectionProperty->isPublic() ? self::VISIBILITY_PUBLIC : ($this->reflectionProperty->isProtected() ? self::VISIBILITY_PROTECTED : self::VISIBILITY_PRIVATE);
    }


    /**
     * Set a property value on an object
     *
     * @param $object
     * @param mixed $value
     */
    public function set($object, $value) {

        // Ensure the reflection property is accessible.
        $this->reflectionProperty->setAccessible(true);


        // If a primitive and not of right type, throw now.
        $wrongType = false;
        if ($this->isPrimitive()) {
            if (!Primitive::isOfPrimitiveType($this->getType(), $value) && $value !== null)
                $wrongType = true;
        } else if (is_object($value)) {
            $wrongType = !(get_class($value) == trim($this->getType(), "\\")
                || is_subclass_of($value, trim($this->getType(), "\\")));
        } else if (!is_array($value) && $value !== null) {
            $wrongType = true;
        }

        if ($wrongType)
            throw new WrongPropertyTypeException("An attempt was made to write to the property {$this->getPropertyName()} on the class {$this->getDeclaringClassInspector()->getClassName()} with a value of the wrong type.");

        $this->reflectionProperty->setValue($object, $value);

    }


    /**
     * Get a property value for an object
     *
     * @param $object
     */
    public function get($object) {
        $this->reflectionProperty->setAccessible(true);
        return $this->reflectionProperty->getValue($object);
    }


    // Strip Array type suffix
    private function stripArrayTypeSuffix($type) {
        $strippedType = trim(preg_replace("/\[.*\]$/", "", $type));
        $arraySuffix = substr($type, strlen($strippedType));
        return array($strippedType, $arraySuffix);
    }


}
