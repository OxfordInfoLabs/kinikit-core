<?php

namespace Kinikit\Core\Util\Serialisation\XML;

// Test object with a none blank constructor.
class TestBadConstructorObject {

    private $requiredField;

    public function __construct($requiredField, $secondField) {
        $this->requiredField = $requiredField;
    }

}


?>