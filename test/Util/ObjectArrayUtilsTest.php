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
use Kinikit\Core\Object\SerialisableObject;
use Kinikit\Core\Object\ObjectWithId;

include_once "autoloader.php";

class ObjectArrayUtilsTest extends \PHPUnit\Framework\TestCase {

    public function testCanGetMemberValueArrayFromAnArrayOfObjectsInAMatchingIndexedArray() {

        $object1 = new PublicGetterObject("Mark Jones", "3 My Lane", "07878 989899");
        $object2 = new PublicGetterObject("Mary Smith", "5 David Street", "01278 989898");
        $object3 = new PublicGetterObject("Clive Staples", "7 Tinder House", "06565 767677");

        $array1 = array($object1, $object2, $object3);
        $array2 = array("Pinky" => $object2, "Perky" => $object3, "Piggy" => $object1);

        $this->assertEquals(array("Mark Jones", "Mary Smith", "Clive Staples"), ObjectArrayUtils::getMemberValueArrayForObjects("name", $array1));
        $this->assertEquals(array("3 My Lane", "5 David Street", "7 Tinder House"), ObjectArrayUtils::getMemberValueArrayForObjects("address", $array1));
        $this->assertEquals(array("07878 989899", "01278 989898", "06565 767677"), ObjectArrayUtils::getMemberValueArrayForObjects("telephone", $array1));

        $this->assertEquals(array("Piggy" => "Mark Jones", "Pinky" => "Mary Smith", "Perky" => "Clive Staples"), ObjectArrayUtils::getMemberValueArrayForObjects("name", $array2));
        $this->assertEquals(array("Piggy" => "3 My Lane", "Pinky" => "5 David Street", "Perky" => "7 Tinder House"), ObjectArrayUtils::getMemberValueArrayForObjects("address", $array2));
        $this->assertEquals(array("Piggy" => "07878 989899", "Pinky" => "01278 989898", "Perky" => "06565 767677"), ObjectArrayUtils::getMemberValueArrayForObjects("telephone", $array2));


    }





    public function testCanFilterArrayOfObjectsByMemberValue() {

        $object1 = new PublicGetterObject("Mark Jones", "Test", "Monkey");
        $object2 = new PublicGetterObject("Mary Smith", "Test", "Gorilla");
        $object3 = new PublicGetterObject("Clive Staples", "Test 2", "Monkey");


        $allObjects = array($object1, $object2, $object3);

        // Test some filters
        $matches = ObjectArrayUtils::filterArrayOfObjectsByMember("name", $allObjects, "Mark Jones");
        $this->assertEquals(array($object1), $matches);

        $matches = ObjectArrayUtils::filterArrayOfObjectsByMember("name", $allObjects, "Mary Smith");
        $this->assertEquals(array($object2), $matches);

        $matches = ObjectArrayUtils::filterArrayOfObjectsByMember("name", $allObjects, "I don't exist");
        $this->assertEquals(array(), $matches);


        $matches = ObjectArrayUtils::filterArrayOfObjectsByMember("address", $allObjects, "Test");
        $this->assertEquals(array($object1, $object2), $matches);

        $matches = ObjectArrayUtils::filterArrayOfObjectsByMember("telephone", $allObjects, "Monkey");
        $this->assertEquals(array($object1, $object3), $matches);

    }


    public function testCanFilterArrayOfObjectsByMultipleValuesWhenSuppliedAsArray() {


        $object1 = new PublicGetterObject("Mark Jones", "Test", "Monkey");
        $object2 = new PublicGetterObject("Mary Smith", "Test", "Gorilla");
        $object3 = new PublicGetterObject("Clive Staples", "Test 2", "Monkey");

        $allObjects = array($object1, $object2, $object3);

        // Test some filters
        $matches = ObjectArrayUtils::filterArrayOfObjectsByMember("name", $allObjects, array("Mark Jones", "Mary Smith"));
        $this->assertEquals(array($object1, $object2), $matches);

        $matches = ObjectArrayUtils::filterArrayOfObjectsByMember("name", $allObjects, array("Mary Smith", "Clive Staples"));
        $this->assertEquals(array($object2, $object3), $matches);

        $matches = ObjectArrayUtils::filterArrayOfObjectsByMember("address", $allObjects, array("Test", "Test 2"));
        $this->assertEquals(array($object1, $object2, $object3), $matches);

        $matches = ObjectArrayUtils::filterArrayOfObjectsByMember("telephone", $allObjects, array("Monkey", "Gorilla"));
        $this->assertEquals(array($object1, $object2, $object3), $matches);

    }





    public function testCanGroupArrayOfObjectsByAMember() {

        $object1 = new PublicGetterObject("Mark Jones", "Test", "Monkey");
        $object2 = new PublicGetterObject("Mary Smith", "Test", "Gorilla");
        $object3 = new PublicGetterObject("Clive Staples", "Test2", "Monkey");
        $object4 = new PublicGetterObject("Mark Jones", "Test2", "Monkey3");
        $object5 = new PublicGetterObject("Mary Jane", "Test2", "Gorilla");
        $object6 = new PublicGetterObject("Clive Staples", "Test 3", "Gorilla");

        $allObjects = array($object1, $object2, $object3, $object4, $object5, $object6);

        $this->assertEquals(array("Mark Jones" => array($object1, $object4),
            "Mary Smith" => array($object2), "Clive Staples" => array($object3, $object6), "Mary Jane" => array($object5)),
            ObjectArrayUtils::groupArrayOfObjectsByMember("name", $allObjects));


        $this->assertEquals(array("Test" => array($object1, $object2), "Test2" => array($object3, $object4, $object5), "Test 3" => array($object6)),
            ObjectArrayUtils::groupArrayOfObjectsByMember("address", $allObjects));

        $this->assertEquals(array("Monkey" => array($object1, $object3), "Gorilla" => array($object2, $object5, $object6), "Monkey3" => array($object4)),
            ObjectArrayUtils::groupArrayOfObjectsByMember("telephone", $allObjects));

    }


    public function testCanGroupArrayOfObjectsByMultipleMembersIfArraySuppliedForMember() {
        $object1 = new PublicGetterObject("Mark Jones", "Test", "Monkey");
        $object2 = new PublicGetterObject("Mary Smith", "Test", "Gorilla");
        $object3 = new PublicGetterObject("Clive Staples", "Test2", "Monkey");
        $object4 = new PublicGetterObject("Mark Jones", "Test2", "Monkey3");
        $object5 = new PublicGetterObject("Mary Jane", "Test2", "Gorilla");
        $object6 = new PublicGetterObject("Clive Staples", "Test 3", "Gorilla");

        $allObjects = array($object1, $object2, $object3, $object4, $object5, $object6);

        $this->assertEquals(array("Mark Jones" => array("Test" => array($object1), "Test2" => array($object4)),
            "Mary Smith" => array("Test" => array($object2)), "Clive Staples" => array("Test2" => array($object3), "Test 3" => array($object6)),
            "Mary Jane" => array("Test2" => array($object5))), ObjectArrayUtils::groupArrayOfObjectsByMember(array("name", "address"), $allObjects));


        $this->assertEquals(array("Test" => array("Mark Jones" => array($object1), "Mary Smith" => array($object2)),
            "Test2" => array("Clive Staples" => array($object3), "Mark Jones" => array($object4), "Mary Jane" => array($object5)),
            "Test 3" => array("Clive Staples" => array($object6))), ObjectArrayUtils::groupArrayOfObjectsByMember(array("address", "name"), $allObjects));


    }



}
