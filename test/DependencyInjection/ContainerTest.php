<?php

namespace Kinikit\Core\DependencyInjection;

use Kinikit\Core\Exception\RecursiveDependencyException;
use PHPMailer\PHPMailer\Exception;

include_once "autoloader.php";

/**
 * Test cases for the container object.
 */
class ContainerTest extends \PHPUnit\Framework\TestCase {


    public function testCanCreateSimpleClassWithNoDependencies() {

        $simpleService = Container::instance()->get("Kinikit\Core\DependencyInjection\SimpleService");
        $this->assertTrue($simpleService instanceof SimpleService);
        $this->assertEquals("Hello wonderful world of fun", $simpleService->getName());
        $this->assertEquals(array("Bob", 1, 4, 9), $simpleService->echoParams("Bob", 1, 4, 9));


        // Check singleton behaviour
        $simpleService2 = Container::instance()->get("Kinikit\Core\DependencyInjection\SimpleService");
        $this->assertTrue($simpleService === $simpleService2);

    }


    public function testCannotCreateDeepClassWithInjectedRecursiveDependencies() {

        try {
            Container::instance()->get("Kinikit\Core\DependencyInjection\ComplexService");
            $this->fail("Should have thrown here");
        } catch (RecursiveDependencyException $e) {
            $this->assertTrue(true);
        }
    }


    public function testObjectInterceptorsAreCalledOnObjectCreation() {
        $container = new Container();
        $methodInterceptor = new TestObjectInterceptor();
        $container->addMethodInterceptor($methodInterceptor);

        $complexService = $container->get("Kinikit\Core\DependencyInjection\SecondaryService");

        $this->assertTrue(in_array("Kinikit\Core\DependencyInjection\SecondaryServiceProxy", $methodInterceptor->afterCreates));
    }


    public function testObjectInterceptorsAreCalledForPreAndPostMethodCallsAndOnExceptions() {

        $container = new Container();
        $methodInterceptor = new TestObjectInterceptor();
        $container->addMethodInterceptor($methodInterceptor);

        $complexService = $container->get("Kinikit\Core\DependencyInjection\SecondaryService");

        // Get a title
        $complexService->ok();

        $this->assertEquals(1, sizeof($methodInterceptor->beforeCalls));
        $this->assertEquals(array("Kinikit\Core\DependencyInjection\SecondaryServiceProxy", "ok"), $methodInterceptor->beforeCalls[0]);

        $this->assertEquals(1, sizeof($methodInterceptor->afterCalls));
        $this->assertEquals(array("Kinikit\Core\DependencyInjection\SecondaryServiceProxy", "ok"), $methodInterceptor->afterCalls[0]);


        try {
            $complexService->throwException();
            $this->fail("Should have thrown here");
        } catch (\Exception $e) {

            $this->assertEquals(1, sizeof($methodInterceptor->exceptionCalls));
            $this->assertEquals(array("Kinikit\Core\DependencyInjection\SecondaryServiceProxy", "throwException"), $methodInterceptor->exceptionCalls[0]);


        }
    }


}
