<?php


namespace Kinikit\Core\Reflection;

use Kinikit\Core\Annotation\ClassAnnotationParser;

include_once 'autoloader.php';

class PropertyTest extends \PHPUnit\Framework\TestCase {


    public function testCanConstructProperty() {

        $classInspector = new ClassInspector(TestTypedPOPO::class);
        $reflectionProperty = $classInspector->getReflectionClass()->getProperty("id");
        $propertyAnnotations = ClassAnnotationParser::instance()->parse(TestTypedPOPO::class)->getFieldAnnotations()["id"];

        $property = new Property($reflectionProperty, $propertyAnnotations, $classInspector);

        $this->assertEquals("id", $property->getPropertyName());
        $this->assertEquals($propertyAnnotations, $property->getPropertyAnnotations());
        $this->assertEquals($reflectionProperty, $property->getReflectionProperty());
        $this->assertEquals($classInspector, $property->getDeclaringClassInspector());
        $this->assertEquals(Property::VISIBILITY_PRIVATE, $property->getVisibility());
        $this->assertEquals("int", $property->getType());


        $reflectionProperty = $classInspector->getReflectionClass()->getProperty("name");
        $propertyAnnotations = ClassAnnotationParser::instance()->parse(TestTypedPOPO::class)->getFieldAnnotations()["name"];

        $property = new Property($reflectionProperty, $propertyAnnotations, $classInspector);

        $this->assertEquals("name", $property->getPropertyName());
        $this->assertEquals($propertyAnnotations, $property->getPropertyAnnotations());
        $this->assertEquals($reflectionProperty, $property->getReflectionProperty());
        $this->assertEquals($classInspector, $property->getDeclaringClassInspector());
        $this->assertEquals(Property::VISIBILITY_PRIVATE, $property->getVisibility());
        $this->assertEquals("string", $property->getType());

        $reflectionProperty = $classInspector->getReflectionClass()->getProperty("dob");
        $propertyAnnotations = ClassAnnotationParser::instance()->parse(TestTypedPOPO::class)->getFieldAnnotations()["dob"];

        $property = new Property($reflectionProperty, $propertyAnnotations, $classInspector);

        $this->assertEquals("dob", $property->getPropertyName());
        $this->assertEquals($propertyAnnotations, $property->getPropertyAnnotations());
        $this->assertEquals($reflectionProperty, $property->getReflectionProperty());
        $this->assertEquals($classInspector, $property->getDeclaringClassInspector());
        $this->assertEquals(Property::VISIBILITY_PROTECTED, $property->getVisibility());
        $this->assertEquals("mixed", $property->getType());


        $reflectionProperty = $classInspector->getReflectionClass()->getProperty("publicPOPO");
        $propertyAnnotations = ClassAnnotationParser::instance()->parse(TestTypedPOPO::class)->getFieldAnnotations()["publicPOPO"];

        $property = new Property($reflectionProperty, $propertyAnnotations, $classInspector);

        $this->assertEquals("publicPOPO", $property->getPropertyName());
        $this->assertEquals($propertyAnnotations, $property->getPropertyAnnotations());
        $this->assertEquals($reflectionProperty, $property->getReflectionProperty());
        $this->assertEquals($classInspector, $property->getDeclaringClassInspector());
        $this->assertEquals(Property::VISIBILITY_PUBLIC, $property->getVisibility());
        $this->assertEquals("\\" . TestTypedPOPO::class, $property->getType());

    }

}
