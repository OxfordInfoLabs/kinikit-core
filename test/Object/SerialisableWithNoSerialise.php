<?php

namespace Kinikit\Core\Object;


class SerialisableWithNoSerialise extends SerialisableObject {

    private $name;
    private $address;

    /**
     * @var
     * @no-serialise
     */
    protected $age;

    protected $startDate;


    /**
     * Construct with all availables
     *
     * @param null $name
     * @param null $address
     * @param null $age
     * @param null $startDate
     */
    public function __construct($name = null, $address = null, $age = null, $startDate = null) {
        $this->name = $name;
        $this->address = $address;
        $this->age = $age;
        $this->startDate = $startDate;
    }


    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return mixed
     * @no-serialise
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address) {
        $this->address = $address;
    }


}