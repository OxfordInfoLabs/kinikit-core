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

    public function testCanReturnModel() {

        $function = new ObjectValueFunction();
        $this->assertTrue($function->doesFunctionApply("model"));

        $this->assertEquals(["bing" => "bong"], $function->applyFunction("model", 1, ["bing" => "bong"]));
        $this->assertEquals(["name" => "Gerald", "age" => 46], $function->applyFunction("model", 1, ["name" => "Gerald", "age" => 46]));

    }

    public function testCanSetMemberOnObject() {

        $function = new ObjectValueFunction();
        $this->assertTrue($function->doesFunctionApply("setMember"));

        // Try setting a sensible value on various initial objects
        $this->assertEquals(["name" => "Gerald"], $function->applyFunction("setMember 'name' 'Gerald'", [], null));
        $this->assertEquals(["name" => "Gerald", "age" => 0], $function->applyFunction("setMember 'age' 0", ["name" => "Gerald"], null));
        $this->assertEquals(["name" => "Gerald", "age" => 46], $function->applyFunction("setMember 'age' 46", ["name" => "Gerald"], null));
        $this->assertEquals(["name" => "Gerald", "age" => 46], $function->applyFunction("setMember 'age' 46 ", ["name" => "Gerald", "age" => 17], null));

        // Try setting a sensible value on various initial objects with ifNotNull flag
        $this->assertEquals(["name" => "Gerald"], $function->applyFunction("setMember 'name' 'Gerald' true", [], null));
        $this->assertEquals(["name" => "Gerald", "age" => 0], $function->applyFunction("setMember 'age' 0 true", ["name" => "Gerald"], null));
        $this->assertEquals(["name" => "Gerald", "age" => 46], $function->applyFunction("setMember 'age' 46 true", ["name" => "Gerald"], null));
        $this->assertEquals(["name" => "Gerald", "age" => 46], $function->applyFunction("setMember 'age' 46 true ", ["name" => "Gerald", "age" => 17], null));

        // Try setting some non-sensible values
        $this->assertEquals(["name" => "Gerald"], $function->applyFunction("setMember 'age' '' true", ["name" => "Gerald"], null));
        $this->assertEquals(["name" => "Gerald"], $function->applyFunction("setMember 'age' null true", ["name" => "Gerald"], null));
        $this->assertEquals(["name" => "Gerald"], $function->applyFunction("setMember 'age' bing true", ["name" => "Gerald"], ["bing" => ""]));

    }

    public function testCanUnsetMemberOnObject() {

        $function = new ObjectValueFunction();
        $this->assertTrue($function->doesFunctionApply("unsetMember"));

        $this->assertEquals([], $function->applyFunction("unsetMember 'name'", ["name" => "Gerald"], []));
        $this->assertEquals(["name" => "Gerald"], $function->applyFunction("unsetMember 'age'", ["name" => "Gerald"], []));
        $this->assertEquals(["age" => 46], $function->applyFunction("unsetMember 'name'", ["name" => "Gerald", "age" => 46], []));
        $this->assertEquals([], $function->applyFunction("unsetMember 'name' 'age'", ["name" => "Gerald", "age" => 46], []));

    }
}
