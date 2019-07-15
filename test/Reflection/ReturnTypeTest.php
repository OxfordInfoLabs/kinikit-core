<?php


namespace Kinikit\Core\Reflection;


class ReturnTypeTest extends \PHPUnit\Framework\TestCase {


    public function testReturnTypeMappedCorrectlyForStronglyTypedMethods() {

        $classInspector = new ClassInspector(TestTypedPOPO::class);

        $methodInspector = $classInspector->getPublicMethod("__construct");

        $returnType = new ReturnType($methodInspector);
        $this->assertEquals("void", $returnType->getType());
        $this->assertFalse($returnType->isExplicitlyTyped());


        $methodInspector = $classInspector->getPublicMethod("getName");

        $returnType = new ReturnType($methodInspector);
        $this->assertEquals("string", $returnType->getType());
        $this->assertTrue($returnType->isExplicitlyTyped());


        $methodInspector = $classInspector->getPublicMethod("setName");

        $returnType = new ReturnType($methodInspector);
        $this->assertEquals("void", $returnType->getType());
        $this->assertTrue($returnType->isExplicitlyTyped());

        $methodInspector = $classInspector->getPublicMethod("clone");

        $returnType = new ReturnType($methodInspector);
        $this->assertEquals("\\" . TestTypedPOPO::class, $returnType->getType());
        $this->assertTrue($returnType->isExplicitlyTyped());


    }


    public function testReturnTypeMappedCorrectlyForAnnotatedMethods() {

        $classInspector = new ClassInspector(TestAnnotatedPOPO::class);

        $methodInspector = $classInspector->getPublicMethod("__construct");

        $returnType = new ReturnType($methodInspector);
        $this->assertEquals("void", $returnType->getType());
        $this->assertFalse($returnType->isExplicitlyTyped());


        $methodInspector = $classInspector->getPublicMethod("getName");

        $returnType = new ReturnType($methodInspector);
        $this->assertEquals("string", $returnType->getType());
        $this->assertFalse($returnType->isExplicitlyTyped());


        $methodInspector = $classInspector->getPublicMethod("setName");

        $returnType = new ReturnType($methodInspector);
        $this->assertEquals("void", $returnType->getType());
        $this->assertFalse($returnType->isExplicitlyTyped());

        $methodInspector = $classInspector->getPublicMethod("clone");

        $returnType = new ReturnType($methodInspector);
        $this->assertEquals("\\" . TestAnnotatedPOPO::class, $returnType->getType());
        $this->assertFalse($returnType->isExplicitlyTyped());


    }


    public function testCanCheckWhetherReturnTypeIsInstanceOfAnotherReturnType() {

        $classInspector = new ClassInspector(TestExtendedPOPO::class);

        $methodInspector = $classInspector->getPublicMethod("clone");

        $returnType = new ReturnType($methodInspector);
        $this->assertEquals("\\" . TestExtendedPOPO::class, $returnType->getType());
        $this->assertTrue($returnType->isInstanceOf(TestAnnotatedPOPO::class));
        $this->assertTrue($returnType->isInstanceOf(TestExtendedPOPO::class));
        $this->assertFalse($returnType->isInstanceOf(TestPropertyPOPO::class));


    }

}
