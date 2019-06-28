<?php

namespace Kinikit\Core\Binding;

use Kinikit\Core\Exception\ObjectBindingException;
use Kinikit\Core\Reflection\ClassInspectorProvider;

include "autoloader.php";

/**
 * Binder tests for object binding.
 *
 * Class ObjectBinderTest
 */
class ObjectBinderTest extends \PHPUnit\Framework\TestCase {

    /**
     * @var ObjectBinder
     */
    private $objectBinder;

    public function setUp(): void {
        parent::setUp();
        $this->objectBinder = new ObjectBinder(new ClassInspectorProvider());
    }

    public function testCanBindSimpleArrayToGetterSetterObject() {

        $data = array("name" => "Marky Marky", "age" => 16, "dob" => "23/01/2016");
        $obj = $this->objectBinder->bindFromArray($data, SimpleGetterSetterObj::class);

        $this->assertTrue($obj instanceof SimpleGetterSetterObj);
        $this->assertEquals("Marky Marky", $obj->getName());
        $this->assertEquals(16, $obj->getAge());
        $this->assertEquals("23/01/2016", $obj->getDob());


        $obj = $this->objectBinder->bindFromArray($data, SimpleStrongTypeGetterSetterObj::class);

        $this->assertTrue($obj instanceof SimpleStrongTypeGetterSetterObj);
        $this->assertEquals("Marky Marky", $obj->getName());
        $this->assertEquals(16, $obj->getAge());
        $this->assertEquals("23/01/2016", $obj->getDob());


    }

    public function testIfWrongTypeSuppliedToStronglyTypedSetterExceptionIsRaised() {

        $data = array("name" => 11, "age" => "33 Years Old", "dob" => "23/01/2016");

        try {
            $this->objectBinder->bindFromArray($data, SimpleStrongTypeGetterSetterObj::class);
            $this->fail("Should have thrown here");
        } catch (ObjectBindingException $e) {
            // Success
        }

        $this->assertTrue(true);

    }


    public function testCanBindSimpleArrayToConstructorObject() {

        $data = array("name" => "Marky Marky", "age" => 16, "dob" => "23/01/2016");
        $obj = $this->objectBinder->bindFromArray($data, SimpleConstructorObject::class);

        $this->assertTrue($obj instanceof SimpleConstructorObject);
        $this->assertEquals("Marky Marky", $obj->name);
        $this->assertEquals(16, $obj->age);
        $this->assertEquals("23/01/2016", $obj->dob);


        $obj = $this->objectBinder->bindFromArray($data, SimpleStrongTypeConstructorObject::class);

        $this->assertTrue($obj instanceof SimpleStrongTypeConstructorObject);
        $this->assertEquals("Marky Marky", $obj->name);
        $this->assertEquals(16, $obj->age);
        $this->assertEquals("23/01/2016", $obj->dob);

    }


    public function testIfInsufficientConstructorArgumentsSuppliedToLooseConstructorNullsAreSupplied() {

        $data = array("name" => "Marky Marky", "age" => 16);
        $obj = $this->objectBinder->bindFromArray($data, SimpleConstructorObject::class);

        $this->assertTrue($obj instanceof SimpleConstructorObject);
        $this->assertEquals("Marky Marky", $obj->name);
        $this->assertEquals(16, $obj->age);
        $this->assertNull($obj->dob);


        $data = array("dob" => "23/01/2016");
        $obj = $this->objectBinder->bindFromArray($data, SimpleConstructorObject::class);

        $this->assertTrue($obj instanceof SimpleConstructorObject);
        $this->assertNull($obj->name);
        $this->assertNull($obj->age);
        $this->assertEquals("23/01/2016", $obj->dob);

    }


    public function testIfInsufficientOrBadlyTypedConstructorArgumentsSuppliedToStrictConstructorExceptionsAreThrown() {

        $data = array("name" => "Marky Marky", "dob" => "23/01/2016");

        try {
            $this->objectBinder->bindFromArray($data, SimpleStrongTypeConstructorObject::class);
            $this->fail("Should have thrown here");
        } catch (ObjectBindingException $e) {
            // Success
        }


        $data = array("name" => 11, "age" => "33 Years Old", "dob" => "23/01/2016");

        try {
            $this->objectBinder->bindFromArray($data, SimpleStrongTypeConstructorObject::class);
            $this->fail("Should have thrown here");
        } catch (ObjectBindingException $e) {
            // Success
        }


        $this->assertTrue(true);


    }


    public function testCanBindArraysOfSimpleObjectsAndArrayKeysArePreserved() {

        $data = [
            ["name" => "Marky Marky", "age" => 16, "dob" => "23/01/2016"],
            ["name" => "Peter Rabbit", "age" => 8, "dob" => "01/01/2017"],
            ["name" => "David Suchet", "age" => 24, "dob" => "23/01/1982"]
        ];

        $results = $this->objectBinder->bindFromArray($data, SimpleConstructorObject::class . "[]");

        $this->assertEquals(new SimpleConstructorObject("Marky Marky", 16, "23/01/2016"), $results[0]);
        $this->assertEquals(new SimpleConstructorObject("Peter Rabbit", 8, "01/01/2017"), $results[1]);
        $this->assertEquals(new SimpleConstructorObject("David Suchet", 24, "23/01/1982"), $results[2]);


        $data = [
            "Test" => $data,
            "Live" => array_reverse($data)
        ];


        $results = $this->objectBinder->bindFromArray($data, SimpleConstructorObject::class . "[string][]");

        $this->assertEquals(new SimpleConstructorObject("Marky Marky", 16, "23/01/2016"), $results["Test"][0]);
        $this->assertEquals(new SimpleConstructorObject("Peter Rabbit", 8, "01/01/2017"), $results["Test"][1]);
        $this->assertEquals(new SimpleConstructorObject("David Suchet", 24, "23/01/1982"), $results["Test"][2]);

    }

    public function testCanBindCompoundObjectsWithObjectMembers() {

        $data = [
            "simpleObject" => ["name" => "Marky Marky", "age" => 16, "dob" => "23/01/2016"],
            "title" => "Bingo",
            "otherObjs" => [
                "Set" => [
                    ["name" => "David Suchet", "age" => 24, "dob" => "23/01/1982"]
                ]
            ],
            "games" => ["tennis", "golf", "football"]
        ];

        $results = $this->objectBinder->bindFromArray($data, ComplexObject::class);

        $expectedSimpleObject = new SimpleGetterSetterObj();
        $expectedSimpleObject->setName("Marky Marky");
        $expectedSimpleObject->setAge(16);
        $expectedSimpleObject->setDob("23/01/2016");

        $expectedOtherObjs = ["Set" => [new SimpleConstructorObject("David Suchet", 24, "23/01/1982")]];

        $this->assertTrue($results instanceof ComplexObject);
        $this->assertEquals($expectedSimpleObject, $results->getSimpleObject());
        $this->assertEquals("Bingo", $results->getTitle());
        $this->assertEquals($expectedOtherObjs, $results->getOtherObjs());
        $this->assertEquals(array("tennis", "golf", "football"), $results->getGames());


    }


}
