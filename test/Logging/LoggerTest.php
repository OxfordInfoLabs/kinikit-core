<?php

namespace Kinikit\Core\Logging;

use Kinikit\Core\DependencyInjection\Container;
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

    public function testLoggerGetsProviderAndCallsLog() {
        Logger::log("A log");
        $this->assertTrue($this->mockLoggingProvider->methodWasCalled("log", ["A log", 7]));

        Logger::log("Another log", 5);
        $this->assertTrue($this->mockLoggingProvider->methodWasCalled("log", ["Another log", 5]));
    }

}
