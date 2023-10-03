<?php

namespace Kinikit\Core\Binding;

use Kinikit\Core\Binding\ObjectBindingException;
use Kinikit\Core\Exception\StatusException;
use Kinikit\Core\Reflection\ClassInspectorProvider;
use Kinikit\Core\Reflection\TestAnnotatedPOPO;
use Kinikit\Core\Reflection\TestEnum;
use Kinikit\Core\Reflection\TestPropertyPOPO;
use Kinikit\Core\Reflection\TestTypedPOPO;

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


    public function testIfPublicOnlySetToFalsePrivateMembersAreBoundAsWell() {


        $instance = $this->objectBinder->bindFromArray(["hidden" => "23", "constructorOnly" => 33, "withGetter" => "Hello",
            "withSetter" => ["id" => 33, "name" => "Mark", "dob" => "01/01/1990"],
            "withSetterAndGetter" => "Bingo",
            "writable" => ["id" => 22, "name" => "John"]], TestPropertyPOPO::class);


        $this->assertEquals(["hidden" => null, "constructorOnly" => 33,
            "withGetter" => null, "withSetter" => null,
            "withSetterAndGetter" => null, "writable" => new TestTypedPOPO(22, "John")], $instance->returnData());


        // Now try one with private access allowed as well.

        $instance = $this->objectBinder->bindFromArray(["hidden" => "23", "constructorOnly" => 33, "withGetter" => "Hello",
            "withSetter" => ["id" => 33, "name" => "Mark", "dob" => "01/01/1990"],
            "withSetterAndGetter" => "Bingo",
            "writable" => ["id" => 22, "name" => "John"]], TestPropertyPOPO::class, false);

        $this->assertEquals(["hidden" => "23", "constructorOnly" => 33,
            "withGetter" => "Hello", "withSetter" => null,
            "withSetterAndGetter" => null, "writable" => new TestTypedPOPO(22, "John")], $instance->returnData());

    }


    public function testPrimitiveObjectsGetReturnedIntactWhenBindingToArray() {

        $this->assertEquals(true, $this->objectBinder->bindToArray(true));
        $this->assertEquals(11, $this->objectBinder->bindToArray(11));
        $this->assertEquals(0.75, $this->objectBinder->bindToArray(0.75));
        $this->assertEquals("hello", $this->objectBinder->bindToArray("hello"));


    }


    public function testCanBindSimpleObjectsToArrayInPublicOnlyMode() {

        $simpleObject = new SimpleGetterSetterObj();
        $simpleObject->setName("Bingo");
        $simpleObject->setAge(23);
        $simpleObject->setDob("06/12/1977");

        $this->assertEquals(array("name" => "Bingo", "age" => 23, "dob" => "06/12/1977"),
            $this->objectBinder->bindToArray($simpleObject));


        $constructorObject = new SimpleConstructorObject("David", 11, "01/01/2001");

        $this->assertEquals(array("name" => "David", "age" => 11, "dob" => "01/01/2001"),
            $this->objectBinder->bindToArray($constructorObject));


    }


    public function testCanBindArraysOfSimpleObjectsToArrayWithPreservedKeys() {

        $simpleObject = new SimpleGetterSetterObj();
        $simpleObject->setName("Bingo");
        $simpleObject->setAge(23);
        $simpleObject->setDob("06/12/1977");

        $simpleObject2 = new SimpleGetterSetterObj();
        $simpleObject2->setName("Bongo");
        $simpleObject2->setAge(49);
        $simpleObject2->setDob("01/01/1999");


        $objects = [$simpleObject, $simpleObject2];

        $array = $this->objectBinder->bindToArray($objects);

        $this->assertEquals([["name" => "Bingo", "age" => 23, "dob" => "06/12/1977"],
            ["name" => "Bongo", "age" => 49, "dob" => "01/01/1999"]], $array);


        $objects = ["first" => $simpleObject, "second" => $simpleObject2];

        $array = $this->objectBinder->bindToArray($objects);

        $this->assertEquals(["first" => ["name" => "Bingo", "age" => 23, "dob" => "06/12/1977"],
            "second" => ["name" => "Bongo", "age" => 49, "dob" => "01/01/1999"]], $array);


    }

    public function testCanBindNullableArguments(){
        $nullableObj1 = new SimpleNullableObject(2050, ["amazing party"]);
        $nullableObj2 = new SimpleNullableObject(null, ["cool party"]);

        $objects = [$nullableObj1, $nullableObj2];


        $array = $this->objectBinder->bindToArray($objects);

        $this->assertEquals(
            [
                ["year" => 2050, "parties" => ["amazing party"], "testPOPO" => null, "testTypedPopos"=>[]],
                ["year" => null, "parties" => ["cool party"], "testPOPO" => null, "testTypedPopos"=>[]]
            ], $array
        );

        $fromArrayObject = $this->objectBinder->bindFromArray(
            [
                "year" => 2050,
                "parties" => ["amazing party"]
            ], SimpleNullableObject::class);

        $this->assertEquals($fromArrayObject, $nullableObj1);

        $fromArrayObject = $this->objectBinder->bindFromArray(
            [
                "year" => 2050,
                "parties" => ["amazing party"],
                "testPOPO" => ["id"=> 4, "name"=> "Sam"],
                "testTypedPopos" => [["id"=>5, "name"=>"Sam"], ["id"=>6, "name"=>"Sam"]]
        ], SimpleNullableObject::class);

        $expectedFromArrayObject = new SimpleNullableObject(
            2050,
            ["amazing party"],
            new TestTypedPOPO(4, "Sam"),
            [
                new TestTypedPOPO(5, "Sam"),
                new TestTypedPOPO(6, "Sam")
            ]
        );
        $this->assertEquals($expectedFromArrayObject, $fromArrayObject);

        $bound = $this->objectBinder->bindFromArray(
            [
                [
                    "year" => 2050,
                    "parties" => ["amazing party"],
                    "testPOPO" => ["id"=> 4, "name"=> "Sam"]
                ],
                [
                    "year" => 2051,
                    "parties" => ["amazing party"],
                    "testPOPO" => ["id"=> 5, "name"=> "Sam"]
                ],

            ],
            SimpleNullableObject::class . "[]"
        );

        $expected = [
            new SimpleNullableObject(2050, ["amazing party"], new TestTypedPOPO(4, "Sam")),
            new SimpleNullableObject(2051, ["amazing party"], new TestTypedPOPO(5, "Sam")),
        ];
        $this->assertEquals($expected, $bound);
    }

    public function testCanBindEnums(){
        $off = TestEnum::OFF;
        $boundOff = $this->objectBinder->bindFromArray("OFF", TestEnum::class);
        $this->assertEquals($off, $boundOff);

        $offString = $this->objectBinder->bindToArray($off);
        $this->assertEquals("OFF", $offString);
    }

    public function testCanBindComplexObjectToArrayInPublicMode() {

        $simpleObject = new SimpleGetterSetterObj();
        $simpleObject->setName("Bingo");
        $simpleObject->setAge(23);
        $simpleObject->setDob("06/12/1977");

        $otherObjs = ["primary" => [new SimpleConstructorObject("Mark", 12, "01/01/1990")],
            "secondary" => [new SimpleConstructorObject("Mark", 22, "01/01/1999"),
                new SimpleConstructorObject("Luke", 33, "01/01/1985")]];


        $complexObject = new ComplexObject($simpleObject);
        $complexObject->setGames(array("hockey", "tennis", "football"));
        $complexObject->setTitle("Pineapple");
        $complexObject->setOtherObjs($otherObjs);
        $complexObject->setResource(fopen(__DIR__ . "/RecursiveObject.php", "r"));

        $array = $this->objectBinder->bindToArray($complexObject);


        $this->assertEquals(["title" => "Pineapple",
            "games" => ["hockey", "tennis", "football"],
            "simpleObject" => ["name" => "Bingo", "age" => 23, "dob" => "06/12/1977"],
            "otherObjs" => ["primary" => [["name" => "Mark", "age" => 12, "dob" => "01/01/1990"]],
                "secondary" => [["name" => "Mark", "age" => 22, "dob" => "01/01/1999"],
                    ["name" => "Luke", "age" => 33, "dob" => "01/01/1985"]]],
            "resource" => null], $array);
    }


    public function testCanBindPrivateMembersToArrayAsWellIfSpecified() {


        $instance = $this->objectBinder->bindFromArray(["hidden" => "23", "constructorOnly" => 33, "withGetter" => "Hello",
            "withSetter" => ["id" => 33, "name" => "Mark", "dob" => "01/01/1990"],
            "withSetterAndGetter" => "Bingo",
            "writable" => ["id" => 22, "name" => "John"]], TestPropertyPOPO::class, false);


        // Public only first
        $array = $this->objectBinder->bindToArray($instance);

        $this->assertEquals(["withGetter" => "GETTER_CALLED", "withSetterAndGetter" => "GETTER_CALLED", "writable" => [
            "id" => 22, "name" => "John", "special" => true, "publicPOPO" => null]], $array);


        // Private as well
        $array = $this->objectBinder->bindToArray($instance, false);

        $this->assertEquals(["hidden" => "23", "constructorOnly" => 33, "withSetter" => null, "withGetter" => "GETTER_CALLED", "withSetterAndGetter" => "GETTER_CALLED", "writable" => [
            "id" => 22, "name" => "John", "special" => true, "publicPOPO" => null, "dob" => "01/01/2016"],
            "__setterValues" => [
                "withSetter" => ["id" => 33, "name" => "Mark", "dob" => "01/01/1990", "special" => true],
                "withSetterAndGetter" => "Bingo"
            ]], $array);


    }


    public function testRecursiveObjectsAreNotBoundButReturnedAsNull() {

        $recursiveObject = new RecursiveObject(23);
        $recursiveObject->setSubObject($recursiveObject);

        $array = $this->objectBinder->bindToArray($recursiveObject);

        $this->assertEquals(["id" => 23, "subObject" => null], $array);


    }


}
