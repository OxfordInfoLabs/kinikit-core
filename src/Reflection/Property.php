<?php


namespace Kinikit\Core\Reflection;


use Kinikit\Core\Annotation\Annotation;
use Kinikit\Core\Exception\WrongPropertyTypeException;
use Kinikit\Core\Util\ArrayUtils;
use Kinikit\Core\Util\Primitive;

class Property {

    /**
     * @var \ReflectionProperty
     */
    private $reflectionProperty;

    /**
     * @var Annotation[][]
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
     * @param Annotation[][] $propertyAnnotations
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
        $nullablePrefix = "";
        if (sizeof($propertyAnnotations) > 0) {
            $annotation = $propertyAnnotations[0];

            //TODO Cover Union type case

            $type = trim($annotation->getValue());
            if (str_contains($type, "?")){
                $nullablePrefix = "?";
                $type = str_replace("?", "", $type);
            }

            [$type, $arraySuffix] = $this->stripArrayTypeSuffix($type);

            if (!Primitive::isStringPrimitiveType($type)) {
                if (isset($declaredNamespaceClasses[$type]))
                    $type = $declaredNamespaceClasses[$type];
                else {
                    if (substr($type, 0, 1) != "\\") {
                        $type = "\\" . $reflectionProperty->getDeclaringClass()->getNamespaceName() . "\\" . $type;
                    }
                }
            }
        } else if ($reflectionProperty->hasType()){
            $reflectionType = $reflectionProperty->getType();
            if ($reflectionType instanceof \ReflectionUnionType){
                $type = join("|", $reflectionType->getTypes());
//                throw new \Error("ReflectionUnionType found: " . print_r($reflectionType->getTypes(), true));
            } else {
                $type = $reflectionType->getName();
            }
        }

        $this->type = $nullablePrefix .  $type . $arraySuffix;

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
     * @return Annotation[][]
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
     * Get the raw type (ignoring nullability) for this property
     *
     * @return string
     */
    public function getType() {
        return str_replace("?", "", $this->type);
    }


    /**
     * Return a boolean indicating whether or not this is a primitive type.
     */
    public function isPrimitive() {
        return in_array($this->type, Primitive::TYPES);
    }

    public function isNullable(){
        return str_starts_with($this->type, "?");
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
        $rawType = $this->getType();

//        if (!$nullable && $value == null){
//            //TODO WARNING! Should require nullable properties to be explicitly labelled
//            //throw new WrongPropertyTypeException("Attempted to set {$this->getPropertyName()} on class {$this->getDeclaringClassInspector()->getClassName()} to null even though it's not nullable");
//        }


        $possibleTypes = explode("|", $rawType);
        $wrongTypeArray = array_map(fn($t) => $this->wrongType($t, $value), $possibleTypes);

        if (ArrayUtils::all($wrongTypeArray)) {
            $valType = gettype($value);
            throw new WrongPropertyTypeException("An attempt was made to write to the {$this->type} property {$this->getPropertyName()} on the class {$this->getDeclaringClassInspector()->getClassName()} with a value of the wrong type $valType.");
        }
        $this->reflectionProperty->setValue($object, $value);

    }

    private function wrongType(string $type, mixed $value){
        $type = trim($type);

        $wrongType = true;
        if ($value === null){ // Allow nullability for all types
            $wrongType = false;
        } else if (Primitive::isStringPrimitiveType($type) && Primitive::isOfPrimitiveType($type, $value)) {
            $wrongType = false; // Allows bools and ints as strings
        } else if (is_object($value)) { // If it's not an instance/subclass of the class
            $wrongType = !(get_class($value) == trim($type, "\\")
                || is_subclass_of($value, trim($type, "\\")));
        } else if (is_array($value) && ((strpos($type, "[") && strpos($type, "]")) || $type == "array")) { // If it's an array we are fine
            $wrongType = false;
        }
        return $wrongType;
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
