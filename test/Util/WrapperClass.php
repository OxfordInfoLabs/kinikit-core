<?php
/**
 * Created by PhpStorm.
 * User: markrobertshaw
 * Date: 24/09/2018
 * Time: 17:51
 */

namespace Kinikit\Core\Util;


use Kinikit\Core\Object\SerialisableObject;

class WrapperClass extends SerialisableObject {

    private $id;
    private $name;

    /**
     * @var \Kinikit\Core\Util\NestedClass
     */
    private $address;


    /**
     * @var \Kinikit\Core\Util\NestedClass[string]
     */
    private $nestedClassesByKey;


    /**
     * @var \Kinikit\Core\Util\NestedClass[string][]
     */
    private $nestedClassesByMultipleKey;

    /**
     * WrapperClass constructor.
     *
     * @param $id
     * @param $name
     * @param \Kinikit\Core\Util\NestedClass $address
     */
    public function __construct($id = null, $name = null, $address = null, $nestedClassesByKey = null, $nestedClassesByMultipleKey = null) {
        $this->id = $id;
        $this->name = $name;
        $this->address = $address;
        $this->nestedClassesByKey = $nestedClassesByKey;
        $this->nestedClassesByMultipleKey = $nestedClassesByMultipleKey;
    }


    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id) {
        $this->id = $id;
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

    /**
     * @return NestedClass
     */
    public function getNestedClassesByKey() {
        return $this->nestedClassesByKey;
    }

    /**
     * @param NestedClass $nestedClassesByKey
     */
    public function setNestedClassesByKey($nestedClassesByKey) {
        $this->nestedClassesByKey = $nestedClassesByKey;
    }

    /**
     * @return NestedClass
     */
    public function getNestedClassesByMultipleKey() {
        return $this->nestedClassesByMultipleKey;
    }

    /**
     * @param NestedClass $nestedClassesByMultipleKey
     */
    public function setNestedClassesByMultipleKey($nestedClassesByMultipleKey) {
        $this->nestedClassesByMultipleKey = $nestedClassesByMultipleKey;
    }


}