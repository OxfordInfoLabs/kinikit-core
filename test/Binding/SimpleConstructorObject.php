<?php


namespace Kinikit\Core\Binding;


class SimpleConstructorObject {

    public $name;
    public $age;
    public $dob;

    /**
     * SimpleConstructorObject constructor.
     * @param $name
     * @param $age
     * @param $dob
     */
    public function __construct($name, $age, $dob) {
        $this->name = $name;
        $this->age = $age;
        $this->dob = $dob;
    }


}
