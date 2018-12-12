<?php

namespace Kinikit\Core\Object;

class MixedDynamicSerialisable extends DynamicSerialisableObject {

    private $name;
    protected $address;
    protected $telephone;

    public function __construct($name, $address, $telephone, $strict) {
        parent::__construct($strict);
        $this->name = $name;
        $this->address = $address;
        $this->telephone = $telephone;
    }

    public function getName() {
        return $this->name;
    }

    protected function getAddress() {
        return $this->address;
    }

    protected function getTelephone() {
        return $this->telephone;
    }

    public function setTelephone($telephone) {
        $this->telephone = $telephone;
    }

}

?>