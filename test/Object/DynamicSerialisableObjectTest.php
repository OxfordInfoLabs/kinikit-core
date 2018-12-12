<?php

namespace Kinikit\Core\Object;

use Kinikit\Core\Exception\NoneExistentMethodException;
use Kinikit\Core\Exception\PropertyNotReadableException;
use Kinikit\Core\Exception\PropertyNotWritableException;

include_once "autoloader.php";

/**
 * Test cases for the dynamic serialisable object.
 *
 * @author mark
 *
 */
class DynamicSerialisableObjectTest extends \PHPUnit\Framework\TestCase  {

    public function testInNoneStrictModeAnySettersAndMatchingGettersMayBeCalledForAnyArbitraryProperties() {

        $object = new DynamicSerialisableObject (false);
        $object->setName("Bodgett");
        $object->setAccomplice("Scarper");
        $object->setJob("Rob the bank");
        $object->setSuccessRate("10%");
        $object->setEstimatedHaul(5000);

        $this->assertEquals("Bodgett", $object->getName());
        $this->assertEquals("Scarper", $object->getAccomplice());
        $this->assertEquals("Rob the bank", $object->getJob());
        $this->assertEquals("10%", $object->getSuccessRate());
        $this->assertEquals(5000, $object->getEstimatedHaul());

        // Also check we can read any not set property and get null without error
        $this->assertNull($object->getDriverName());
        $this->assertNull($object->getARP());

    }

    public function testInNoneStrictModeAnyArbitraryPublicPropertiesMayBeSetAndGet() {

        $object = new DynamicSerialisableObject (false);
        $object->name = "Bob Jones";
        $object->address = "3 The South Side";
        $object->phone = "01865 989898";

        $this->assertEquals("Bob Jones", $object->name);
        $this->assertEquals("3 The South Side", $object->address);
        $this->assertEquals("01865 989898", $object->phone);

        // Also check we can read any not set properties and get null without error
        $this->assertNull($object->postcode);
        $this->assertNull($object->dateOfBirth);

    }

    public function testInNoneStrictModeGettersSettersAndPropertiesMayBeUsedInterchangeably() {

        $object = new DynamicSerialisableObject (false);
        $object->name = "Bob Jones";
        $object->setAddress("3 The South Side");
        $object->setPhone("01865 989898");

        $this->assertEquals("Bob Jones", $object->getName());
        $this->assertEquals("3 The South Side", $object->address);
        $this->assertEquals("01865 989898", $object->phone);

    }

    public function testInStrictModeGettersSettersAndPropertiesMayNotBeAccessedIfNoDynamicPropertyKeysSet() {
        self::assertTrue(true);
        $object = new DynamicSerialisableObject (true);

        // Check that we can't  write any arbitrary properties initially either by set method or by direct property set.
        try {
            $object->setName("Bodgett");
            $this->fail("Should have thrown here");
        } catch (PropertyNotWritableException $e) {
            // Success
        }

        try {
            $object->setAccomplice("Scarper");
            $this->fail("Should have thrown here");
        } catch (PropertyNotWritableException $e) {
            // Success
        }

        try {
            $object->accomplice = "Scarper";
            $this->fail("Should have thrown here");
        } catch (PropertyNotWritableException $e) {
            // Success
        }

        try {
            $object->name = "Bodgett";
            $this->fail("Should have thrown here");
        } catch (PropertyNotWritableException $e) {
            // Success
        }

        // Check that we can't read any arbitrary properties from the object either.


        try {
            $object->accomplice;
            $this->fail("Should have thrown here");
        } catch (PropertyNotReadableException $e) {
            // Success
        }

        try {
            $object->name;
            $this->fail("Should have thrown here");
        } catch (PropertyNotReadableException $e) {
            // Success
        }

        try {
            $object->getName();
            $this->fail("Should have thrown here");
        } catch (PropertyNotReadableException $e) {
            // Success
        }

        try {
            $object->getAccomplice();
            $this->fail("Should have thrown here");
        } catch (PropertyNotReadableException $e) {
            // Success
        }

    }

    public function testInStrictModeGettersSettersAndPropertiesMayBeAccessedIfFirstDefinedAsDynamicKeysUsingCall() {

        $object = new DynamicSerialisableObject (true);
        $object->__setDynamicPropertyKeys(array("name", "address", "phone", "fax"));

        $object->setName("Bernard");
        $object->address = "Bob Dillon";
        $object->setPhone("01223 355931");

        $this->assertEquals("Bernard", $object->name);
        $this->assertEquals("Bob Dillon", $object->getAddress());
        $this->assertEquals("01223 355931", $object->getPhone());

        // Check a not yet intitialised works.
        $this->assertNull($object->getFax());

        // Check we still can't access other properties
        try {
            $object->getAccomplice();
            $this->fail("Should have thrown here");
        } catch (PropertyNotReadableException $e) {
            // Success
        }

        try {
            $object->accomplice;
            $this->fail("Should have thrown here");
        } catch (PropertyNotReadableException $e) {
            // Success
        }

    }

