<?php

namespace Kinikit\Core\Serialisation\XML;
use Kinikit\Core\Object\SerialisableObject;

/**
 * Test object with all protected access fields and no setters or getters
 *
 */
class TestObject2 extends SerialisableObject {

    protected $street;
    protected $city;
    protected $county;
    protected $key;

    public function __construct($street = null, $city = null, $county = null, $key = null) {
        $this->street = $street;
        $this->city = $city;
        $this->county = $county;
        $this->key = $key;
    }

    public function toString() {
        return $street . "," . $city . "," . $county;
    }

}

?>
