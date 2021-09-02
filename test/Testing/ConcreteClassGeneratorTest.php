<?php


namespace Kinikit\Core\Testing;

include_once "autoloader.php";

class ConcreteClassGeneratorTest extends \PHPUnit\Framework\TestCase {


    public function testCanGenerateConcreteClassFromAbstractClass() {

        $instance = ConcreteClassGenerator::instance()->generateInstance(TestAbstractClass::class, "Concrete");
        $this->assertTrue($instance instanceof TestAbstractClass);
        $this->assertEquals("Hello world", $instance->baseMethod());

        // Try second call to ensure we can do it twice
        $instance = ConcreteClassGenerator::instance()->generateInstance(TestAbstractClass::class, "Concrete");
        $this->assertTrue($instance instanceof TestAbstractClass);
        $this->assertEquals("Hello world", $instance->baseMethod());

    }

}