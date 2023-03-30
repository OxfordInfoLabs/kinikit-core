<?php

namespace Kinikit\Core\Template\ValueFunction;

use PHPUnit\Framework\TestCase;

include_once "autoloader.php";

class ArrayValueFunctionTest extends TestCase {

    public function testCanGetMemberValueArrayFromArrayOfObjects() {

        $function = new ArrayValueFunction();
        $this->assertTrue($function->doesFunctionApply("memberValues"));


        $array = [
            [
                "id" => 1,
                "name" => "Mark"
            ],
            [
                "id" => 2,
                "name" => "Mary"
            ],
            [
                "id" => 3,
                "name" => "Paul"
            ]
        ];

        $this->assertEquals([1, 2, 3], $function->applyFunction("memberValues id", $array, null));
        $this->assertEquals(["Mark", "Mary", "Paul"], $function->applyFunction("memberValues name", $array, null));


    }

    public function testCanJoinArrayValuesUsingDelimiter() {

        $function = new ArrayValueFunction();
        $this->assertTrue($function->doesFunctionApply("join"));


        $array = ["Mark", "James", "Mary"];
        $this->assertEquals("Mark,James,Mary", $function->applyFunction("join ,", $array, null));
        $this->assertEquals("Mark;James;Mary", $function->applyFunction("join ;", $array, null));

    }

    public function testCanSliceArrayBetweenIndexes() {
        $function = new ArrayValueFunction();
        $this->assertTrue($function->doesFunctionApply("slice"));

        $array = ["Mark", "James", "Mary"];
        $this->assertEquals(["James", "Mary"], $function->applyFunction("slice 1", $array, null));
        $this->assertEquals(["James"], $function->applyFunction("slice 1 1", $array, null));
        $this->assertEquals(["Mary"], $function->applyFunction("slice 2 1", $array, null));
        $this->assertEquals(["Mark", "James"], $function->applyFunction("slice 0 2", $array, null));

    }

    public function testCanPickItemFromArray() {
        $function = new ArrayValueFunction();
        $this->assertTrue($function->doesFunctionApply("item"));

        $array = ["Steve", 34, []];
        $this->assertEquals("Steve", $function->applyFunction("item 0", $array, null));
        $this->assertEquals(34, $function->applyFunction("item 1", $array, null));
        $this->assertEquals([], $function->applyFunction("item 2", $array, null));
        $this->assertEquals(null, $function->applyFunction("item 3", $array, null));

    }

    public function testCanPopItemFromArray() {
        $function = new ArrayValueFunction();
        $this->assertTrue($function->doesFunctionApply("pop"));

        $array1 = [1, 2, 3, 4];
        $array2 = ["uno", "dos", "tres"];
        $array3 = ["single"];
        $this->assertEquals(4, $function->applyFunction("pop", $array1, null));
        $this->assertEquals("tres", $function->applyFunction("pop", $array2, null));
        $this->assertEquals("single", $function->applyFunction("shift", $array3, null));

    }

    public function testCanShiftItemFromArray() {
        $function = new ArrayValueFunction();
        $this->assertTrue($function->doesFunctionApply("shift"));

        $array1 = [1, 2, 3, 4];
        $array2 = ["uno", "dos", "tres"];
        $array3 = ["single"];
        $this->assertEquals(1, $function->applyFunction("shift", $array1, null));
        $this->assertEquals("uno", $function->applyFunction("shift", $array2, null));
        $this->assertEquals("single", $function->applyFunction("shift", $array3, null));

    }

    public function testCanMergeValuesOfArray() {
        $function = new ArrayValueFunction();
        $this->assertTrue($function->doesFunctionApply("mergeValues"));

        $array1 = [[1, 2, 3], [7, 8, 9]];
        $array2 = [[], ["uno", "dos", "tres"]];
        $this->assertEquals([1, 2, 3, 7, 8, 9], $function->applyFunction("mergeValues", $array1, null));
        $this->assertEquals(["uno", "dos", "tres"], $function->applyFunction("mergeValues", $array2, null));

    }

    public function testCanGetDistinctItemsFromArray() {
        $function = new ArrayValueFunction();
        $this->assertTrue($function->doesFunctionApply("distinct"));

        $array1 = [0, 1, 1, 2, 3, 5, 8];
        $array2 = ["chas", "dave", "rabbit", "rabbit", "rabbit", "rabbit"];
        $this->assertEquals([0, 1, 2, 3, 5, 8], $function->applyFunction("distinct", $array1, null));
        $this->assertEquals(["chas", "dave", "rabbit"], $function->applyFunction("distinct", $array2, null));

    }

    public function testCanFilterArray() {
        $function = new ArrayValueFunction();
        $this->assertTrue($function->doesFunctionApply("filter"));

        $data = [
            [
                "name" => "bob",
                "age" => 18
            ], [
                "name" => "bobby",
                "age" => 25
            ], [
                "name" => "mary",
                "age" => 18
            ]
        ];

        $this->assertEquals([[
            "name" => "bob",
            "age" => 18
        ]], $function->applyFunction("filter 'name' 'bob' 'equals'", $data, null));

        $this->assertEquals([
            [
                "name" => "bob",
                "age" => 18
            ], [
                "name" => "mary",
                "age" => 18
            ]], $function->applyFunction("filter 'age' 18 'equals'", $data, null));

        $this->assertEquals([[
            "name" => "bobby",
            "age" => 25
        ]], $function->applyFunction("filter 'age' 18 'notequals'", $data, null));

        $this->assertEquals([
            [
                "name" => "bob",
                "age" => 18
            ], [
                "name" => "bobby",
                "age" => 25
            ]], $function->applyFunction("filter 'name' 'ob' 'like", $data, null));

        $this->assertEquals([
            [
                "name" => "bob",
                "age" => 18
            ], [
                "name" => "bobby",
                "age" => 25
            ]], $function->applyFunction("filter 'name' 'b' 'startsWith'", $data, null));

        $this->assertEquals([[
            "name" => "bobby",
            "age" => 25
        ]], $function->applyFunction("filter 'age' 22 'gt'", $data, null));

        $this->assertEquals([[
            "name" => "bobby",
            "age" => 25
        ]], $function->applyFunction("filter 'age' 25 'gte'", $data, null));

        $this->assertEquals([[
            "name" => "bob",
            "age" => 18
        ], [
            "name" => "mary",
            "age" => 18
        ]], $function->applyFunction("filter 'age' 25 'lt'", $data, null));

        $this->assertEquals($data, $function->applyFunction("filter 'age' 25 'lte'", $data, null));

        $this->assertEquals([[
                "name" => "bobby",
                "age" => 25
            ]], $function->applyFunction("filter 'age' 25 'contains'", $data, null));

        $this->assertEquals([[
            "name" => "bobby",
            "age" => 25
        ]], $function->applyFunction("filter 'age' 18 'notContains'", $data, null));
    }

    public function testCanSortArray() {
        $function = new ArrayValueFunction();
        $this->assertTrue($function->doesFunctionApply("sort"));

        $array1 = [9, 8, 7, 6, 5];
        $array2 = [4, 6, 2, 1, 3, 5];
        $this->assertEquals([5, 6, 7, 8, 9], $function->applyFunction("sort", $array1, null));
        $this->assertEquals([1, 2, 3, 4, 5, 6], $function->applyFunction("sort", $array2, null));
    }

    public function testCanSumNumericValuesOfArray() {
        $function = new ArrayValueFunction();
        $this->assertTrue($function->doesFunctionApply("sum"));

        $array = [1, 2, 3];
        $this->assertEquals(6, $function->applyFunction("sum", $array, null));

    }

}