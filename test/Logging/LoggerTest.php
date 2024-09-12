<?php

namespace Kinikit\Core\Logging;

use Kinikit\Core\DependencyInjection\Container;
use Kinikit\Core\Reflection\TestAttributePOPO;
use Kinikit\Core\Testing\MockObjectProvider;
use PHPUnit\Framework\TestCase;

include_once "autoloader.php";


/**
 * Created by PhpStorm.
 * User: mark
 * Date: 16/07/2014
 * Time: 10:37
 */
class LoggerTest extends TestCase {

    private LoggingProvider $mockLoggingProvider;

    private LoggingProvider $defaultLoggingProvider;

    public function setUp(): void {
        $this->mockLoggingProvider = MockObjectProvider::mock(LoggingProvider::class);

        $this->defaultLoggingProvider = Container::instance()->get(LoggingProvider::class);
        Container::instance()->set(LoggingProvider::class, $this->mockLoggingProvider);
    }

    public function tearDown(): void {
        Container::instance()->set(LoggingProvider::class, $this->defaultLoggingProvider);
    }

    public function testLoggerCallsCorrectFunctionForStrings() {
        Logger::log("A log");
        $this->assertTrue($this->mockLoggingProvider->methodWasCalled("log", ["A log", 7]));

        Logger::log("Another log", 5);
        $this->assertTrue($this->mockLoggingProvider->methodWasCalled("log", ["Another log", 5]));
    }

    public function testLoggerCallsCorrectFunctionForArrays() {
        Logger::log([1, 2, 3]);
        $this->assertTrue($this->mockLoggingProvider->methodWasCalled("logArray", [[1, 2, 3], 7]));

        Logger::log([4, 5, 6], 6);
        $this->assertTrue($this->mockLoggingProvider->methodWasCalled("logArray", [[4, 5, 6], 6]));
    }

    public function testLoggerCallsCorrectFunctionForObjects() {
        $obj1 = new TestAttributePOPO(1, "Steve");
        Logger::log($obj1);
        $this->assertTrue($this->mockLoggingProvider->methodWasCalled("logObject", [$obj1, 7]));

        $obj2 = new TestAttributePOPO(2, "Fred");
        Logger::log($obj2, 3);
        $this->assertTrue($this->mockLoggingProvider->methodWasCalled("logObject", [$obj2, 3]));
    }

    public function testLoggerCallsCorrectFunctionForExceptions() {
        $e1 = new \Exception("Uh oh!");
        Logger::log($e1);
        $this->assertTrue($this->mockLoggingProvider->methodWasCalled("logException", [$e1, 4]));

        $e2 = new TestException("Oh dear!");
        Logger::log($e2, 2);
        $this->assertTrue($this->mockLoggingProvider->methodWasCalled("logException", [$e2, 2]));
    }

}
