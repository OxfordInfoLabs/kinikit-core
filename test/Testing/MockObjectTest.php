<?php


namespace Kinikit\Core\Testing;


use http\Exception\BadMethodCallException;
use Kinikit\Core\Binding\SimpleNullableObject;
use Kinikit\Core\DependencyInjection\Container;
use Kinikit\Core\DependencyInjection\SimpleService;
use Kinikit\Core\Exception\AccessDeniedException;
use Kinikit\Core\Exception\ItemNotFoundException;
use Kinikit\Core\Exception\NoneExistentMethodException;
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


    public function testCanProgramAndReprogramMethodReturnValuesAndExceptions(){


        $mockObject = $this->mockObjectProvider->getMockInstance(SimpleService::class);

        // Set to an exception
        $mockObject->throwException("echoParams", new AccessDeniedException());

        try {
            $mockObject->echoParams(1, 2, 3, 4);
            $this->fail("Should have thrown here");
        } catch (AccessDeniedException $e) {
            // Success
        }


        // Set to a return value
        $mockObject->returnValue("echoParams", "BOSHER");
        $this->assertEquals("BOSHER", $mockObject->echoParams(1, 2, 3, 4));


        // Set back to an exception
        $mockObject->throwException("echoParams", new AccessDeniedException());

        try {
            $mockObject->echoParams(1, 2, 3, 4);
            $this->fail("Should have thrown here");
        } catch (AccessDeniedException $e) {
            // Success
        }

    }


    public function testCanGetMethodCallHistory() {

        $mockObject = $this->mockObjectProvider->getMockInstance(SimpleService::class);
        $mockObject->echoParams(1, 2, 3, 4);

        $this->assertEquals([[1, 2, 3, 4]], $mockObject->getMethodCallHistory("echoParams"));

        $mockObject->echoParams("Mark", "Luke", "John", "Steve");

        $this->assertEquals([[1, 2, 3, 4], ["Mark", "Luke", "John", "Steve"]], $mockObject->getMethodCallHistory("echoParams"));


    }


    public function testAttemptToProgramOrCallNoneExistentMethodsThrowsException() {

        $mockObject = $this->mockObjectProvider->getMockInstance(SimpleService::class);

        try {
            $mockObject->returnValue("iDontExist", 12345);
            $this->fail("Should have thrown here");
        } catch (NoneExistentMethodException $e) {
            $this->assertTrue(true);
        }

        try {
            $mockObject->throwException("iDontExist", new \Exception("Test Exception"));
            $this->fail("Should have thrown here");
        } catch (NoneExistentMethodException $e) {
            $this->assertTrue(true);
        }

        try {
            $mockObject->myDummyFunction("Hello");
            $this->fail("Should have thrown here");
        } catch (NoneExistentMethodException $e) {
            $this->assertTrue(true);
        }

    }


    public function testAttemptToProgramOrCallNonExistentMethodsWhereMagicCallFunctionExistsSucceed() {
        $mockObject = $this->mockObjectProvider->getMockInstance(SimpleServiceWithCallMethod::class);

        $mockObject->returnValue("iDontExist", 12345);
        $mockObject->throwException("iDontExist", new \Exception("Test Exception"));
        $mockObject->myDummyFunction("Hello");

        $this->assertTrue(true);
    }

    public function testAbleToMockObjectWithNullableArguments(){
        $mockObject = $this->mockObjectProvider->getMockInstance(SimpleNullableObject::class);

        try{
            $mockObject->returnValue("getHat", "Sunhat");
            $this->fail(); // Simple nullable obj has no such method
        } catch (NoneExistentMethodException $e){
            //Success
        }


        $mockObject->returnValue("getYear", 1970);

        $this->assertEquals(1970, $mockObject->getYear());

    }


}
