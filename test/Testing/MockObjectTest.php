<?php


namespace Kinikit\Core\Testing;


use http\Exception\BadMethodCallException;
use Kinikit\Core\DependencyInjection\Container;
use Kinikit\Core\DependencyInjection\SimpleService;
use Kinikit\Core\Exception\AccessDeniedException;
use Kinikit\Core\Exception\ItemNotFoundException;
use Kinikit\Core\Exception\StatusException;
use Kinikit\Core\Exception\WrongParametersException;

include_once "autoloader.php";

class MockObjectTest extends \PHPUnit\Framework\TestCase {

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

    public function testByDefaultMockObjectsDoNothingWhenCalled() {

        $mockObject = $this->mockObjectProvider->getMockInstance(SimpleService::class);

        $this->assertEquals(null, $mockObject->echoParams(1, 2, 3, 4));
        $this->assertEquals(null, $mockObject->getName());
    }


    public function testCanProgramMethodReturnValues() {

        $mockObject = $this->mockObjectProvider->getMockInstance(SimpleService::class);
        $mockObject->returnValue("echoParams", "BOSHER");

        $this->assertEquals("BOSHER", $mockObject->echoParams(1, 2, 3, 4));
        $this->assertEquals("BOSHER", $mockObject->echoParams("a", "n", "c", "d"));


        $mockObject->returnValue("echoParams", "BINGO", [1, 2, 3, 4]);
        $this->assertEquals("BINGO", $mockObject->echoParams(1, 2, 3, 4));
        $this->assertEquals("BOSHER", $mockObject->echoParams("a", "n", "c", "d"));


        $mockObject->returnValue("echoParams", "HEYDUDE", [new SimpleService(), new SimpleService(), 2, 3]);
        $this->assertEquals("HEYDUDE", $mockObject->echoParams(new SimpleService(), new SimpleService(), 2, 3));


    }

    public function testCanProgramMethodExceptions() {

        $mockObject = $this->mockObjectProvider->getMockInstance(SimpleService::class);
        $mockObject->throwException("echoParams", new AccessDeniedException());

        try {
            $mockObject->echoParams(1, 2, 3, 4);
            $this->fail("Should have thrown here");
        } catch (AccessDeniedException $e) {
            // Success
        }

        try {
            $mockObject->echoParams("a", "n", "c", "d");
            $this->fail("Should have thrown here");
        } catch (AccessDeniedException $e) {
            // Success
        }


        $mockObject->throwException("echoParams", new ItemNotFoundException(), [1, 2, 3, 4]);


        try {
            $mockObject->echoParams(1, 2, 3, 4);
            $this->fail("Should have thrown here");
        } catch (ItemNotFoundException $e) {
            // Success
        }

        try {
            $mockObject->echoParams("a", "n", "c", "d");
            $this->fail("Should have thrown here");
        } catch (AccessDeniedException $e) {
            // Success
        }


        $mockObject->throwException("echoParams", new WrongParametersException(), [new SimpleService(), new SimpleService(), 2, 3]);

        try {
            $mockObject->echoParams(new SimpleService(), new SimpleService(), 2, 3);
            $this->fail("Should have thrown here");
        } catch (WrongParametersException $e) {
            // Success
        }

        $this->assertTrue(true);
    }

}
