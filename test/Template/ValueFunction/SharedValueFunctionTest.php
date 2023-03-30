<?php

namespace Kinikit\Core\Template\ValueFunction;

use PHPUnit\Framework\TestCase;

class SharedValueFunctionTest extends TestCase {

    public function testCanDoContainsForDifferentDataTypes() {

        $function = new SharedValueFunction();
        $this->assertTrue($function->doesFunctionApply("contains"));

        $this->assertEquals(true, $function->applyFunction("contains bob", ["dave", "bob"], null));
        $this->assertEquals(false, $function->applyFunction("contains steve", ["dave", "bob"], null));

        $this->assertEquals(true, $function->applyFunction("contains ll", "hello", null));
        $this->assertEquals(false, $function->applyFunction("contains bad", "goodbye", null));

    }

    public function testCanFindLengthOfDifferentDataTypes() {

        $function = new SharedValueFunction();
        $this->assertTrue($function->doesFunctionApply("length"));

        $this->assertEquals(4, $function->applyFunction("length", [1,2,3,4], null));
        $this->assertEquals(2, $function->applyFunction("length", ["dave", "bob"], null));

        $this->assertEquals(5, $function->applyFunction("length", "hello", null));
        $this->assertEquals(7, $function->applyFunction("length", "goodbye", null));

    }

    public function testCanDoConcatForDifferentDataTypes() {

        $function = new SharedValueFunction();
        $this->assertTrue($function->doesFunctionApply("concat"));

        $this->assertEquals("hellooooo", $function->applyFunction("concat oooo", "hello", null));
        $this->assertEquals("goodbyeeeee", $function->applyFunction("concat eeee", "goodbye", null));

    }

}