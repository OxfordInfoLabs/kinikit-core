<?php

namespace Kinikit\Core\Util;

include_once "autoloader.php";

class PrimitiveTest extends \PHPUnit\Framework\TestCase {
    public function testIsOfPrimitiveType(){
        $this->assertEquals(true, Primitive::isStringPrimitiveType("string"));
        $this->assertEquals(true, Primitive::isStringPrimitiveType("?int"));
        $this->assertEquals(false, Primitive::isStringPrimitiveType("?TestCase"));
    }

    public function testConvertToPrimitive(){
        $a = 24;
        $converted = Primitive::convertToPrimitive(Primitive::TYPE_STRING, $a);
        $this->assertEquals("string", gettype($converted));
    }
}