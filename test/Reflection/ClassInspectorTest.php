<?php

namespace Kinikit\Core\Reflection;

use Kinikit\Core\Annotation\ClassAnnotationParser;
use Kinikit\Core\Binding\ComplexObject;
use Kinikit\Core\DependencyInjection\SecondaryService;
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
            "ContainerInterceptor" => "\Kinikit\Core\DependencyInjection\ContainerInterceptor"], $classInspector->getDeclaredNamespaceClasses());


        $parser = new ClassAnnotationParser();

        $this->assertEquals($parser->parse(TestTypedPOPO::class)->getClassAnnotations(), $classInspector->getClassAnnotations());

    }

    public function testDeclaredNamespaceClassesAlsoIncludeNamespacesFromParentClassesAndTraits() {

        $classInspector = new ClassInspector(TestExtendedPOPO::class);
        $this->assertEquals(["Annotation" => "\Kinikit\Core\Annotation\Annotation"], $classInspector->getDeclaredNamespaceClasses());

        $classInspector = new ClassInspector(TestTraitedPOPO::class);
        $this->assertEquals(["Annotation" => "\Kinikit\Core\Annotation\Annotation"], $classInspector->getDeclaredNamespaceClasses());

    }


    public function testCanGetConstructorAndPublicMethodsForTypedClass() {

        $classInspector = new ClassInspector(TestTypedPOPO::class);

        $reflectionClass = new \ReflectionClass(TestTypedPOPO::class);
        $parser = new ClassAnnotationParser();

        $annotations = $parser->parse(TestTypedPOPO::class);

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
        $annotations = (new ClassAnnotationParser())->parse(TestTypedPOPO::class);

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

        $classInspector = new ClassInspector(TestNullableTypedPOPO::class);


        //TODO Nullable params don't need to be passed in
//        try {
//            $classInspector->createInstance([]);
//            $this->fail("Should have thrown here");
//        } catch (InsufficientParametersException $e){
//            // Success
//        }

        //NOTE: Integers are typecast to strings when passed into createInstance
        $instance1 = $classInspector->createInstance(["hat"=> "Sunhat", ["left sock", "right sock"]]);
        $instance2 = $classInspector->createInstance(["hat"=> null, ["left sock", "right sock"]]);

        $instance1->setHat(null);

        $this->assertEquals($instance1, $instance2);

    }

    public function testCanCreateClassInspectorFromNullableClass(){
        $classInspector = new ClassInspector(TestTypedPOPO::class);

        $classInspectorNullable = new ClassInspector("?" . TestTypedPOPO::class);
        $splitClass = explode("\\", TestTypedPOPO::class);
        $unqualifiedClass = array_pop($splitClass);
        $namespace = join("\\", $splitClass);
        $classInspectorNullable2 = new ClassInspector($namespace . "\\?" . $unqualifiedClass);

        $this->assertEquals($classInspector, $classInspectorNullable);
        $this->assertEquals($classInspector, $classInspectorNullable2);
    }


    public function testCanSetPropertyDataOneAtATimePublicOnly() {

        $classInspector = new ClassInspector(TestPropertyPOPO::class);
        $testPropertyPOPO = new TestPropertyPOPO(33);

        // Non public members
        $classInspector->setPropertyData($testPropertyPOPO, "Monkey", "hidden");
        $this->assertFalse(isset($testPropertyPOPO->returnData()["hidden"]));

        $classInspector->setPropertyData($testPropertyPOPO, 77, "constructorOnly");
        $this->assertEquals(33, $testPropertyPOPO->returnData()["constructorOnly"]);

        $classInspector->setPropertyData($testPropertyPOPO, "Monkey", "withGetter");
        $this->assertFalse(isset($testPropertyPOPO->returnData()["withGetter"]));


        // Setter members
        $classInspector->setPropertyData($testPropertyPOPO, new TestAnnotatedPOPO(44, "Mark"), "withSetter");
        $this->assertFalse(isset($testPropertyPOPO->returnData()["withSetter"]));
        $this->assertEquals(new TestAnnotatedPOPO(44, "Mark"), $testPropertyPOPO->returnSetterValues()["withSetter"]);


        $classInspector->setPropertyData($testPropertyPOPO, "BINGO", "withSetterAndGetter");
        $this->assertFalse(isset($testPropertyPOPO->returnData()["withSetterAndGetter"]));
        $this->assertEquals("BINGO", $testPropertyPOPO->returnSetterValues()["withSetterAndGetter"]);


        // Public member
        $classInspector->setPropertyData($testPropertyPOPO, new TestTypedPOPO(44, "BIGBOY"), "writable");
        $this->assertEquals(new TestTypedPOPO(44, "BIGBOY"), $testPropertyPOPO->writable);
    }


    public function testCanSetPropertyDataOneAtATimeNotPublicOnly() {

        $classInspector = new ClassInspector(TestPropertyPOPO::class);
        $testPropertyPOPO = new TestPropertyPOPO(33);

        // Non public members
        $classInspector->setPropertyData($testPropertyPOPO, "Monkey", "hidden", false);
        $this->assertEquals("Monkey", $testPropertyPOPO->returnData()["hidden"]);

        $classInspector->setPropertyData($testPropertyPOPO, 77, "constructorOnly", false);
        $this->assertEquals(77, $testPropertyPOPO->returnData()["constructorOnly"]);

        $classInspector->setPropertyData($testPropertyPOPO, "Monkey", "withGetter", false);
        $this->assertEquals("Monkey", $testPropertyPOPO->returnData()["withGetter"]);


        // Setter members
        $classInspector->setPropertyData($testPropertyPOPO, new TestAnnotatedPOPO(44, "Mark"), "withSetter", false);
        $this->assertFalse(isset($testPropertyPOPO->returnData()["withSetter"]));
        $this->assertEquals(new TestAnnotatedPOPO(44, "Mark"), $testPropertyPOPO->returnSetterValues()["withSetter"]);


        $classInspector->setPropertyData($testPropertyPOPO, "BINGO", "withSetterAndGetter", false);
        $this->assertFalse(isset($testPropertyPOPO->returnData()["withSetterAndGetter"]));
        $this->assertEquals("BINGO", $testPropertyPOPO->returnSetterValues()["withSetterAndGetter"]);


        // Public member
        $classInspector->setPropertyData($testPropertyPOPO, new TestTypedPOPO(44, "BIGBOY"), "writable", false);
        $this->assertEquals(new TestTypedPOPO(44, "BIGBOY"), $testPropertyPOPO->writable);
    }


    public function testCanSetPropertyDataInBulk() {

        $classInspector = new ClassInspector(TestPropertyPOPO::class);
        $testPropertyPOPO = new TestPropertyPOPO(33);

        $data = array("hidden" => "BADBOY", "constructorOnly" => 22, "withGetter" => "Badger", "withSetter" => new TestAnnotatedPOPO(11, "Badger"),
            "withSetterAndGetter" => "MARKO", "writable" => new TestTypedPOPO(11, "MARKO"));


        // Set public only first
        $classInspector->setPropertyData($testPropertyPOPO, $data);

        $this->assertEquals(["hidden" => null, "constructorOnly" => 33, "withGetter" => null, "withSetter" => null, "withSetterAndGetter" => null, "writable" => new TestTypedPOPO(11, "MARKO")], $testPropertyPOPO->returnData());
        $this->assertEquals(["withSetter" => new TestAnnotatedPOPO(11, "Badger"),
            "withSetterAndGetter" => "MARKO"], $testPropertyPOPO->returnSetterValues());


        // Now set private as well
        $testPropertyPOPO = new TestPropertyPOPO(33);
        $classInspector->setPropertyData($testPropertyPOPO, $data, null, false);


        $this->assertEquals(["hidden" => "BADBOY", "constructorOnly" => 22, "withGetter" => "Badger", "withSetter" => null, "withSetterAndGetter" => null, "writable" => new TestTypedPOPO(11, "MARKO")], $testPropertyPOPO->returnData());
        $this->assertEquals(["withSetter" => new TestAnnotatedPOPO(11, "Badger"),
            "withSetterAndGetter" => "MARKO"], $testPropertyPOPO->returnSetterValues());


    }


    public function testCanGetPropertyDataOneAtATime() {

        $classInspector = new ClassInspector(TestPropertyPOPO::class);
        $testPropertyPOPO = new TestPropertyPOPO(33);
        $testPropertyPOPO->writable = new TestTypedPOPO(3, "Marky");

        $this->assertEquals(null, $classInspector->getPropertyData($testPropertyPOPO, "constructorOnly"));
        $this->assertEquals(new TestTypedPOPO(3, "Marky"), $classInspector->getPropertyData($testPropertyPOPO, "writable"));
        $this->assertEquals("GETTER_CALLED", $classInspector->getPropertyData($testPropertyPOPO, "withGetter"));
        $this->assertEquals("GETTER_CALLED", $classInspector->getPropertyData($testPropertyPOPO, "withSetterAndGetter"));


        $this->assertEquals(33, $classInspector->getPropertyData($testPropertyPOPO, "constructorOnly", false));
        $this->assertEquals(new TestTypedPOPO(3, "Marky"), $classInspector->getPropertyData($testPropertyPOPO, "writable", false));
        $this->assertEquals("GETTER_CALLED", $classInspector->getPropertyData($testPropertyPOPO, "withGetter", false));
        $this->assertEquals("GETTER_CALLED", $classInspector->getPropertyData($testPropertyPOPO, "withSetterAndGetter", false));


    }


    public function testCanGetPropertyDataInBulk() {

        $classInspector = new ClassInspector(TestPropertyPOPO::class);
        $testPropertyPOPO = new TestPropertyPOPO(33);
        $testPropertyPOPO->writable = new TestTypedPOPO(3, "Marky");

        // Public only
        $bulkData = $classInspector->getPropertyData($testPropertyPOPO);
        $this->assertEquals(["withGetter" => "GETTER_CALLED", "withSetterAndGetter" => "GETTER_CALLED", "writable" => new TestTypedPOPO(3, "Marky")], $bulkData);


        // With private data
        $bulkData = $classInspector->getPropertyData($testPropertyPOPO, null, false);
        $this->assertEquals(["hidden" => null, "constructorOnly" => 33, "withGetter" => "GETTER_CALLED",
            "withSetter" => null, "withSetterAndGetter" => "GETTER_CALLED", "writable" => new TestTypedPOPO(3, "Marky"),
            "__setterValues" => []], $bulkData);


    }


    public function testCanConstructWithFilenameInstead() {

        $classInspector = new ClassInspector("Binding/ComplexObject.php");
        $this->assertEquals(new ClassInspector(ComplexObject::class), $classInspector);

        $classInspector = new ClassInspector("DependencyInjection/SecondaryService.php");
        $this->assertEquals(new ClassInspector(SecondaryService::class), $classInspector);

    }


    public function testConstructorHandlesSamePropertyNamesWithKeyExtension() {

        $classInspector = new ClassInspector(TestPOPOSimilarProperties::class);
        $constructorParameters = $classInspector->getConstructor()->getParameters();

        $this->assertEquals(2, sizeof($constructorParameters));
        $this->assertEquals("nameKey", $constructorParameters[0]->getName());
        $this->assertEquals("string", $constructorParameters[0]->getType());
        $this->assertEquals("name", $constructorParameters[1]->getName());
        $this->assertEquals("string", $constructorParameters[1]->getType());


    }

}
