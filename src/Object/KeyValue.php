<?php
/**
 * Created by PhpStorm.
 * User: markrobertshaw
 * Date: 22/10/2018
 * Time: 11:58
 */

namespace Kinikit\Core\Object;


/**
 * Convenient Key / Value object for use when these are required.
 *
 * Class KeyValue
 * @package Kinikit\Core\Object
 */
class KeyValue extends SerialisableObject {

    private $key;
    private $value;

    /**
     * Construct with data
     *
     * KeyValue constructor.
     * @param $key
     * @param $value
     */
    public function __construct($key = null, $value = null) {
        $this->key = $key;
        $this->value = $value;
    }


    /**
     * @return mixed
     */
    public function getKey() {
        return $this->key;
    }

    /**
     * @param mixed $key
     */
    public function setKey($key) {
        $this->key = $key;
    }

    /**
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value) {
        $this->value = $value;
    }


}