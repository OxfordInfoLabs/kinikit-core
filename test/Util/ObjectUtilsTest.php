<?php
namespace Kinikit\Core\Util;

use Kinikit\Core\Object\PublicGetterSerialisable;
use Kinikit\Core\Object\PublicMemberSerialisable;

include_once "autoloader.php";

/**
 * Class ObjectUtilsTest
 */
class ObjectUtilsTest extends \PHPUnit\Framework\TestCase {


    public function testSingleMembersAreReturnedUsingGettersIfDefined() {

        $object = new PublicGetterSerialisable("mark", "test", "smile");

        $this->assertEquals("mark", ObjectUtils::getNestedObjectProperty($object, "name"));
        $this->assertEquals("test", ObjectUtils::getNestedObjectProperty($object, "address"));
        $this->assertEquals("smile", ObjectUtils::getNestedObjectProperty($object, "telephone"));


    }


    public function testSingleMembersAreReturnedUsingDirectMemberAccessIfDefined() {
        $object = new PublicMemberSerialisable();
        $object->school = "St Leonards";
        $object->region = "Oxford";
        $object->SATAverage = 90.5;

        $this->assertEquals("St Leonards", ObjectUtils::getNestedObjectProperty($object, "school"));
        $this->assertEquals("Oxford", ObjectUtils::getNestedObjectProperty($object, "region"));
        $this->assertEquals("90.5", ObjectUtils::getNestedObjectProperty($object, "SATAverage"));

    }

    public function testAssociativeArraysMayAlsoBeTraversedWithDirectMemberAccess() {
        $object = array("name" => "Mark", "address" => array("street1" => "3 The Lane", "city" => "Oxford"));

        $this->assertEquals("Mark", ObjectUtils::getNestedObjectProperty($object, "name"));
        $this->assertEquals("Oxford", ObjectUtils::getNestedObjectProperty($object, "address.city"));
    }


    public function testMultiplyNestedMembersAreReturnedAsExpected() {

        $object = new PublicGetterSerialisable("mark", new PublicGetterSerialisable("mary", "test", new PublicGetterSerialisable("john", "test2", "01865")), "07595");

        $this->assertEquals("test", ObjectUtils::getNestedObjectProperty($object, "address.address"));
        $this->assertEquals("john", ObjectUtils::getNestedObjectProperty($object, "address.telephone.name"));


    }

    public function testSimpleArrayMembersAreEvaluated() {

        $object = new  PublicGetterSerialisable("mark", array("john", "smith", "james"), "philip");
        $this->assertEquals("john", ObjectUtils::getNestedObjectProperty($object, "address[0]"));
        $this->assertEquals("smith", ObjectUtils::getNestedObjectProperty($object, "address[1]"));
        $this->assertEquals("james", ObjectUtils::getNestedObjectProperty($object, "address[2]"));

        $object = new  PublicGetterSerialisable("mark", array(new PublicGetterSerialisable("bing", "bang", "bong"), new PublicGetterSerialisable("bish", "bash", array("bosh1", "bosh2", "bosh3"))), "philip");

        $this->assertEquals("bang", ObjectUtils::getNestedObjectProperty($object, "address[0].address"));
        $this->assertEquals("bish", ObjectUtils::getNestedObjectProperty($object, "address[1].name"));
        $this->assertEquals("bosh3", ObjectUtils::getNestedObjectProperty($object, "address[1].telephone[2]"));


    }

    public function testInvalidPathsReturnNull() {
        $object = new  PublicGetterSerialisable("mark", array(new PublicGetterSerialisable("bing", "bang", "bong"), new PublicGetterSerialisable("bish", "bash", array("bosh1", "bosh2", "bosh3"))), "philip");

        $this->assertNull(ObjectUtils::getNestedObjectProperty($object, "flame"));
        $this->assertNull(ObjectUtils::getNestedObjectProperty($object, "address[0].flame"));
        $this->assertNull(ObjectUtils::getNestedObjectProperty($object, "address[3].name"));
        $this->assertNull(ObjectUtils::getNestedObjectProperty($object, "address[1].telephone[4]"));


    }


    public function testSingleMembersWithPublicSettersAreSetCorrectly() {


        $object = new PublicGetterSerialisable();

        ObjectUtils::setNestedObjectProperty("Mark", $object, "name");
        ObjectUtils::setNestedObjectProperty("3 The Lane", $object, "address");
        ObjectUtils::setNestedObjectProperty("01865 784294", $object, "telephone");

        $this->assertEquals(new PublicGetterSerialisable("Mark", "3 The Lane", "01865 784294"), $object);


    }


    public function testSingleMembersWithPublicMemberAccessAreSetCorrectly() {
        $object = new PublicMemberSerialisable();

        ObjectUtils::setNestedObjectProperty("Oxford High", $object, "school");
        ObjectUtils::setNestedObjectProperty("Oxfordshire", $object, "region");
        ObjectUtils::setNestedObjectProperty("97.5", $object, "satAverage");

        $this->assertEquals("Oxford High", $object->school);
        $this->assertEquals("Oxfordshire", $object->region);
        $this->assertEquals("97.5", $object->satAverage);

    }


