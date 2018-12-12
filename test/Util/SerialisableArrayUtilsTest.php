<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 16/08/2018
 * Time: 17:04
 */

namespace Kinikit\Core\Util;

use Kinikit\Core\Exception\ClassNotSerialisableException;
use Kinikit\Core\Object\NoneSerialisable;
use Kinikit\Core\Object\PublicGetterSerialisable;
use Kinikit\Core\Object\SerialisableObject;
use Kinikit\Core\Object\ObjectWithId;

include_once "autoloader.php";

class SerialisableArrayUtilsTest extends \PHPUnit\Framework\TestCase {

    public function testCanGetMemberValueArrayFromAnArrayOfObjectsInAMatchingIndexedArray() {

        $object1 = new PublicGetterSerialisable("Mark Jones", "3 My Lane", "07878 989899");
        $object2 = new PublicGetterSerialisable("Mary Smith", "5 David Street", "01278 989898");
        $object3 = new PublicGetterSerialisable("Clive Staples", "7 Tinder House", "06565 767677");

        $array1 = array($object1, $object2, $object3);
        $array2 = array("Pinky" => $object2, "Perky" => $object3, "Piggy" => $object1);

        $this->assertEquals(array("Mark Jones", "Mary Smith", "Clive Staples"), SerialisableArrayUtils::getMemberValueArrayForObjects("name", $array1));
        $this->assertEquals(array("3 My Lane", "5 David Street", "7 Tinder House"), SerialisableArrayUtils::getMemberValueArrayForObjects("address", $array1));
        $this->assertEquals(array("07878 989899", "01278 989898", "06565 767677"), SerialisableArrayUtils::getMemberValueArrayForObjects("telephone", $array1));

        $this->assertEquals(array("Piggy" => "Mark Jones", "Pinky" => "Mary Smith", "Perky" => "Clive Staples"), SerialisableArrayUtils::getMemberValueArrayForObjects("name", $array2));
        $this->assertEquals(array("Piggy" => "3 My Lane", "Pinky" => "5 David Street", "Perky" => "7 Tinder House"), SerialisableArrayUtils::getMemberValueArrayForObjects("address", $array2));
        $this->assertEquals(array("Piggy" => "07878 989899", "Pinky" => "01278 989898", "Perky" => "06565 767677"), SerialisableArrayUtils::getMemberValueArrayForObjects("telephone", $array2));


    }


    public function testNonSerialisableObjectExceptionThrownIfAttemptIsMadeToGetMemberValueArrayForNonSerialisableObject() {
        self::assertTrue(true);
        $object1 = new NoneSerialisable();
        $object2 = new NoneSerialisable();

        try {

            SerialisableArrayUtils::getMemberValueArrayForObjects("monkey", array($object1, $object2));
            $this->fail("Should have thrown here");

        } catch (ClassNotSerialisableException $e) {
            // Success
        }


    }


    public function testCanFilterArrayOfObjectsByMemberValue() {

        $object1 = new PublicGetterSerialisable("Mark Jones", "Test", "Monkey");
        $object2 = new PublicGetterSerialisable("Mary Smith", "Test", "Gorilla");
        $object3 = new PublicGetterSerialisable("Clive Staples", "Test 2", "Monkey");


        $allObjects = array($object1, $object2, $object3);

        // Test some filters
        $matches = SerialisableArrayUtils::filterArrayOfObjectsByMember("name", $allObjects, "Mark Jones");
        $this->assertEquals(array($object1), $matches);

        $matches = SerialisableArrayUtils::filterArrayOfObjectsByMember("name", $allObjects, "Mary Smith");
        $this->assertEquals(array($object2), $matches);

        $matches = SerialisableArrayUtils::filterArrayOfObjectsByMember("name", $allObjects, "I don't exist");
        $this->assertEquals(array(), $matches);


        $matches = SerialisableArrayUtils::filterArrayOfObjectsByMember("address", $allObjects, "Test");
        $this->assertEquals(array($object1, $object2), $matches);

        $matches = SerialisableArrayUtils::filterArrayOfObjectsByMember("telephone", $allObjects, "Monkey");
        $this->assertEquals(array($object1, $object3), $matches);

    }


    public function testCanFilterArrayOfObjectsByMultipleValuesWhenSuppliedAsArray() {


        $object1 = new PublicGetterSerialisable("Mark Jones", "Test", "Monkey");
        $object2 = new PublicGetterSerialisable("Mary Smith", "Test", "Gorilla");
        $object3 = new PublicGetterSerialisable("Clive Staples", "Test 2", "Monkey");

        $allObjects = array($object1, $object2, $object3);

        // Test some filters
        $matches = SerialisableArrayUtils::filterArrayOfObjectsByMember("name", $allObjects, array("Mark Jones", "Mary Smith"));
        $this->assertEquals(array($object1, $object2), $matches);

        $matches = SerialisableArrayUtils::filterArrayOfObjectsByMember("name", $allObjects, array("Mary Smith", "Clive Staples"));
        $this->assertEquals(array($object2, $object3), $matches);

        $matches = SerialisableArrayUtils::filterArrayOfObjectsByMember("address", $allObjects, array("Test", "Test 2"));
        $this->assertEquals(array($object1, $object2, $object3), $matches);

        $matches = SerialisableArrayUtils::filterArrayOfObjectsByMember("telephone", $allObjects, array("Monkey", "Gorilla"));
        $this->assertEquals(array($object1, $object2, $object3), $matches);

    }


