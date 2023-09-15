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

    public function testResourceIsPrimitive(){
        $resource = fopen(__DIR__ . "/test.txt", "r");
        $this->assertTrue(Primitive::isPrimitive($resource));
        $this->assertTrue(Primitive::isStringPrimitiveType(gettype($resource)));
        $this->assertTrue(Primitive::isOfPrimitiveType(Primitive::TYPE_RESOURCE, $resource));
    }
}