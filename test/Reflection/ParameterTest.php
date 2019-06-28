<?php


namespace Kinikit\Core\Reflection;

/**
 * Class ParameterTest
 * @package Kinikit\Core\Reflection
 */
class ParameterTest extends \PHPUnit\Framework\TestCase {

    public function testParameterMappedCorrectlyForStronglyTypedParameters() {

        $classInspector = new ClassInspector(TestTypedPOPO::class);

        $methodInspector = $classInspector->getPublicMethod("__construct");
        $constructorParams = $methodInspector->getReflectionMethod()->getParameters();

        $parameter = new Parameter($constructorParams[0], $methodInspector);

        $this->assertEquals("id", $parameter->getName());
        $this->assertEquals("int", $parameter->getType());
        $this->assertNull($parameter->getDefaultValue());
        $this->assertTrue($parameter->isRequired());
        $this->assertTrue($parameter->isPrimitive());

        $parameter = new Parameter($constructorParams[1], $methodInspector);
        $this->assertEquals("name", $parameter->getName());
        $this->assertEquals("string", $parameter->getType());
        $this->assertNull($parameter->getDefaultValue());
        $this->assertTrue($parameter->isRequired());
        $this->assertTrue($parameter->isPrimitive());


        $parameter = new Parameter($constructorParams[2], $methodInspector);
        $this->assertEquals("dob", $parameter->getName());
        $this->assertEquals("string", $parameter->getType());
        $this->assertEquals("01/01/2016", $parameter->getDefaultValue());
        $this->assertFalse($parameter->isRequired());
        $this->assertTrue($parameter->isPrimitive());

        $methodInspector = $classInspector->getPublicMethod("clone");
        $methodParams = $methodInspector->getReflectionMethod()->getParameters();

        $parameter = new Parameter($methodParams[0], $methodInspector);

        $this->assertEquals("otherPOPO", $parameter->getName());
        $this->assertEquals("\\" . TestTypedPOPO::class, $parameter->getType());
        $this->assertNull($parameter->getDefaultValue());
        $this->assertTrue($parameter->isRequired());
        $this->assertFalse($parameter->isPrimitive());

    }


    public function testParameterMappedCorrectlyForAnnotatedParameters() {

        $classInspector = new ClassInspector(TestAnnotatedPOPO::class);

        $methodInspector = $classInspector->getPublicMethod("__construct");
        $constructorParams = $methodInspector->getReflectionMethod()->getParameters();

        $parameter = new Parameter($constructorParams[0], $methodInspector);

        $this->assertEquals("id", $parameter->getName());
        $this->assertEquals("int", $parameter->getType());
        $this->assertNull($parameter->getDefaultValue());
        $this->assertTrue($parameter->isRequired());
        $this->assertTrue($parameter->isPrimitive());

        $parameter = new Parameter($constructorParams[1], $methodInspector);
        $this->assertEquals("name", $parameter->getName());
        $this->assertEquals("string", $parameter->getType());
        $this->assertNull($parameter->getDefaultValue());
        $this->assertTrue($parameter->isRequired());
        $this->assertTrue($parameter->isPrimitive());


        $parameter = new Parameter($constructorParams[2], $methodInspector);
        $this->assertEquals("dob", $parameter->getName());
        $this->assertEquals("string", $parameter->getType());
        $this->assertEquals("01/01/2016", $parameter->getDefaultValue());
        $this->assertFalse($parameter->isRequired());
        $this->assertTrue($parameter->isPrimitive());

        $methodInspector = $classInspector->getPublicMethod("clone");
        $methodParams = $methodInspector->getReflectionMethod()->getParameters();

        $parameter = new Parameter($methodParams[0], $methodInspector);

        $this->assertEquals("otherPOPO", $parameter->getName());
        $this->assertEquals("\\" . TestAnnotatedPOPO::class, $parameter->getType());
        $this->assertNull($parameter->getDefaultValue());
        $this->assertTrue($parameter->isRequired());
        $this->assertFalse($parameter->isPrimitive());

    }


}