    public function testFilteringArraysOfObjectsThrowExceptionIfNotSerialisable() {
        $object1 = new NoneSerialisable();
        $object2 = new NoneSerialisable();
        self::assertTrue(true);
        try {

            SerialisableArrayUtils::filterArrayOfObjectsByMember("monkey", array($object1, $object2), "test");
            $this->fail("Should have thrown here");

        } catch (ClassNotSerialisableException $e) {
            // Success
        }

    }


    public function testCanGroupArrayOfObjectsByAMember() {

        $object1 = new PublicGetterSerialisable("Mark Jones", "Test", "Monkey");
        $object2 = new PublicGetterSerialisable("Mary Smith", "Test", "Gorilla");
        $object3 = new PublicGetterSerialisable("Clive Staples", "Test2", "Monkey");
        $object4 = new PublicGetterSerialisable("Mark Jones", "Test2", "Monkey3");
        $object5 = new PublicGetterSerialisable("Mary Jane", "Test2", "Gorilla");
        $object6 = new PublicGetterSerialisable("Clive Staples", "Test 3", "Gorilla");

        $allObjects = array($object1, $object2, $object3, $object4, $object5, $object6);

        $this->assertEquals(array("Mark Jones" => array($object1, $object4),
            "Mary Smith" => array($object2), "Clive Staples" => array($object3, $object6), "Mary Jane" => array($object5)),
            SerialisableArrayUtils::groupArrayOfObjectsByMember("name", $allObjects));


        $this->assertEquals(array("Test" => array($object1, $object2), "Test2" => array($object3, $object4, $object5), "Test 3" => array($object6)),
            SerialisableArrayUtils::groupArrayOfObjectsByMember("address", $allObjects));

        $this->assertEquals(array("Monkey" => array($object1, $object3), "Gorilla" => array($object2, $object5, $object6), "Monkey3" => array($object4)),
            SerialisableArrayUtils::groupArrayOfObjectsByMember("telephone", $allObjects));

    }


    public function testCanGroupArrayOfObjectsByMultipleMembersIfArraySuppliedForMember() {
        $object1 = new PublicGetterSerialisable("Mark Jones", "Test", "Monkey");
        $object2 = new PublicGetterSerialisable("Mary Smith", "Test", "Gorilla");
        $object3 = new PublicGetterSerialisable("Clive Staples", "Test2", "Monkey");
        $object4 = new PublicGetterSerialisable("Mark Jones", "Test2", "Monkey3");
        $object5 = new PublicGetterSerialisable("Mary Jane", "Test2", "Gorilla");
        $object6 = new PublicGetterSerialisable("Clive Staples", "Test 3", "Gorilla");

        $allObjects = array($object1, $object2, $object3, $object4, $object5, $object6);

        $this->assertEquals(array("Mark Jones" => array("Test" => array($object1), "Test2" => array($object4)),
            "Mary Smith" => array("Test" => array($object2)), "Clive Staples" => array("Test2" => array($object3), "Test 3" => array($object6)),
            "Mary Jane" => array("Test2" => array($object5))), SerialisableArrayUtils::groupArrayOfObjectsByMember(array("name", "address"), $allObjects));


        $this->assertEquals(array("Test" => array("Mark Jones" => array($object1), "Mary Smith" => array($object2)),
            "Test2" => array("Clive Staples" => array($object3), "Mark Jones" => array($object4), "Mary Jane" => array($object5)),
            "Test 3" => array("Clive Staples" => array($object6))), SerialisableArrayUtils::groupArrayOfObjectsByMember(array("address", "name"), $allObjects));


    }


    public function testCanConvertDeepNestedSerialisableStructuresToAssociativeArrays() {

        // Check that normal stuff is left intact
        $this->assertEquals(array(3, "Hello", "Goodbye"), SerialisableArrayUtils::convertSerialisableObjectsToAssociativeArrays(array(3, "Hello", "Goodbye")));
        $this->assertEquals(array("Name" => "Mark", "Age" => 3), SerialisableArrayUtils::convertSerialisableObjectsToAssociativeArrays(array("Name" => "Mark", "Age" => 3)));

        $arrayOfSerialisables = array(new ObjectWithId("Goon", 44, 10), new ObjectWithId("Smith", 33, 5));
        $this->assertEquals(array(array("id" => null, "name" => "Goon", "age" => 44, "shoeSize" => 10), array("id" => null, "name" => "Smith", "age" => 33, "shoeSize" => 5)),
            SerialisableArrayUtils::convertSerialisableObjectsToAssociativeArrays($arrayOfSerialisables));


        $deepSerialisable = array("top" => array("bottom" => array("middle" => array(new ObjectWithId("Bish", 33, 4)))));
        $this->assertEquals(array("top" => array("bottom" => array("middle" => array(array("id" => null, "name" => "Bish", "age" => 33, "shoeSize" => 4))))), SerialisableArrayUtils::convertSerialisableObjectsToAssociativeArrays($deepSerialisable));

    }


