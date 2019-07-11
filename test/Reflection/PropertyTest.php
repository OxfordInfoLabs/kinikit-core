<?php


namespace Kinikit\Core\Reflection;

use Exception;
use Kinikit\Core\Annotation\ClassAnnotationParser;
use Kinikit\Core\Exception\WrongPropertyTypeException;


include_once 'autoloader.php';

class PropertyTest extends \PHPUnit\Framework\TestCase {


    public function testCanConstructProperty() {

        $classInspector = new ClassInspector(TestTypedPOPO::class);
        $reflectionProperty = $classInspector->getReflectionClass()->getProperty("id");
        $propertyAnnotations = (new ClassAnnotationParser())->parse(TestTypedPOPO::class)->getFieldAnnotations()["id"];

        $property = new Property($reflectionProperty, $propertyAnnotations, $classInspector);

        $this->assertEquals("id", $property->getPropertyName());
        $this->assertEquals($propertyAnnotations, $property->getPropertyAnnotations());
        $this->assertEquals($reflectionProperty, $property->getReflectionProperty());
        $this->assertEquals($classInspector, $property->getDeclaringClassInspector());
        $this->assertEquals(Property::VISIBILITY_PRIVATE, $property->getVisibility());
        $this->assertEquals("int", $property->getType());


        $reflectionProperty = $classInspector->getReflectionClass()->getProperty("name");
        $propertyAnnotations = (new ClassAnnotationParser())->parse(TestTypedPOPO::class)->getFieldAnnotations()["name"];

        $property = new Property($reflectionProperty, $propertyAnnotations, $classInspector);

        $this->assertEquals("name", $property->getPropertyName());
        $this->assertEquals($propertyAnnotations, $property->getPropertyAnnotations());
        $this->assertEquals($reflectionProperty, $property->getReflectionProperty());
        $this->assertEquals($classInspector, $property->getDeclaringClassInspector());
        $this->assertEquals(Property::VISIBILITY_PRIVATE, $property->getVisibility());
        $this->assertEquals("string", $property->getType());

        $reflectionProperty = $classInspector->getReflectionClass()->getProperty("dob");
        $propertyAnnotations = (new ClassAnnotationParser())->parse(TestTypedPOPO::class)->getFieldAnnotations()["dob"];

        $property = new Property($reflectionProperty, $propertyAnnotations, $classInspector);

        $this->assertEquals("dob", $property->getPropertyName());
        $this->assertEquals($propertyAnnotations, $property->getPropertyAnnotations());
        $this->assertEquals($reflectionProperty, $property->getReflectionProperty());
        $this->assertEquals($classInspector, $property->getDeclaringClassInspector());
        $this->assertEquals(Property::VISIBILITY_PROTECTED, $property->getVisibility());
        $this->assertEquals("mixed", $property->getType());


        $reflectionProperty = $classInspector->getReflectionClass()->getProperty("publicPOPO");
        $propertyAnnotations = (new ClassAnnotationParser())->parse(TestTypedPOPO::class)->getFieldAnnotations()["publicPOPO"];

        $property = new Property($reflectionProperty, $propertyAnnotations, $classInspector);

        $this->assertEquals("publicPOPO", $property->getPropertyName());
        $this->assertEquals($propertyAnnotations, $property->getPropertyAnnotations());
        $this->assertEquals($reflectionProperty, $property->getReflectionProperty());
        $this->assertEquals($classInspector, $property->getDeclaringClassInspector());
        $this->assertEquals(Property::VISIBILITY_PUBLIC, $property->getVisibility());
        $this->assertEquals("\\" . TestTypedPOPO::class, $property->getType());

    }


    public function testCanSetPropertiesProvidedTypeIsRight() {


        $classInspector = new ClassInspector(TestPropertyPOPO::class);

        $testPOPO = new TestPropertyPOPO(99);

        $reflectionProperty = $classInspector->getReflectionClass()->getProperty("hidden");
        $propertyAnnotations = (new ClassAnnotationParser())->parse(TestPropertyPOPO::class)->getFieldAnnotations()["hidden"];
        $property = new Property($reflectionProperty, $propertyAnnotations, $classInspector);

        try {
            $property->set($testPOPO, true);
            $this->fail("Should have thrown here");
        } catch (WrongPropertyTypeException $e) {
            // As expected
        }

        $property->set($testPOPO, "My Little Pony");
        $this->assertEquals("My Little Pony", $reflectionProperty->getValue($testPOPO));


        $reflectionProperty = $classInspector->getReflectionClass()->getProperty("withSetter");
        $propertyAnnotations = (new ClassAnnotationParser())->parse(TestPropertyPOPO::class)->getFieldAnnotations()["withSetter"];
        $property = new Property($reflectionProperty, $propertyAnnotations, $classInspector);

        try {
            $property->set($testPOPO, true);
            $this->fail("Should have thrown here");
        } catch (WrongPropertyTypeException $e) {
            // As expected
        }

        try {
            $property->set($testPOPO, new TestTypedPOPO(1, "Me"));
            $this->fail("Should have thrown here");
        } catch (WrongPropertyTypeException $e) {
            // As expected
        }

        $property->set($testPOPO, new TestAnnotatedPOPO(1, "Bingo"));
        $this->assertEquals(new TestAnnotatedPOPO(1, "Bingo"), $reflectionProperty->getValue($testPOPO));

    }


    public function testCanGetProperties() {


        $classInspector = new ClassInspector(TestPropertyPOPO::class);

        $testPOPO = new TestPropertyPOPO(99);

        $reflectionProperty = $classInspector->getReflectionClass()->getProperty("constructorOnly");
        $propertyAnnotations = (new ClassAnnotationParser())->parse(TestPropertyPOPO::class)->getFieldAnnotations()["constructorOnly"];
        $property = new Property($reflectionProperty, $propertyAnnotations, $classInspector);
        $this->assertEquals(99, $property->get($testPOPO));


        $reflectionProperty = $classInspector->getReflectionClass()->getProperty("withSetter");
        $propertyAnnotations = (new ClassAnnotationParser())->parse(TestPropertyPOPO::class)->getFieldAnnotations()["withSetter"];
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($testPOPO, new TestAnnotatedPOPO(1, "Mark"));

        $property = new Property($reflectionProperty, $propertyAnnotations, $classInspector);
        $this->assertEquals(new TestAnnotatedPOPO(1, "Mark"), $property->get($testPOPO));

    }


}
