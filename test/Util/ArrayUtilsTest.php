<?php

namespace Kinikit\Core\Util;

include_once "autoloader.php";

class ArrayUtilsTest extends \PHPUnit\Framework\TestCase {


    public function testCanMapArrayKeysUsingMappingArray() {

        // Source
        $source = ["a" => "Hello", "b" => "World", "c" => "of", "d" => "fun"];
        $mappings = ["a" => "First", "b" => "Second", "c" => "Third", "d" => "Fourth"];

        $this->assertEquals(["First" => "Hello", "Second" => "World", "Third" => "of", "Fourth" => "fun"], ArrayUtils::mapArrayKeys($source, $mappings));

    }


    public function testNonMappedKeysAreRemovedByDefault() {
        // Source
        $source = ["a" => "Hello", "b" => "World", "c" => "of", "d" => "fun"];
        $mappings = ["a" => "First", "b" => "Second"];

        $this->assertEquals(["First" => "Hello", "Second" => "World"], ArrayUtils::mapArrayKeys($source, $mappings));

    }


    public function testNonMappedKeysAreLeftIntactIfBooleanPassed() {

        // Source
        $source = ["a" => "Hello", "b" => "World", "c" => "of", "d" => "fun"];
        $mappings = ["a" => "First", "b" => "Second"];

        $this->assertEquals(["First" => "Hello", "Second" => "World", "c" => "of", "d" => "fun"], ArrayUtils::mapArrayKeys($source, $mappings, false));


    }

    public function testAny() {
        $this->assertEquals(false, ArrayUtils::any([false, false, false]));
        $this->assertEquals(true, ArrayUtils::any([false, true, false]));
        $this->assertEquals(true, ArrayUtils::any([false, false, true]));
        $this->assertEquals(false, ArrayUtils::any([]));
        try {
            $wrongTyped = ArrayUtils::any([false, 0, 1]);
            $this->fail();
        } catch (\Exception $exception) {
            //Succeed
        }
    }

    public function testAll() {
        $this->assertEquals(false, ArrayUtils::all([false, false, false]));
        $this->assertEquals(false, ArrayUtils::all([true, true, false]));
        $this->assertEquals(true, ArrayUtils::all([true, true, true]));
        $this->assertEquals(true, ArrayUtils::all([]));
        try {
            $wrongTyped = ArrayUtils::all([false, 0, 1]);
            $this->fail();
        } catch (\Exception $exception) {
            //Succeed
        }
    }


    public function testCanMergeArraysRecursivelyPreservingKeys() {

        $array1 = ["Test" => [3 => "Bing", 5 => "Bong"]];
        $array2 = ["Test" => [4 => "Bang", 1 => "Bung"]];
        $this->assertEquals(["Test" => [3 => "Bing", 5 => "Bong", 4 => "Bang", 1 => "Bung"]], ArrayUtils::mergeArrayRecursive($array1, $array2));


    }

}