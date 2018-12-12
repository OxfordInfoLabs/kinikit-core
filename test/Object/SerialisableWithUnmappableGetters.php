<?php

namespace Kinikit\Core\Object;

class SerialisableWithUnmappableGetters extends SerialisableObject {

    private $id;
    private $name;
    private $age;
    private $shoeSize;

    public function __construct($id = null, $name = null, $age = null, $shoeSize = null) {
        $this->id = $id;
        $this->name = $name;
        $this->age = $age;
        $this->shoeSize = $shoeSize;
    }

    /**
     * @return the $id
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return the $name
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return the $age
     */
    public function getAge($showMeShowMe) {
        return $this->age;
    }

    /**
     * @return the $shoeSize
     */
    public function getShoeSize($testMe = null) {
        return $this->shoeSize;
    }

    /**
     * @param $id the $id to set
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @param $name the $name to set
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @param $age the $age to set
     */
    public function setAge($age) {
        $this->age = $age;
    }

    /**
     * @param $shoeSize the $shoeSize to set
     */
    public function setShoeSize($shoeSize) {
        $this->shoeSize = $shoeSize;
    }

}

?>