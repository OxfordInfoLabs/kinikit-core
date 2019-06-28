<?php

namespace Kinikit\Core\Reflection;

use Kinikit\Core\Annotation\ClassAnnotationParser;
use Kinikit\Core\Annotation\ClassAnnotations;
use Kinikit\Core\Exception\BadParameterException;
use Kinikit\Core\Exception\InsufficientParametersException;
use Kinikit\Core\Exception\WrongParametersException;

include_once "autoloader.php";

/**
 * Class inspector test cases
 *
 * Class ClassInspectorTest
 */
class ClassInspectorTest extends \PHPUnit\Framework\TestCase {

    public function testCanGetClassLevelAttributesFromInspector() {

        $classInspector = new ClassInspector(TestTypedPOPO::class);
        $this->assertEquals("\Kinikit\Core\Reflection\TestTypedPOPO", $classInspector->getClassName());
        $this->assertEquals("TestTypedPOPO", $classInspector->getShortClassName());
        $this->assertEquals("Kinikit\Core\Reflection", $classInspector->getNamespace());

        $this->assertEquals(["Annotation" => "\Kinikit\Core\Annotation\Annotation",
            "AccessDeniedException" => "\Kinikit\Core\Exception\AccessDeniedException",
            "ObjectInterceptor" => "\Kinikit\Core\DependencyInjection\ObjectInterceptor"], $classInspector->getDeclaredNamespaceClasses());


        $this->assertEquals(ClassAnnotationParser::instance()->parse(TestTypedPOPO::class)->getClassAnnotations(), $classInspector->getClassAnnotations());

    }


    public function testCanGetConstructorAndPublicMethodsForTypedClass() {

        $classInspector = new ClassInspector(TestTypedPOPO::class);

        $reflectionClass = new \ReflectionClass(TestTypedPOPO::class);
        $annotations = ClassAnnotationParser::instance()->parse(TestTypedPOPO::class);

        // Check constructor
        $this->assertEquals(new Method($reflectionClass->getConstructor(), $annotations->getMethodAnnotations()["__construct"], $classInspector), $classInspector->getConstructor());


        // Check public methods
        $publicMethods = $classInspector->getPublicMethods();
        $this->assertEquals(6, sizeof($publicMethods));
        $this->assertEquals(new Method($reflectionClass->getMethod("getId"), $annotations->getMethodAnnotations()["getId"], $classInspector), $publicMethods["getId"]);
        $this->assertEquals(new Method($reflectionClass->getMethod("getName"), $annotations->getMethodAnnotations()["getName"], $classInspector), $publicMethods["getName"]);
        $this->assertEquals(new Method($reflectionClass->getMethod("setName"), $annotations->getMethodAnnotations()["setName"], $classInspector), $publicMethods["setName"]);
        $this->assertEquals(new Method($reflectionClass->getMethod("setDob"), $annotations->getMethodAnnotations()["setDob"], $classInspector), $publicMethods["setDob"]);
        $this->assertEquals(new Method($reflectionClass->getMethod("clone"), $annotations->getMethodAnnotations()["clone"], $classInspector), $publicMethods["clone"]);
        $this->assertEquals(new Method($reflectionClass->getMethod("isSpecial"), $annotations->getMethodAnnotations()["isSpecial"], $classInspector), $publicMethods["isSpecial"]);


        $this->assertEquals(new Method($reflectionClass->getMethod("getId"), $annotations->getMethodAnnotations()["getId"], $classInspector), $classInspector->getPublicMethod("getId"));
        $this->assertEquals(new Method($reflectionClass->getMethod("getName"), $annotations->getMethodAnnotations()["getName"], $classInspector), $classInspector->getPublicMethod("getName"));
        $this->assertEquals(new Method($reflectionClass->getMethod("setName"), $annotations->getMethodAnnotations()["setName"], $classInspector), $classInspector->getPublicMethod("setName"));
        $this->assertEquals(new Method($reflectionClass->getMethod("setDob"), $annotations->getMethodAnnotations()["setDob"], $classInspector), $classInspector->getPublicMethod("setDob"));
        $this->assertEquals(new Method($reflectionClass->getMethod("clone"), $annotations->getMethodAnnotations()["clone"], $classInspector), $classInspector->getPublicMethod("clone"));


    }


    public function testCanGetGetterAndSetterMembersForClass() {

        $classInspector = new ClassInspector(TestTypedPOPO::class);
        $getters = $classInspector->getGetters();
        $this->assertEquals(3, sizeof($getters));
        $this->assertEquals("getId", $getters["id"]->getMethodName());
        $this->assertEquals("getName", $getters["name"]->getMethodName());
        $this->assertEquals("isSpecial", $getters["special"]->getMethodName());

        $setters = $classInspector->getSetters();
        $this->assertEquals(2, sizeof($setters));
        $this->assertEquals("setName", $setters["name"]->getMethodName());
        $this->assertEquals("setDob", $setters["dob"]->getMethodName());


    }


    public function testCanGetPropertiesOfClass() {
        $classInspector = new ClassInspector(TestTypedPOPO::class);
        $properties = $classInspector->getProperties();
        $annotations = ClassAnnotationParser::instance()->parse(TestTypedPOPO::class);

        $this->assertEquals(4, sizeof($properties));

        $this->assertEquals(new Property($classInspector->getReflectionClass()->getProperty("id"), $annotations->getFieldAnnotations()["id"], $classInspector), $properties["id"]);
        $this->assertEquals(new Property($classInspector->getReflectionClass()->getProperty("name"), $annotations->getFieldAnnotations()["name"], $classInspector), $properties["name"]);
        $this->assertEquals(new Property($classInspector->getReflectionClass()->getProperty("dob"), $annotations->getFieldAnnotations()["dob"], $classInspector), $properties["dob"]);
        $this->assertEquals(new Property($classInspector->getReflectionClass()->getProperty("publicPOPO"), $annotations->getFieldAnnotations()["publicPOPO"], $classInspector), $properties["publicPOPO"]);



    }


    public function testCanCreateInstanceOfClassProvidedAllRequiredArgumentsAreSupplied() {

        $classInspector = new ClassInspector(TestTypedPOPO::class);

        // Try missing params first
        try {
            $classInspector->createInstance([]);
            $this->fail("Should have thrown here");
        } catch (InsufficientParametersException $e) {
            // Success
        }

        // Try wrong types now
        try {
            $classInspector->createInstance(["name" => "mark", "id" => "Bad Type"]);
            $this->fail("Should have thrown here");
        } catch (WrongParametersException $e) {
            // Success
        }


        // Try successful ones
        $this->assertEquals(new TestTypedPOPO(11, "Mark"), $classInspector->createInstance(["id" => 11, "name" => "Mark"]));


    }


}