    public function testInStrictAndNonStrictModeNoSuchMethodExceptionsAreRaisedIfAnyOtherNonExistentMethodIsAttemptedToBeCalledOtherThanSetAndGets() {
        self::assertTrue(true);
        $object = new DynamicSerialisableObject (true);
        $object->__setDynamicPropertyKeys(array("Bobbins"));
        try {
            $object->myMethod();
            $this->fail("Should have thrown here");
        } catch (NoneExistentMethodException $e) {
            // Success
        }

        try {
            $object->arbitraryMethod();
            $this->fail("Should have thrown here");
        } catch (NoneExistentMethodException $e) {
            // Success
        }

        // Now do a getter with an argument.
        try {
            $object->getBobbins("Baghdad");
            $this->fail("Should have thrown here");
        } catch (NoneExistentMethodException $e) {
            // Success
        }

        // Now do a setter with no arguments.
        try {
            $object->setBobbins();
            $this->fail("Should have thrown here");
        } catch (NoneExistentMethodException $e) {
            // Success
        }

        // Now do a setter with too many arguments.
        try {
            $object->setBobbins("Bunny", "Bags");
            $this->fail("Should have thrown here");
        } catch (NoneExistentMethodException $e) {
            // Success
        }

        $object = new DynamicSerialisableObject (false);

        try {
            $object->myMethod();
            $this->fail("Should have thrown here");
        } catch (NoneExistentMethodException $e) {
            // Success
        }

        try {
            $object->arbitraryMethod();
            $this->fail("Should have thrown here");
        } catch (NoneExistentMethodException $e) {
            // Success
        }

        // Now do a getter with an argument.
        try {
            $object->getBobbins("Baghdad");
            $this->fail("Should have thrown here");
        } catch (NoneExistentMethodException $e) {
            // Success
        }

        // Now do a setter with no arguments.
        try {
            $object->setBobbins();
            $this->fail("Should have thrown here");
        } catch (NoneExistentMethodException $e) {
            // Success
        }

        // Now do a setter with too many arguments.
        try {
            $object->setBobbins("Bunny", "Bags");
            $this->fail("Should have thrown here");
        } catch (NoneExistentMethodException $e) {
            // Success
        }

    }

    public function testAllDynamicPropertiesAreMergedIntoTheMainSerialisablePropertyMapWhenItIsObtainedInBothStrictAndNonStrictModes() {

        $mixedDynamic = new MixedDynamicSerialisable ("Mark Robertshaw", "3 Hawlings Row", "01865 787898", true);
        $mixedDynamic->__setDynamicPropertyKeys(array("age", "postCode", "bugs"));

        $mixedDynamic->setAge(33);
        $mixedDynamic->postCode = "CB4 2JL";

        // Grab all serialisable properties
        $propertyMap = $mixedDynamic->__getSerialisablePropertyMap();

        $this->assertEquals(5, sizeof($propertyMap));
        $this->assertEquals("Mark Robertshaw", $propertyMap ["name"]);
        $this->assertEquals("3 Hawlings Row", $propertyMap ["address"]);
        $this->assertEquals("01865 787898", $propertyMap ["telephone"]);
        $this->assertEquals("33", $propertyMap ["age"]);
        $this->assertEquals("CB4 2JL", $propertyMap ["postCode"]);

        $mixedDynamic = new MixedDynamicSerialisable ("Mark Robertshaw", "3 Hawlings Row", "01865 787898", false);
        $mixedDynamic->setTeamName("Roofing");
        $mixedDynamic->setTeamLeader("Terry");
        $mixedDynamic->setPOI("The Roof");

        // Grab all serialisable properties
        $propertyMap = $mixedDynamic->__getSerialisablePropertyMap();

        $this->assertEquals(6, sizeof($propertyMap));
        $this->assertEquals("Mark Robertshaw", $propertyMap ["name"]);
        $this->assertEquals("3 Hawlings Row", $propertyMap ["address"]);
        $this->assertEquals("01865 787898", $propertyMap ["telephone"]);
        $this->assertEquals("Roofing", $propertyMap ["teamName"]);
        $this->assertEquals("Terry", $propertyMap ["teamLeader"]);
        $this->assertEquals("The Roof", $propertyMap ["POI"]);

    }

