<?php


namespace Kinikit\Core\Reflection;


use Kinikit\Core\Util\Primitive;

class Parameter {


    private $name;
    private $type;
    private $required;
    private $defaultValue;
    private $explicitlyTyped;

    const NO_DEFAULT_VALUE = "!!!";

    /**
     * Construct with a name a type and a default value if one exists.
     *
     * Parameter constructor.
     * @param $name
     * @param $type
     * @param null $defaultValue
     */
    public function __construct($name, $type, $required = false, $defaultValue = self::NO_DEFAULT_VALUE, $explicitlyTyped = false) {
        $this->name = $name;
        $this->type = $type;
        $this->required = $required;
        $this->defaultValue = $defaultValue;
        $this->explicitlyTyped = $explicitlyTyped;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
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
    public function getRequired() {
        return $this->required;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue() {
        return $this->defaultValue;
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
        $type = trim(preg_replace("/\[.*\]/", "", $this->type));
        return in_array($type, Primitive::TYPES);
    }


}
