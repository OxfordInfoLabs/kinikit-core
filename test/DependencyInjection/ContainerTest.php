<?php

namespace Kinikit\Core\DependencyInjection;

use PHPMailer\PHPMailer\Exception;

include_once "autoloader.php";

/**
 * Test cases for the container object.
 */
class ContainerTest extends \PHPUnit\Framework\TestCase {

    public function testCanCreateSimpleClassWithNoDependencies() {

        $simpleService = Container::instance()->get("Kinikit\Core\DependencyInjection\SimpleService");
        $this->assertTrue($simpleService instanceof Proxy);
        $this->assertEquals(new SimpleService(), $simpleService->__getObject());
        $this->assertEquals("Hello wonderful world of fun", $simpleService->getName());
        $this->assertEquals(array("Bob", 1, 4, 9), $simpleService->echoParams("Bob", 1, 4, 9));


        // Check singleton behaviour
        $simpleService2 = Container::instance()->get("Kinikit\Core\DependencyInjection\SimpleService");
        $this->assertTrue($simpleService === $simpleService2);

    }


    public function testCanCreateDeepClassWithInjectedRecursiveDependencies() {

        $complexService = Container::instance()->get("Kinikit\Core\DependencyInjection\ComplexService");
        $this->assertTrue($complexService instanceof Proxy);
        $this->assertTrue($complexService->__getObject() instanceof ComplexService);
        $this->assertEquals("Hello wonderful world of fun", $complexService->getTitle());

        $this->assertEquals(Container::instance()->get("Kinikit\Core\DependencyInjection\SimpleService"), $complexService->getSimpleService());
        $this->assertEquals(Container::instance()->get("Kinikit\Core\DependencyInjection\SecondaryService"), $complexService->getSecondaryService());
        $this->assertEquals($complexService, $complexService->getSecondaryService()->getComplexService());

    }





    public function testObjectInterceptorsAreCalledOnObjectCreation() {
        $container = new Container();
        $methodInterceptor = new TestObjectInterceptor();
        $container->addMethodInterceptor($methodInterceptor);

        $complexService = $container->get("Kinikit\Core\DependencyInjection\ComplexService");

        $this->assertTrue(in_array("Kinikit\Core\DependencyInjection\ComplexService", $methodInterceptor->afterCreates));
    }


    public function testObjectInterceptorsAreCalledForPreAndPostMethodCallsAndOnExceptions() {

        $container = new Container();
        $methodInterceptor = new TestObjectInterceptor();
        $container->addMethodInterceptor($methodInterceptor);

        $complexService = $container->get("Kinikit\Core\DependencyInjection\ComplexService");

        // Get a title
        $complexService->getTitle();

        $this->assertEquals(1, sizeof($methodInterceptor->beforeCalls));
        $this->assertEquals(array("Kinikit\Core\DependencyInjection\ComplexService", "getTitle"), $methodInterceptor->beforeCalls[0]);

        $this->assertEquals(1, sizeof($methodInterceptor->afterCalls));
        $this->assertEquals(array("Kinikit\Core\DependencyInjection\ComplexService", "getTitle"), $methodInterceptor->afterCalls[0]);


        try {
            $complexService->getSecondaryService()->throwException();
            $this->fail("Should have thrown here");
        } catch (\Exception $e) {

            $this->assertEquals(1, sizeof($methodInterceptor->exceptionCalls));
            $this->assertEquals(array("Kinikit\Core\DependencyInjection\SecondaryService", "throwException"), $methodInterceptor->exceptionCalls[0]);


        }
    }


}
