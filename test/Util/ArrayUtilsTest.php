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


    public function testNonMappedKeysAreRemovedByDefault(){
        // Source
        $source = ["a" => "Hello", "b" => "World", "c" => "of", "d" => "fun"];
        $mappings = ["a" => "First", "b" => "Second"];

        $this->assertEquals(["First" => "Hello", "Second" => "World"], ArrayUtils::mapArrayKeys($source, $mappings));

    }


    public function testNonMappedKeysAreLeftIntactIfBooleanPassed(){

        // Source
        $source = ["a" => "Hello", "b" => "World", "c" => "of", "d" => "fun"];
        $mappings = ["a" => "First", "b" => "Second"];

        $this->assertEquals(["First" => "Hello", "Second" => "World", "c" => "of", "d" => "fun"], ArrayUtils::mapArrayKeys($source, $mappings, false));


    }

}