<?php


namespace Kinikit\Core\DependencyInjection;


use Kinikit\Core\Configuration\Configuration;

class InterfaceResolverTest extends \PHPUnit\Framework\TestCase {


    public function testAttemptToGetCurrentlyConfiguredInterfaceClassForInterfaceWithNoConfigurationsThrowsException() {

        try {
            $interfaceResolver = Container::instance()->get(InterfaceResolver::class);
            $interfaceResolver->getCurrentlyConfiguredImplementationClass(InterfaceNoDefault::class);
            $this->fail("Should have thrown here");

        } catch (MissingInterfaceImplementationException $e) {
            // Success
            $this->assertTrue(true);
        }

    }

    public function testCanGetCurrentlyConfiguredInterfaceClassWhereConfigurationsHaveBeenSuppliedAsAnnotations() {

        $interfaceResolver = Container::instance()->get(InterfaceResolver::class);

        Configuration::instance()->removeParameter("interface.class");
        $implementation = $interfaceResolver->getCurrentlyConfiguredImplementationClass(InterfaceWithMappings::class);
        $this->assertEquals(ImplementationMapping1::class, $implementation);

        Configuration::instance()->addParameter("interface.class", "second");
        $implementation = $interfaceResolver->getCurrentlyConfiguredImplementationClass(InterfaceWithMappings::class);
        $this->assertEquals(ImplementationMapping2::class, $implementation);

        Configuration::instance()->addParameter("interface.class", "first");
        $implementation = $interfaceResolver->getCurrentlyConfiguredImplementationClass(InterfaceWithMappings::class);
        $this->assertEquals(ImplementationMapping1::class, $implementation);

        // Also try concrete class in config.
        Configuration::instance()->addParameter("interface.class", ImplementationMapping2::class);
        $implementation = $interfaceResolver->getCurrentlyConfiguredImplementationClass(InterfaceWithMappings::class);
        $this->assertEquals(ImplementationMapping2::class, $implementation);

    }


    public function testCanGetImplementationClassForKeyIfExists() {

        $interfaceResolver = Container::instance()->get(InterfaceResolver::class);

        // If key passed as null, assume default
        $this->assertEquals(ImplementationMapping1::class, $interfaceResolver->getImplementationClassForKey(InterfaceWithMappings::class));
        $this->assertEquals(ImplementationMapping1::class, $interfaceResolver->getImplementationClassForKey(InterfaceWithMappings::class, "first"));
        $this->assertEquals(ImplementationMapping2::class, $interfaceResolver->getImplementationClassForKey(InterfaceWithMappings::class, "second"));
        $this->assertEquals(ImplementationMapping2::class, $interfaceResolver->getImplementationClassForKey(InterfaceWithMappings::class, ImplementationMapping2::class));


        try {
            $interfaceResolver->getImplementationClassForKey(InterfaceNoDefault::class);
            $this->fail("Should have thrown here");
        } catch (MissingInterfaceImplementationException $e) {
            // Success
        }

        try {
            $interfaceResolver->getImplementationClassForKey(InterfaceWithMappings::class, "third");
            $this->fail("Should have thrown here");
        } catch (MissingInterfaceImplementationException $e) {
            // Success
        }


    }


    public function testCanAddImplementationClassForKey() {

        /**
         * @var InterfaceResolver $interfaceResolver
         */
        $interfaceResolver = Container::instance()->get(InterfaceResolver::class);

        // Check non exists to start with
        try {
            $interfaceResolver->getImplementationClassForKey(InterfaceWithMappings::class, "thirdimplementation");
            $this->fail("Should have thrown here");
        } catch (MissingInterfaceImplementationException $e) {
            // Success
        }

        $interfaceResolver->addImplementationClassForKey(InterfaceWithMappings::class, "thirdimplementation", ImplementationMapping3::class);


        $this->assertEquals(ImplementationMapping3::class, $interfaceResolver->getImplementationClassForKey(InterfaceWithMappings::class, "thirdimplementation"));

    }

}
