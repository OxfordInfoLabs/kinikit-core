<?php

namespace Kinikit\Core\Asynchronous;

use Kinikit\Core\Annotation\TestAnnotatedClass;
use Kinikit\Core\DependencyInjection\Container;
use Kinikit\Core\Exception\NoneExistentClassException;
use Kinikit\Core\Exception\NoneExistentMethodException;
use Kinikit\Core\Exception\WrongParametersException;
use Kinikit\Core\Reflection\TestAnnotatedPOPO;
use Kinikit\Core\Reflection\TestPropertyPOPO;
use Kinikit\Core\Testing\MockObjectProvider;

include "autoloader.php";

class AsynchronousClassMethodTest extends \PHPUnit\Framework\TestCase {


    /**
     * @doesNotPerformAssertions
     */
    public function testExceptionsRaisedIfClassOrMethodDoesNotExistOrIfParametersAreWrongType() {

        try {
            new AsynchronousClassMethod("IDontExist", "nothere", []);
            $this->fail("Should have thrown here");
        } catch (NoneExistentClassException $e) {
        }

        try {
            new AsynchronousClassMethod(TestAnnotatedClass::class, "nothere", []);
            $this->fail("Should have thrown here");
        } catch (NoneExistentMethodException $e) {
        }

        try {
            new AsynchronousClassMethod(TestPropertyPOPO::class, "setWithSetter", [
                "withSetter" => "Hello world"
            ]);
            $this->fail("Should have thrown here");
        } catch (WrongParametersException $e) {
        }
    }


    public function testParameterTypesCorrectlyDerivedOnConstruction() {

        $asynchronous = new AsynchronousClassMethod(TestPropertyPOPO::class, "setWithSetter", [
            "withSetter" => new TestAnnotatedPOPO(1, "Hello")
        ]);

        $this->assertEquals(["withSetter" => TestAnnotatedPOPO::class], $asynchronous->getParameterTypes());


        $asynchronous = new AsynchronousClassMethod(TestPropertyPOPO::class, "setWithSetterAndGetter", [
            "withSetterAndGetter" => "bingo"
        ]);

        $this->assertEquals(["withSetterAndGetter" => "string"], $asynchronous->getParameterTypes());

    }


    public function testReturnTypeCorrectlySetOnConstruction(){
        $asynchronous = new AsynchronousClassMethod(TestPropertyPOPO::class, "getWithGetter", [
        ]);

        $this->assertEquals("string", $asynchronous->getReturnValueType());

    }


    public function testClassMethodIsCalledCorrectlyOnRunWithSimpleParams() {

        $mockObject = MockObjectProvider::instance()->getMockInstance(TestAnnotatedClass::class);
        Container::instance()->set(get_class($mockObject), $mockObject);

        // Create a new class method function
        $asynchronous = new AsynchronousClassMethod(get_class($mockObject), "setTag", ["tag" => "BINGO"]);

        $asynchronous->run();

        $this->assertTrue($mockObject->methodWasCalled("setTag", ["BINGO"]));

    }


}