<?php

namespace Kinikit\Core\Testing;

use Kinikit\Core\Binding\SimpleNullableObject;
use Kinikit\Core\DependencyInjection\Container;
use Kinikit\Core\DependencyInjection\SecondaryService;
use Kinikit\Core\DependencyInjection\SimpleService;

include_once "autoloader.php";

/**
 * Test cases for
 *
 * Class MockObjectProviderTest
 */
class MockObjectProviderTest extends \PHPUnit\Framework\TestCase {

    /**
     * @var MockObjectProvider
     */
    private $mockObjectProvider;

    /**
     *  Set up
     */
    public function setUp(): void {
        $this->mockObjectProvider = Container::instance()->get(MockObjectProvider::class);
    }

    public function testCanGetMockInstanceForSimpleClassAsExtendedClassWithMockObjectTrait() {

        $mockSimpleService = $this->mockObjectProvider->getMockInstance(SimpleService::class);
        $this->assertEquals("Kinikit\Core\DependencyInjection\SimpleServiceMock", get_class($mockSimpleService));
        $this->assertTrue($mockSimpleService instanceof SimpleService);
        $this->assertTrue(in_array("Kinikit\Core\Testing\MockObject", class_uses($mockSimpleService)));

    }


    public function testMockDependenciesAreInjectedForClassWithDependencies() {

        $mockSecondaryService = $this->mockObjectProvider->getMockInstance(SecondaryService::class);
        $this->assertEquals("Kinikit\Core\DependencyInjection\SecondaryServiceMock", get_class($mockSecondaryService));
        $this->assertTrue($mockSecondaryService instanceof SecondaryService);
        $this->assertTrue(in_array("Kinikit\Core\Testing\MockObject", class_uses($mockSecondaryService)));


        // Check that a mock simple service was injected.
        $mockSimpleService = $this->mockObjectProvider->getMockInstance(SimpleService::class);
//        $this->assertEquals(new \Kinikit\Core\DependencyInjection\SecondaryServiceMock($mockSimpleService), $mockSecondaryService);

    }

    public function testCanMockAClassWithNullableProperties(){
        $mockObject = $this->mockObjectProvider->getMockInstance(SimpleNullableObject::class);
        $mockObject->returnValue("getYear", 100);
        $this->assertEquals(100, $mockObject->getYear());
    }


}