    public function testCanConvertSimpleAndDeepAssociativeArraysToObjects() {

        // Check really simple example
        $singleObject = array("id" => 545, "name" => "Mark");
        $this->assertEquals(new WrapperClass(545, "Mark"), SerialisableArrayUtils::convertArrayToSerialisableObjects($singleObject, "Kinikit\Core\Util\WrapperClass"));

        // Check simple nested example
        $nestedObject = array("id" => 333, "name" => "Bingo", "address" => array("id" => 111, "description" => "Powerful Bingo Street"));
        $this->assertEquals(new WrapperClass(333, "Bingo", new NestedClass(111, "Powerful Bingo Street")), SerialisableArrayUtils::convertArrayToSerialisableObjects($nestedObject, "Kinikit\Core\Util\WrapperClass"));


        $nestedComplexObject = array("id" => 222, "name" => "Bob", "address" => array("id" => 33, "description" => "My Land", "items" => [
            array("id" => 22, "description" => "Hello world"),
            array("id" => 33, "description" => "Goodbye Sun")
        ]));

        $expected = new WrapperClass(222, "Bob", new NestedClass(33, "My Land", array(new NestedClass(22, "Hello world"), new NestedClass(33, "Goodbye Sun"))));
        $this->assertEquals($expected, SerialisableArrayUtils::convertArrayToSerialisableObjects($nestedComplexObject, "Kinikit\Core\Util\WrapperClass"));


        $nestedClassesByKey = array("thisone" => array("id" => 22, "description" => "Hello world"), "thatone" => array("id" => 33, "description" => "Goodbye Sun"));
        $nestedObjectAssociativeArray = array("id" => 444, "name" => "Peach", "nestedClassesByKey" => $nestedClassesByKey);

        $expected = new WrapperClass("444", "Peach", null, array("thisone" => new NestedClass(22, "Hello world"), "thatone" => new NestedClass(33, "Goodbye Sun")));
        $this->assertEquals($expected, SerialisableArrayUtils::convertArrayToSerialisableObjects($nestedObjectAssociativeArray, "Kinikit\Core\Util\WrapperClass"));

        $nestedClassesByMultipleKey = array("red" => array(array("id" => 44, "description" => "New Land"),
            array("id" => 55, "description" => "Old Close")),
            "blue" => array(array("id" => 66, "description" => "Bingo"), array("id" => 77, "description" => "Hiho")));
        $wrapperData = array("id" => 100, "name" => "Bob", "nestedClassesByMultipleKey" => $nestedClassesByMultipleKey);

        $expected = new WrapperClass(100, "Bob", null, null, array("red" => array(new NestedClass(44, "New Land"),
            new NestedClass(55, "Old Close")), "blue" => array(new NestedClass(66, "Bingo"), new NestedClass(77, "Hiho"))));

        $this->assertEquals($expected, SerialisableArrayUtils::convertArrayToSerialisableObjects($wrapperData, "Kinikit\Core\Util\WrapperClass"));


    }


    public function testIfCreateValuesSuppliedValuesArraysAreInsertedForAllAssociativeArraysDownTheTree() {

        // Check that normal stuff is left intact
        $this->assertEquals(array(3, "Hello", "Goodbye"), SerialisableArrayUtils::convertSerialisableObjectsToAssociativeArrays(array(3, "Hello", "Goodbye"), true));
        $this->assertEquals(array("Name" => "Mark", "Age" => 3, "_values" => array("Mark", 3)), SerialisableArrayUtils::convertSerialisableObjectsToAssociativeArrays(array("Name" => "Mark", "Age" => 3), true));

        $arrayOfSerialisables = array(new ObjectWithId("Goon", 44, 10), new ObjectWithId("Smith", 33, 5));
        $this->assertEquals(array(array("id" => null, "name" => "Goon", "age" => 44, "shoeSize" => 10), array("id" => null, "name" => "Smith", "age" => 33, "shoeSize" => 5)),
            SerialisableArrayUtils::convertSerialisableObjectsToAssociativeArrays($arrayOfSerialisables, true));


        $deepSerialisable = array("top" => array("middle" => array(new ObjectWithId("Bish", 33, 4))));
        $this->assertEquals(array("top" => array("middle" => array(array("id" => null, "name" => "Bish", "age" => 33, "shoeSize" => 4)),
            "_values" => array(array(array("id" => null, "name" => "Bish", "age" => 33, "shoeSize" => 4)))), "_values" => array(array("middle" => array(array("id" => null, "name" => "Bish", "age" => 33, "shoeSize" => 4)),
            "_values" => array(array(array("id" => null, "name" => "Bish", "age" => 33, "shoeSize" => 4)))))),
            SerialisableArrayUtils::convertSerialisableObjectsToAssociativeArrays($deepSerialisable, true));


    }


}