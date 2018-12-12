<?php

namespace Kinikit\Core\Object;

use Kinikit\Core\Exception\ClassNotSerialisableException;
use Kinikit\Core\Exception\PropertyNotReadableException;
use Kinikit\Core\Exception\PropertyNotWritableException;

include_once "autoloader.php";

/**
 * test case.
 */
class SerialisableObjectTest extends \PHPUnit\Framework\TestCase {

    public function testCanSetValidSerialisablePropertyMapForASerialisableObjectSubClassWithPublicSetters() {

        $object = new PublicGetterSerialisable ();

        // Set a full map of data.
        $propertyMap = array("name" => "Marko Polo", "address" => "3 The Lane", "telephone" => "01865 787898");
        $object->__setSerialisablePropertyMap($propertyMap);

        $this->assertEquals("Marko Polo", $object->getName());
        $this->assertEquals("3 The Lane", $object->getAddress());
        $this->assertEquals("01865 787898", $object->getTelephone());

        // Now set a partial map of data.
        $propertyMap = array("name" => "John Ball", "telephone" => "01865 111111");
        $object->__setSerialisablePropertyMap($propertyMap);

        $this->assertEquals("John Ball", $object->getName());
        $this->assertEquals("3 The Lane", $object->getAddress());
        $this->assertEquals("01865 111111", $object->getTelephone());

    }

    public function testCanSetValidSerialisablePropertyMapForASerialisableObjectSubClassWithPublicMembers() {

        $object = new PublicMemberSerialisable ();

        // Set a full map of data.
        $propertyMap = array("school" => "My Village College", "region" => "Oxford", "SATAverage" => "87%");
        $object->__setSerialisablePropertyMap($propertyMap);

        $this->assertEquals("My Village College", $object->school);
        $this->assertEquals("Oxford", $object->region);
        $this->assertEquals("87%", $object->satAverage);

        // Now set a partial map (check for case insensitivity too)
        $propertyMap = array("school" => "Abingdon School", "SATAverage" => "97%");
        $object->__setSerialisablePropertyMap($propertyMap);

        $this->assertEquals("Abingdon School", $object->school);
        $this->assertEquals("Oxford", $object->region);
        $this->assertEquals("97%", $object->satAverage);

    }

    public function testCanSetValidSerialisablePropertyMapForASerialisableObjectSubClassWithProtectedSettersOrMembers() {

        $object = new ProtectedSerialisable ();
        $propertyMap = array("mother" => "Jane", "father" => "Bob", "brother" => "Frank");
        $object->__setSerialisablePropertyMap($propertyMap);

        $this->assertEquals("Jane,Bob,Brother:Frank", $object->toString());

    }

    public function testCanSetMixedObjectWithDifferentTypesOfPropertyAndFunctionsAreCalledInFavourOfPropertiesIfPresent() {

        $object = new MixedSerialisable ();
        $propertyMap = array("petName" => "Bonzo", "petAge" => 13, "petSex" => "M", "petOwner" => "Jim", "birthDate" => "01/01/2000", "birthPlace" => "Oxford", "mothersName" => "Woody");
        $object->__setSerialisablePropertyMap($propertyMap);

        $this->assertEquals(trim(",PetName:Bonzo,13,PetSex:M,PetOwner:Jim,01/01/2000,BirthPlace:Oxford,MothersName:Woody"), trim($object->toString()));

    }

    public function testCanGetSerialisablePropertyMapForASerialisableObjectSubClassWithPublicGetters() {

        $object = new PublicGetterSerialisable ();
        $object->setName("Bobbins");
        $object->setAddress("3 The Closes");
        $object->setTelephone("01865 787878");

        $serialisableMap = $object->__getSerialisablePropertyMap();
        $this->assertEquals(3, sizeof($serialisableMap));

        $this->assertEquals("Bobbins", $serialisableMap ["name"]);
        $this->assertEquals("3 The Closes", $serialisableMap ["address"]);
        $this->assertEquals("01865 787878", $serialisableMap ["telephone"]);

    }

