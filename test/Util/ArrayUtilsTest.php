<?php

namespace Kinikit\Core\Util;

include_once "autoloader.php";

/**
 * Test cases for the array utils
 *
 * @author mark
 *
 */
class ArrayUtilsTest extends \PHPUnit\Framework\TestCase {

    public function testCanPrefixAPassedAssociativeArrayKeysWithString() {

        $sourceArray = array("var1" => "Hello", "var2" => "Goodbye", "var3" => "Why");
        $this->assertEquals(array("mine_var1" => "Hello", "mine_var2" => "Goodbye", "mine_var3" => "Why"), ArrayUtils::prefixArrayKeys($sourceArray, "mine_"));
        $this->assertEquals(array("bingvar1" => "Hello", "bingvar2" => "Goodbye", "bingvar3" => "Why"), ArrayUtils::prefixArrayKeys($sourceArray, "bing"));

    }

    public function testCanExtractAllElementsOfAnArrayByKeyPrefix() {

        $sourceArray = array("param1" => "Bunny", "param2" => "Smiles", "param3" => "Junkies", "pears" => "Jumps", "apples" => "Mark", "applicant" => "John");
        $this->assertEquals(array("param1" => "Bunny", "param2" => "Smiles", "param3" => "Junkies"), ArrayUtils::getAllArrayItemsByKeyPrefix($sourceArray, "param"));
        $this->assertEquals(array("param1" => "Bunny", "param2" => "Smiles", "param3" => "Junkies", "pears" => "Jumps"), ArrayUtils::getAllArrayItemsByKeyPrefix($sourceArray, "p"));
        $this->assertEquals(array("apples" => "Mark", "applicant" => "John"), ArrayUtils::getAllArrayItemsByKeyPrefix($sourceArray, "app"));
        $this->assertEquals(array("apples" => "Mark"), ArrayUtils::getAllArrayItemsByKeyPrefix($sourceArray, "apple"));

    }

    public function testCanTestToSeeIfArrayIsAssociativeOrNot() {

        $array1 = array(1);
        $array2 = array("string");
        $array3 = array(1, 2, 3, 4, 5);
        $array4 = array("string", "string2", "string3");
        $array5 = array(1 => "Mark", 2 => "John", 3 => "Mary");
        $array6 = array("John" => "James");
        $array7 = array(1 => "John");
        $array8 = array("Mary" => 2, "Mark" => 3, "James" => 4);

        $this->assertFalse(ArrayUtils::isAssociative($array1));
        $this->assertFalse(ArrayUtils::isAssociative($array2));
        $this->assertFalse(ArrayUtils::isAssociative($array3));
        $this->assertFalse(ArrayUtils::isAssociative($array4));
        $this->assertTrue(ArrayUtils::isAssociative($array5));
        $this->assertTrue(ArrayUtils::isAssociative($array6));
        $this->assertTrue(ArrayUtils::isAssociative($array7));
        $this->assertTrue(ArrayUtils::isAssociative($array8));

    }


    public function testRecursiveMergeMergesAndLeavesDataInFirstArrayIntactForSingleLevelArrays() {

        $array1 = array("name" => "Peter", "address" => "3 White Lane", "phone" => "07595 894488");
        $array2 = array("name" => "Paul", "phone" => "07867 898989", "age" => 34, "shoeSize" => 10);

        $merged = ArrayUtils::recursiveMerge($array1, $array2);
        $this->assertEquals(array("name" => "Peter", "address" => "3 White Lane", "phone" => "07595 894488", "age" => 34, "shoeSize" => 10), $merged);

    }


    public function testRecursiveMergeMergesAndLeavesDataInFirstArrayIntactForDeepArrays() {

        $array1 = array("name" => "Peter", "address" => array("street1" => "3 My Lane", "city" => "Oxford", "country" => array("name" => "Great Britain")), "skills" => array("swimming" => 1, "fencing" => 2));
        $array2 = array("name" => "Paul", "address" => array("street1" => "7 Memory Lane", "street2" => "Jericho", "city" => "Oxfordish", "country" => array("name" => "France", "code" => "FR")),
            "skills" => array("swimming" => 1, "fencing" => 2, "shopping" => 3, "running" => 4));


        $merged = ArrayUtils::recursiveMerge($array1, $array2);

        $expectedResult = array("name" => "Peter", "address" => array("street1" => "3 My Lane", "street2" => "Jericho", "city" => "Oxford", "country" => array("name" => "Great Britain", "code" => "FR")),
            "skills" => array("swimming" => 1, "fencing" => 2, "shopping" => 3, "running" => 4));

        $this->assertEquals($expectedResult, $merged);

    }

