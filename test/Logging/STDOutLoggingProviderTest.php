<?php

namespace Kinikit\Core\Logging;

use Kinikit\Core\Reflection\TestAttributePOPO;
use Kinikit\Core\Stream\StreamIntercept;
use PHPUnit\Framework\TestCase;

include_once "autoloader.php";

class STDOutLoggingProviderTest extends TestCase {

    private static $streamFilter;

    private STDOutLoggingProvider $logger;

    public static function setUpBeforeClass(): void {
        stream_filter_register("intercept", StreamIntercept::class);
        self::$streamFilter = stream_filter_append(STDOUT, "intercept");
    }

    public function setUp(): void {
        $this->logger = new STDOutLoggingProvider();
        StreamIntercept::$cache = '';
    }

    public static function tearDownAfterClass(): void {
        stream_filter_remove(self::$streamFilter);
    }

    public function testDoesSendSimpleMessageToSTDOUT() {

        $this->logger->log("A simple message");

        $this->assertEquals('{"severity":"Debug","message":"A simple message"}', StreamIntercept::$cache);

    }

    public function testDoesLogExceptionsCorrectly() {

        $e = new TestException("A bad thing happened!");
        $this->logger->logException($e);

        $this->assertEquals('{"severity":"Warning","message":"TestException: A bad thing happened!"}', StreamIntercept::$cache);

    }

    public function testDoesLogObjectsCorrectly() {

        $obj = new TestAttributePOPO(1, "Jim");
        $this->logger->logObject($obj);

        $this->assertEquals('{"severity":"Debug","message":"\\\Kinikit\\\Core\\\Reflection\\\TestAttributePOPO::__set_state(array(\n   \'id\' => 1,\n   \'name\' => \'Jim\',\n   \'dob\' => \'01\/01\/2016\',\n   \'publicPOPO\' => NULL,\n))"}', StreamIntercept::$cache);

    }

    public function testDoesLogArraysCorrectly() {

        $arr = ["apple", "banana", "carrot", 4];
        $this->logger->logArray($arr);

        $this->assertEquals('{"severity":"Debug","message":"array (\n  0 => \'apple\',\n  1 => \'banana\',\n  2 => \'carrot\',\n  3 => 4,\n)"}', StreamIntercept::$cache);

    }

}