    public function testCanGetSerialisablePropertyMapForASerialisableObjectSubClassWithPublicMembers() {
        $object = new PublicMemberSerialisable ();
        $object->school = "Oxford Comp";
        $object->region = "Oxon";
        $object->satAverage = "98%";

        $serialisableMap = $object->__getSerialisablePropertyMap();
        $this->assertEquals(3, sizeof($serialisableMap));

        $this->assertEquals("Oxford Comp", $serialisableMap ["school"]);
        $this->assertEquals("Oxon", $serialisableMap ["region"]);
        $this->assertEquals("98%", $serialisableMap ["satAverage"]);

    }

    public function testCanGetValidSerialisablePropertyMapForASerialisableObjectSubClassWithProtectedSettersOrMembers() {
        $object = new ProtectedSerialisable ("Molly", "Stefan", "Bobby");
        $serialisableMap = $object->__getSerialisablePropertyMap();
        $this->assertEquals(3, sizeof($serialisableMap));
        $this->assertEquals("Molly", $serialisableMap ["mother"]);
        $this->assertEquals("Stefan", $serialisableMap ["father"]);
        $this->assertEquals("Bobby", $serialisableMap ["brother"]);
    }

    public function testCanGetPropertyMapForAMixedSerialisableObject() {

        $object = new MixedSerialisable ("Cat", "Moggy", 5, "F", "Miss Piggy", "11/11/2005", "London", "Bonnie and Clyde");

        $serialisableMap = $object->__getSerialisablePropertyMap();
        $this->assertEquals(7, sizeof($serialisableMap));
        $this->assertEquals("PetName:Moggy", $serialisableMap ["petName"]);
        $this->assertEquals(5, $serialisableMap ["petAge"]);
        $this->assertEquals("PetSex:F", $serialisableMap ["petSex"]);
        $this->assertEquals("PetOwner:Miss Piggy", $serialisableMap ["petOwner"]);
        $this->assertEquals("11/11/2005", $serialisableMap ["birthDate"]);
        $this->assertEquals("BirthPlace:London", $serialisableMap ["birthPlace"]);
        $this->assertEquals("MothersName:Bonnie and Clyde", $serialisableMap ["mothersName"]);

    }

    public function testCanGetPropertyMapForASerialisableObjectWithPropertiesWithNumbersIn() {

        $object = new SerialisableWithNumbersInProperties ("Apples", "Pears", "Bananas");
        $serialisableMap = $object->__getSerialisablePropertyMap();
        $this->assertEquals(3, sizeof($serialisableMap));
        $this->assertEquals("Apples", $serialisableMap ["regularProperty"]);
        $this->assertEquals("Pears", $serialisableMap ["m4Property"]);
        $this->assertEquals("Bananas", $serialisableMap ["another5Property"]);

    }

    public function testIfAnyPropertiesAreSetWhichDontExistExceptionsAreRaisedUnlessAllowNoneExistentFlagPassedIn() {

        $object = new MixedSerialisable ();
        $propertyMap = array("badProp" => "Dog", "petName" => "Bonzo", "petAge" => 13, "petSex" => "M", "petOwner" => "Jim", "birthDate" => "01/01/2000", "birthPlace" => "Oxford", "mothersName" => "Woody");
        try {
            $object->__setSerialisablePropertyMap($propertyMap);
            $this->fail("Should have thrown here");
        } catch (PropertyNotWritableException $e) {
            // Success
        }

        $propertyMap = array("badProp" => "Dog", "petName" => "Bonzo", "petAge" => 13, "petSex" => "M", "dodgyOwner" => "Jim", "birthDate" => "01/01/2000", "birthPlace" => "Oxford", "mothersName" => "Woody");
        try {
            $object->__setSerialisablePropertyMap($propertyMap);
            $this->fail("Should have thrown here");
        } catch (PropertyNotWritableException $e) {
            // Success
        }

        // Check that the bad property is ignored if allow none existent flag passed in and bad props are returned as an array.
        $badProps = $object->__setSerialisablePropertyMap($propertyMap, true);
        $this->assertEquals(2, sizeof($badProps));
        $this->assertEquals("Dog", $badProps ["badProp"]);
        $this->assertEquals("Jim", $badProps ["dodgyOwner"]);
    }

