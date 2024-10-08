<?php

namespace Kinikit\Core\DependencyInjection;

use Kinikit\Core\Configuration\Configuration;

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


    public function testGlobalObjectInterceptorsAreCalledOnObjectCreation() {
        $container = new Container();
        $methodInterceptor = new TestContainerInterceptor();
        $container->addInterceptor($methodInterceptor);

        $complexService = $container->get(SecondaryService::class);

        $this->assertTrue(in_array(SecondaryService::class . "Proxy", $methodInterceptor->afterCreates));
    }


    public function testGlobalObjectInterceptorsAreCalledForPreAndPostMethodCallsAndOnExceptions() {

        $container = new Container();
        $methodInterceptor = new TestContainerInterceptor();
        $container->addInterceptor($methodInterceptor);

        $complexService = $container->get("Kinikit\Core\DependencyInjection\SecondaryService");

        // Get a title
        $complexService->ok();

        $this->assertEquals(1, count($methodInterceptor->beforeCalls));
        $this->assertEquals(array("Kinikit\Core\DependencyInjection\SecondaryServiceProxy", "ok"), $methodInterceptor->beforeCalls[0]);

        $this->assertEquals(1, count($methodInterceptor->afterCalls));
        $this->assertEquals(array("Kinikit\Core\DependencyInjection\SecondaryServiceProxy", "ok"), $methodInterceptor->afterCalls[0]);


        try {
            $complexService->throwException();
            $this->fail("Should have thrown here");
        } catch (\Exception $e) {

            $this->assertEquals(1, count($methodInterceptor->exceptionCalls));
            $this->assertEquals(array("Kinikit\Core\DependencyInjection\SecondaryServiceProxy", "throwException"), $methodInterceptor->exceptionCalls[0]);


        }
    }

    public function testCanAddInterceptorToSelectiveClassesIdentifiedAsArrayOfClassNames() {

        $container = new Container();
        $methodInterceptor = new TestContainerInterceptor();
        $container->addInterceptor($methodInterceptor, [SecondaryService::class]);

        $secondaryService = $container->get(SecondaryService::class);

        // Get a title
        $secondaryService->ok();

        $this->assertEquals(1, count($methodInterceptor->beforeCalls));
        $this->assertEquals(array(SecondaryService::class . "Proxy", "ok"), $methodInterceptor->beforeCalls[0]);

        $this->assertEquals(1, count($methodInterceptor->afterCalls));
        $this->assertEquals(array(SecondaryService::class . "Proxy", "ok"), $methodInterceptor->afterCalls[0]);


        // Now call an unattached one

        $simpleService = $container->get(SimpleService::class);

        // Get a title
        $simpleService->getName();

        // Check no additional calls
        $this->assertEquals(1, count($methodInterceptor->beforeCalls));
        $this->assertEquals(1, count($methodInterceptor->afterCalls));


    }


    public function testClassesWithNoProxyAnnotationAreNotProxied() {

        $container = new Container();
        $instance = $container->get(NonProxyService::class);

        $this->assertEquals("Kinikit\Core\DependencyInjection\NonProxyService", get_class($instance));

    }


    public function testCanExplicitlyMapInterfaceToImplementation() {

        $container = new Container();
        $container->addClassMapping(InterfaceNoDefault::class, ImplementationNoDefault::class);

        $implementation = $container->get(InterfaceNoDefault::class);
        $this->assertEquals(new ImplementationNoDefault(), $implementation);
    }


    public function testAttemptToConstructAnInterfaceWithoutExplicitOrDefaultImplementationThrowsException() {

        try {
            $container = new Container();
            $container->get(InterfaceNoDefault::class);
            $this->fail("Should have thrown here");

        } catch (MissingInterfaceImplementationException $e) {
            // Success
            $this->assertTrue(true);
        }

    }

    public function testCanImplicitlyMapInterfaceToImplementationViaAnnotations() {

        $container = new Container();
        $implementation = $container->get(InterfaceWithMappings::class);
        $this->assertEquals(new ImplementationMapping1(), $implementation);

        Configuration::instance()->addParameter("interface.class", "second");
        $container = new Container();
        $implementation = $container->get(InterfaceWithMappings::class);
        $this->assertEquals(new ImplementationMapping2(), $implementation);

        Configuration::instance()->addParameter("interface.class", "first");
        $container = new Container();
        $implementation = $container->get(InterfaceWithMappings::class);
        $this->assertEquals(new ImplementationMapping1(), $implementation);

        // Also try concrete class in config.
        Configuration::instance()->addParameter("interface.class", ImplementationMapping2::class);
        $container = new Container();
        $implementation = $container->get(InterfaceWithMappings::class);
        $this->assertEquals(new ImplementationMapping2(), $implementation);

    }


    public function testCanCreateNewInstances() {

        $container = new Container();

        // Add a couple of test cases
        $container->addClassMapping(InterfaceNoDefault::class, ImplementationNoDefault::class);
        Configuration::instance()->addParameter("interface.class", "second");

        // Check an interface one.
        $firstInstance = $container->new(InterfaceNoDefault::class);
        $this->assertTrue($firstInstance instanceof ImplementationNoDefault);
        $secondInstance = $container->new(InterfaceNoDefault::class);
        $this->assertTrue($secondInstance instanceof ImplementationNoDefault);
        $this->assertFalse($firstInstance === $secondInstance);

        // Mapped one next
        $firstInstance = $container->new(InterfaceWithMappings::class);
        $this->assertTrue($firstInstance instanceof ImplementationMapping2);
        $secondInstance = $container->new(InterfaceWithMappings::class);
        $this->assertTrue($secondInstance instanceof ImplementationMapping2);
        $this->assertFalse($firstInstance === $secondInstance);

        // Regular one finally
        $firstInstance = $container->new(SimpleService::class);
        $this->assertTrue($firstInstance instanceof SimpleService);
        $secondInstance = $container->new(SimpleService::class);
        $this->assertTrue($secondInstance instanceof SimpleService);
        $this->assertFalse($firstInstance === $secondInstance);

    }


    public function testCanGetInterfaceImplementationsUsingKeysAndTheseReturnSingletons() {

        $container = new Container();

        $firstInstance = $container->getInterfaceImplementation(InterfaceWithMappings::class, "first");
        $this->assertEquals(new ImplementationMapping1(), $firstInstance);
        $firstInstance2 = $container->getInterfaceImplementation(InterfaceWithMappings::class, "first");
        $this->assertTrue($firstInstance === $firstInstance2);


        $secondInstance = $container->getInterfaceImplementation(InterfaceWithMappings::class, "second");
        $this->assertEquals(new ImplementationMapping2(), $secondInstance);
        $secondInstance2 = $container->getInterfaceImplementation(InterfaceWithMappings::class, "second");
        $this->assertTrue($secondInstance === $secondInstance2);


    }

    public function testCanSetNewInterfaceImplementationOnContainerForKey() {

        $container = new Container();
        $container->addInterfaceImplementation(InterfaceWithMappings::class, "third", ImplementationMapping3::class);

        $thirdInstance = $container->getInterfaceImplementation(InterfaceWithMappings::class, "third");
        $this->assertEquals(new ImplementationMapping3(), $thirdInstance);
        $thirdInstance2 = $container->getInterfaceImplementation(InterfaceWithMappings::class, "third");
        $this->assertTrue($thirdInstance === $thirdInstance2);

    }


}