    public function testIfPropertyMapSetForNoneStrictSerialisableAllUnknownPropertiesAreMappedDynamicallyRegardlessOfTheIgnoreFlag() {

        $mixedDynamic = new MixedDynamicSerialisable ("Mark Robertshaw", "3 Hawlings Row", "01865 787898", false);

        $data = array("name" => "Bob Jones", "address" => "Marsh Land", "telephone" => "01223 677686", "age" => 55, "gender" => "M", "weight" => 88.75);
        $mixedDynamic->__setSerialisablePropertyMap($data);

        $this->assertEquals("01223 677686", $mixedDynamic->__getSerialisablePropertyValue("telephone"));
        $this->assertEquals(55, $mixedDynamic->getAge());
        $this->assertEquals("M", $mixedDynamic->getGender());
        $this->assertEquals(88.75, $mixedDynamic->getWeight());

    }

    public function testIfPropertyMapSetForStrictSerialisableWithoutIgnoreNoneWritableFlagSetExceptionsAreRaisedIfUnpermittedPropertyIsSet() {
        self::assertTrue(true);
        $mixedDynamic = new MixedDynamicSerialisable ("Mark Robertshaw", "3 Hawlings Row", "01865 787898", true);
        $mixedDynamic->__setDynamicPropertyKeys(array("age", "weight"));

        $data = array("name" => "Bob Jones", "address" => "Marsh Land", "telephone" => "01223 677686", "age" => 55, "gender" => "M", "weight" => 88.75);

        try {
            $mixedDynamic->__setSerialisablePropertyMap($data);
            $this->fail("Should have thrown here");
        } catch (PropertyNotWritableException $e) {
            // Success
        }
    }

    public function testIfPropertyMapSetForStrictSerialisableWithIgnoreNoneWritableFlagSetUndefinedPropertiesAreSilentlyIgnored() {
        $mixedDynamic = new MixedDynamicSerialisable ("Mark Robertshaw", "3 Hawlings Row", "01865 787898", false);
        $mixedDynamic->__setDynamicPropertyKeys(array("age", "weight"));

        $data = array("name" => "Bob Jones", "address" => "Marsh Land", "telephone" => "01223 677686", "age" => 55, "gender" => "M", "weight" => 88.75);
        $mixedDynamic->__setSerialisablePropertyMap($data);

        $this->assertEquals("01223 677686", $mixedDynamic->__getSerialisablePropertyValue("telephone"));
        $this->assertEquals(55, $mixedDynamic->getAge());
        $this->assertEquals(88.75, $mixedDynamic->getWeight());
    }


    public function testCanSetAndGetPropertiesInDynamicSerialisableObjectUsingArrayAccess() {
        $dynamic = new BlankDynamic();

        $dynamic["name"] = "Nathan";
        $dynamic["address"] = "3 Red Hill, Oxford";
        $dynamic["phone"] = "01865 777988";

        // Confirm properties have been set
        $this->assertEquals("Nathan", $dynamic->getName());
        $this->assertEquals("3 Red Hill, Oxford", $dynamic->getAddress());
        $this->assertEquals("01865 777988", $dynamic->getPhone());

        // Confirm Array access
        $this->assertEquals("Nathan", $dynamic["name"]);
        $this->assertEquals("3 Red Hill, Oxford", $dynamic ["address"]);
        $this->assertEquals("01865 777988", $dynamic["phone"]);
    }


    public function testCanUnsetAndCheckForArrayKeyExistence() {

        $dynamic = new BlankDynamic();

        $dynamic["name"] = "Nathan";
        $dynamic["address"] = "3 Red Hill, Oxford";
        $dynamic["phone"] = "01865 777988";

        unset($dynamic["phone"]);

        $this->assertNull($dynamic->getPhone());


        $this->assertTrue(isset($dynamic["address"]));
        $this->assertFalse(isset($dynamic["phone"]));

    }


    public function testCanIterateUsingForeachWithKeyValuePairs() {

        $dynamic = new BlankDynamic();

        $dynamic["name"] = "Nathan";
        $dynamic["address"] = "3 Red Hill, Oxford";
        $dynamic["phone"] = "01865 777988";

        $newDynamic = array();
        foreach ($dynamic as $member => $value) {
            $newDynamic[$member] = $value;
        }

        $this->assertEquals(array("name" => "Nathan", "address" => "3 Red Hill, Oxford", "phone" => "01865 777988"), $newDynamic);

    }


    public function testNestedDynamicSerialisableObjectsWithArraysBehaveCorrectly() {
        $dynamic = new BlankDynamic();

        $dynamic["Mark"] = new BlankDynamic();
        $dynamic["Mark"]["Luke"] = new BlankDynamic();
        $dynamic["Mark"]["Luke"]["Tim"] = "Bingo";

        $this->assertEquals("Bingo", $dynamic["Mark"]["Luke"]["Tim"]);


        $dynamic["Top"] = array();
        $dynamic["Top"][0] = new BlankDynamic();
        $dynamic["Top"][0]["Middle"] = "Bongo";

        $this->assertEquals("Bongo", $dynamic["Top"][0]["Middle"]);

    }

}

?>