    public function testSinglePropertyValueCanBeReadFromASerialisableObjectWithPropertyNotReadableExceptionRaisedIfNoneExistent() {

        $object = new MixedSerialisable ("Cat", "Moggy", 5, "F", "Miss Piggy", "11/11/2005", "London", "Bonnie and Clyde");
        $this->assertEquals("PetName:Moggy", $object->__getSerialisablePropertyValue("petName"));
        $this->assertEquals(5, $object->__getSerialisablePropertyValue("petAge"));
        $this->assertEquals("PetSex:F", $object->__getSerialisablePropertyValue("petSex"));
        $this->assertEquals("PetOwner:Miss Piggy", $object->__getSerialisablePropertyValue("petOwner"));
        $this->assertEquals("BirthPlace:London", $object->__getSerialisablePropertyValue("birthPlace"));

        try {
            $object->__getSerialisablePropertyValue("petType");
            $this->fail("Should have thrown here");
        } catch (PropertyNotReadableException $e) {
            // Success
        }

        try {
            $object->__getSerialisablePropertyValue("arbitrary");
            $this->fail("Should have thrown here");
        } catch (PropertyNotReadableException $e) {
            // Success
        }

    }

    public function testSinglePropertyValueCanBeSetForASerialisableObjectWithPropertyNotWritableExceptionRaisedIfNoneExistent() {
        $object = new MixedSerialisable ("Cat", "Moggy", 5, "F", "Miss Piggy", "11/11/2005", "London", "Bonnie and Clyde");
        $object->__setSerialisablePropertyValue("petName", "Hoover");
        $object->__setSerialisablePropertyValue("petAge", 7);
        $object->__setSerialisablePropertyValue("petSex", "M");
        $object->__setSerialisablePropertyValue("petOwner", "The Queen");

        $this->assertEquals(trim("Cat,PetName:Hoover,7,PetSex:M,PetOwner:The Queen,11/11/2005,London,Bonnie and Clyde"), trim($object->toString()));

        // Now test dodgy ones
        try {
            $object->__setSerialisablePropertyValue("petType", "Kitten");
            $this->fail("Should have thrown here");
        } catch (PropertyNotWritableException $e) {
            // Success
        }

        try {
            $object->__setSerialisablePropertyValue("arbitrary", "Value");
            $this->fail("Should have thrown here");
        } catch (PropertyNotWritableException $e) {
            // Success
        }

    }

    public function testStaticPropertiesAndGettersAreIgnoredForSerialisation() {

        $object = new SerialisableWithStatics (2334, "Jolly Roger", 44, 3);

        $this->assertEquals(array("id" => 2334, "name" => "Jolly Roger"), $object->__getSerialisablePropertyMap());

    }

    public function testGettersWithArgumentsAreIgnoredForSerialisation() {
        $object = new SerialisableWithUnmappableGetters (2678, "Bobby", 56, 14);

        $this->assertEquals(array("id" => 2678, "name" => "Bobby", "shoeSize" => 14), $object->__getSerialisablePropertyMap());

    }


    public function testCanApplyNoSerialiseTagsToGetMethodsOrProtectedVariablesToPreventInclusionInSerialisation() {

        $serialisable = new SerialisableWithNoSerialise("Bob Brown", "35 My Lane", 35, "01/10/2010");

        $this->assertEquals(array("name" => "Bob Brown", "startDate" => "01/10/2010"), $serialisable->__getSerialisablePropertyMap());

        $this->assertEquals("Bob Brown", $serialisable->__getSerialisablePropertyValue("name"));
        $this->assertEquals("01/10/2010", $serialisable->__getSerialisablePropertyValue("startDate"));


        try {
            $serialisable->__getSerialisablePropertyValue("address");
            $this->fail("Should have thrown");
        } catch (PropertyNotReadableException $e) {
            // Success
        }
        try {
            $this->assertNull($serialisable->__getSerialisablePropertyValue("age"));
            $this->fail("Should have thrown");
        } catch (PropertyNotReadableException $e) {
            // Success
        }


    }

}