    public function testAssociativeArraysMayAlsoBePopulatedUsingDirectMemberNotation() {
        $object = array();

        ObjectUtils::setNestedObjectProperty("Mark", $object, "name");
        ObjectUtils::setNestedObjectProperty("3 The Lane", $object, "address");
        ObjectUtils::setNestedObjectProperty("01865 784294", $object, "telephone");

        $this->assertEquals(array("name" => "Mark", "address" => "3 The Lane", "telephone" => "01865 784294"), $object);
    }


    public function testMultiplyNestedMembersAreSetAsExpected() {

        $object = new PublicGetterSerialisable(null, new PublicGetterSerialisable(array("components" => array()), null, new PublicGetterSerialisable(null, null, null)), null);

        ObjectUtils::setNestedObjectProperty("3 Finger Road", $object, "address.address");
        ObjectUtils::setNestedObjectProperty("John", $object, "address.telephone.name");

        ObjectUtils::setNestedObjectProperty("Marko", $object, "address.name.components.forename");

        $this->assertEquals("3 Finger Road", $object->getAddress()->getAddress());
        $this->assertEquals("John", $object->getAddress()->getTelephone()->getName());
        // $this->assertEquals("Marko", $object->getAddress()->getName()["components"]["forename"]);


    }


    public function testCanGetDifferingMembersForTwoSimpleObjects() {

        $object = new PublicGetterSerialisable("Hello", "My Address", "01865 787878");
        $object2 = new PublicGetterSerialisable("Hello", "My new Address", "01865 787879");

        $differingMembers = ObjectUtils::getDifferingMembers($object, $object2);
        $this->assertEquals(array("address" => array("first" => "My Address", "second" => "My new Address"),
            "telephone" => array("first" => "01865 787878", "second" => "01865 787879")), $differingMembers);

    }


    public function testCanLimitToMembersToConsiderByPassingArrayToGetDifferingMembers() {
        $object = new PublicGetterSerialisable("Hello", "My Address", "01865 787878");
        $object2 = new PublicGetterSerialisable("Hello", "My new Address", "01865 787879");

        $differingMembers = ObjectUtils::getDifferingMembers($object, $object2, array("address"));
        $this->assertEquals(array("address" => array("first" => "My Address", "second" => "My new Address")), $differingMembers);
    }

    public function testCanGetDifferingMembersRecursivelyIfRecursiveSet() {

        $object = new PublicGetterSerialisable("Hello", new PublicGetterSerialisable("Bong", "Bing", "01676 989898"), "01865 787878");
        $object2 = new PublicGetterSerialisable("Hello", new PublicGetterSerialisable("Bongo", "Binger", "01676 989898"), "01865 787879");

        $differingMembers = ObjectUtils::getDifferingMembers($object, $object2, null, true);
        $this->assertEquals(array("address.name" => array("first" => "Bong", "second" => "Bongo"), "address.address" => array("first" => "Bing", "second" => "Binger"),
            "telephone" => array("first" => "01865 787878", "second" => "01865 787879")), $differingMembers);

        // Test limited members array
        $differingMembers = ObjectUtils::getDifferingMembers($object, $object2, array("address.address", "telephone"), true);
        $this->assertEquals(array("address.address" => array("first" => "Bing", "second" => "Binger"),
            "telephone" => array("first" => "01865 787878", "second" => "01865 787879")), $differingMembers);


        $object = new PublicGetterSerialisable("Hello", array(new PublicGetterSerialisable("Bong", "Bing", "01676 989898"), new PublicGetterSerialisable("Bongo", "Binger", "01676 989898")), "01865 787878");
        $object2 = new PublicGetterSerialisable("Hello", array(new PublicGetterSerialisable("Bong", "Ping", "01676 989898"), new PublicGetterSerialisable("Pongo", "Binger", "01676 989898")), "01865 787879");

        $differingMembers = ObjectUtils::getDifferingMembers($object, $object2, null, true);
        $this->assertEquals(array("address[0].address" => array("first" => "Bing", "second" => "Ping"), "address[1].name" => array("first" => "Bongo", "second" => "Pongo"),
            "telephone" => array("first" => "01865 787878", "second" => "01865 787879")), $differingMembers);


        // Test one with limited array of


    }


    public function testCanGetDifferingMembersStringUsingFormatStringAndSeparator() {

        $object = new PublicGetterSerialisable("Hello", "My Address", "01865 787878");
        $object2 = new PublicGetterSerialisable("Hello", "My new Address", "01865 787879");

        $formatString = "{member} changed from {first} to {second}";
        $separator = ", ";

        $differingMembersString = ObjectUtils::getDifferingMembersFormattedString($object, $object2, $formatString, $separator);

        $this->assertEquals("address changed from My Address to My new Address, telephone changed from 01865 787878 to 01865 787879", $differingMembersString);

    }


    public function testCanUseArrayOfMemberDisplayKeysToEnhanceFormatString() {
        $object = new PublicGetterSerialisable("Hello", "My Address", "01865 787878");
        $object2 = new PublicGetterSerialisable("Hello", "My new Address", "01865 787879");

        $formatString = "{member} changed from {first} to {second}";
        $separator = ", ";

        $displayKeys = array("address" => "Address", "telephone" => "Tel No.");

        $differingMembersString = ObjectUtils::getDifferingMembersFormattedString($object, $object2, $formatString, $separator, $displayKeys);

        $this->assertEquals("Address changed from My Address to My new Address, Tel No. changed from 01865 787878 to 01865 787879", $differingMembersString);

    }


}