<?php


namespace Kinikit\Core\Util;


class PublicGetterObject {

    private $name;
    private $address;
    private $telephone;

    public function __construct($name = null, $address = null, $telephone = null) {
        $this->name = $name;
        $this->address = $address;
        $this->telephone = $telephone;
    }

    /**
     * @return the $name
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return the $address
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * @return the $telephone
     */
    public function getTelephone() {
        return $this->telephone;
    }

    /**
     * @param $name the $name to set
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @param $address the $address to set
     */
    public function setAddress($address) {
        $this->address = $address;
    }

    /**
     * @param $telephone the $telephone to set
     */
    public function setTelephone($telephone) {
        $this->telephone = $telephone;
    }
    

}