    public function testRecursiveMergeMergesNoneAssociativeArrays() {

        $array1 = array("name" => "Bobby", "colours" => array(array("name" => "Red"), array("name" => "Blue"), array("name" => "Green")));
        $array2 = array("name" => "Jordan", "colours" => array(array("name" => "Red", "shade" => "Crimson"), array("name" => "Purple", "shade" => "Mauve"), array("name" => "Green"),
            array("name" => "Yellow", "shade" => "Sunset"), array("name" => "White", "shade" => "plain")));

        $merged = ArrayUtils::recursiveMerge($array1, $array2);

        $expectedResult = array("name" => "Bobby", "colours" => array(array("name" => "Red", "shade" => "Crimson"), array("name" => "Blue", "shade" => "Mauve"), array("name" => "Green"),
            array("name" => "Yellow", "shade" => "Sunset"), array("name" => "White", "shade" => "plain")));

        $this->assertEquals($expectedResult, $merged);
    }


    public function testRecursiveDiffReturnsADifferencesArrayForSimpleExample() {

        $array1 = array("name" => "Peter", "address" => "3 White Lane", "phone" => "07595 894488");
        $array2 = array("name" => "Paul", "phone" => "07867 898989", "age" => 34, "shoeSize" => 10);

        $differences = ArrayUtils::recursiveDiff($array1, $array2);

        $this->assertEquals(array("name" => array("value1" => "Peter", "value2" => "Paul"), "phone" => array("value1" => "07595 894488", "value2" => "07867 898989")), $differences);


    }


    public function testRecursiveDiffReturnsADifferencesArrayForNestedExample() {

        $array1 = array("name" => "Bobby", "colours" => array(array("name" => "Red"), array("name" => "Blue"), array("name" => "Green", "shade" => "Lime")));
        $array2 = array("name" => "Jordan", "colours" => array(array("name" => "Red", "shade" => "Crimson"), array("name" => "Purple", "shade" => "Mauve"), array("name" => "Green", "shade" => "Grass"),
            array("name" => "Yellow", "shade" => "Sunset"), array("name" => "White", "shade" => "plain")));

        $merged = ArrayUtils::recursiveDiff($array1, $array2);

        $expectedResult = array("name" => array("value1" => "Bobby", "value2" => "Jordan"), "colours" => array(array(), array("name" => array("value1" => "Blue", "value2" => "Purple")),
            array("shade" => array("value1" => "Lime", "value2" => "Grass"))));

        $this->assertEquals($expectedResult, $merged);

    }


    public function testCanReduceAnArrayToOnlyThoseElementsWithTheSpecifiedKeyRecursively() {

        $array = array("name" => "Plan A", "address" => array("name" => "Plan B", "code" => "Test", "phones" => array("name" => "Plan C", "number" => "01876 878778"), "fax" => "07676 8787887"));

        $reduced = ArrayUtils::reduceToElementsWithKey($array, "name");

        $expectedResult = array("name" => "Plan A", "address" => array("name" => "Plan B", "phones" => array("name" => "Plan C")));

        $this->assertEquals($expectedResult, $reduced);

    }


    public function testCanFindElementsByKeyRecursively() {
        $array = array("name" => "Plan A", "address" => array("name" => "Plan B", "code" => "Test", "phones" =>
            array(
                array("name" => "Plan C", "number" => "01876 878778", "fax" => "07676 8787887"),
                array("name" => "Plan D", "number" => "01876 878778", "fax" => "07676 8787887"))
        )
        );

        $elements = ArrayUtils::findElementsByKey($array, "name");

        $expectedResult = array("name" => "Plan A", "address.name" => "Plan B", "address.phones[0].name" => "Plan C", "address.phones[1].name" => "Plan D");

        $this->assertEquals($expectedResult, $elements);

    }

}

?>