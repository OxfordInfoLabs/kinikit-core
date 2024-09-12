<?php

namespace Kinikit\Core\Logging;

use Kinikit\Core\Reflection\TestAttributePOPO;
use PHPUnit\Framework\TestCase;

include_once "autoloader.php";

class FileLoggingProviderTest extends TestCase {

    /**
     * @var FileLoggingProvider
     */
    private FileLoggingProvider $logger;

    public function setUp(): void {
        parent::setUp();
        passthru("rm -rf /tmp/ooacorelog.log");

        $this->logger = new FileLoggingProvider();
    }

    public function testCanLogSimpleGeneralStringsDirectlyToLogFile() {

        $this->logger->log("Hello World");
        $this->assertEquals(date("d/m/Y H:i:s") . "\tDEBUG\tHello World", trim(file_get_contents("/tmp/ooacorelog.log")));

        $this->logger->log("Gumdrop");
        $this->assertEquals(date("d/m/Y H:i:s") . "\tDEBUG\tHello World\n" . date("d/m/Y H:i:s") . "\tDEBUG\tGumdrop", trim(file_get_contents("/tmp/ooacorelog.log")));

    }

    public function testCanLogStringsWithCustomCategoryToLogFile() {
        $this->logger->log("Custom Test", 0);
        $this->assertEquals(date("d/m/Y H:i:s") . "\tEMERGENCY\tCustom Test", trim(file_get_contents("/tmp/ooacorelog.log")));

        $this->logger->log("Another One", 3);
        $this->assertEquals(date("d/m/Y H:i:s") . "\tEMERGENCY\tCustom Test\n" . date("d/m/Y H:i:s") . "\tERROR\tAnother One", trim(file_get_contents("/tmp/ooacorelog.log")));
    }

    public function testCanLogExceptionsDirectlyAndTheseGetLoggedAsErrors() {
        $this->logger->logException(new \Exception("Test exception"));
        $this->assertEquals(date("d/m/Y H:i:s") . "\tWARNING\tException\nTest exception", trim(file_get_contents("/tmp/ooacorelog.log")));
    }

    public function testArraysAreLoggedUsingVarExportOnNewLine() {
        $this->logger->logArray([1, 2, 3, 4, 5]);
        $this->assertEquals(date("d/m/Y H:i:s") . "\tDEBUG\tArray\n" . var_export([1, 2, 3, 4, 5], true), trim(file_get_contents("/tmp/ooacorelog.log")));
    }

    public function testObjectsAreLoggedCorrectly() {
        $obj = new TestAttributePOPO(3, "John");
        $this->logger->logObject($obj);
        $this->assertEquals(date("d/m/Y H:i:s") ."\tDEBUG\tKinikit\Core\Reflection\TestAttributePOPO\n\Kinikit\Core\Reflection\TestAttributePOPO::__set_state(array(\n   'id' => 3,\n   'name' => 'John',\n   'dob' => '01/01/2016',\n   'publicPOPO' => NULL,\n))", trim(file_get_contents("/tmp/ooacorelog.log")));
    }

}