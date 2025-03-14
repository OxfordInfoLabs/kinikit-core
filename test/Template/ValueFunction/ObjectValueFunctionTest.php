<?php

namespace Kinikit\Core\Template\ValueFunction;

use PHPUnit\Framework\TestCase;

include_once "autoloader.php";

class ObjectValueFunctionTest extends TestCase {

    public function testCanReturnSingleMember() {
        $function = new ObjectValueFunction();
        $this->assertTrue($function->doesFunctionApply("member"));

        $testArray = ["item1" => [1, 2, 3], "item2" => [4, 5, 6]];

        $this->assertEquals([1, 2, 3], $function->applyFunction("member 'item1'", $testArray, null));
        $this->assertEquals([4, 5, 6], $function->applyFunction("member 'item2'", $testArray, null));

    }

    public function testCanConvertObjectToKeyValueArray() {

        $function = new ObjectValueFunction();
        $this->assertTrue($function->doesFunctionApply("keyValueArray"));

        $testObject = ["fruit" => "Apple", "drink" => "Wine", "pet" => "Rabbit"];

        $this->assertEquals([
            ["key" => "fruit", "value" => "Apple"],
            [
                "key" => "drink", "value" => "Wine"
            ],
            [
                "key" => "pet", "value" => "Rabbit"
            ]
        ], $function->applyFunction("keyValueArray", $testObject, null));


        $this->assertEquals([
            ["property" => "fruit", "output" => "Apple"],
            [
                "property" => "drink", "output" => "Wine"
            ],
            [
                "property" => "pet", "output" => "Rabbit"
            ]
        ], $function->applyFunction("keyValueArray 'property' 'output'", $testObject, null));

    }

    public function testCanEvaluateObjectKeysCorrectly() {

        $function = new ObjectValueFunction();
        $this->assertTrue($function->doesFunctionApply("keys"));

        $this->assertEquals(["one", "two"], $function->applyFunction("keys", ["one" => "bob", "two" => "steve"], null));
        $this->assertEquals([0, 1, 2], $function->applyFunction("keys", ["first", "second", "third"], null));

    }

    public function testCanEvaluateObjectValuesCorrectly() {

        $function = new ObjectValueFunction();
        $this->assertTrue($function->doesFunctionApply("values"));

        $this->assertEquals(["bob", "steve"], $function->applyFunction("values", ["one" => "bob", "two" => "steve"], null));
        $this->assertEquals(["first", "second", "third"], $function->applyFunction("values", ["first", "second", "third"], null));

    }

    public function testCanCombineAnObjectCorrectly() {

        $function = new ObjectValueFunction();
        $this->assertTrue($function->doesFunctionApply("combine"));

        $this->assertEquals([1, 2, 3, 4, 5, 6], $function->applyFunction("combine array2 array3", [1, 2], ["array2" => [3, 4], "array3" => [5, 6]]));
    }

    public function testCanWrapObjectAsAnArray() {

        $function = new ObjectValueFunction();
        $this->assertTrue($function->doesFunctionApply("wrapAsArray"));

        $this->assertEquals([["dummy"]], $function->applyFunction("wrapAsArray", ["dummy"], null));

    }
}
