<?php

namespace Kinikit\Core\Logging;

use Kinikit\Core\Configuration\Configuration;
use Kinikit\Core\Reflection\TestAttributePOPO;
use PHPUnit\Framework\TestCase;

include_once "autoloader.php";

class FileLoggingProviderTest extends TestCase {

    private FileLoggingProvider $logger;

    private $originalConfigFile;
    private $logPath = "/tmp/file_logging_test.log";

    public function setUp(): void {
        $this->originalConfigFile = getenv("KINIKIT_CONFIG_FILE");
        $this->assertNotFalse($this->originalConfigFile);
        // Use a config with logPath set
        putenv("KINIKIT_CONFIG_FILE=config.txt.test");
        Configuration::instance(true);
        passthru("rm -f $this->logPath");

        $this->logger = new FileLoggingProvider();
        parent::setUp();
    }
    protected function tearDown(): void {
        putenv("KINIKIT_CONFIG_FILE=$this->originalConfigFile");
        $this->assertSame($this->originalConfigFile, getenv("KINIKIT_CONFIG_FILE"));
        Configuration::instance(true);
        parent::tearDown();
    }

    public function testCanLogSimpleGeneralStringsDirectlyToLogFile() {

        $this->logger->log("Hello World");
        $this->assertEquals(date("d/m/Y H:i:s") . "\tDEBUG\tHello World", trim(file_get_contents($this->logPath)));

        $this->logger->log("Gumdrop");
        $this->assertEquals(date("d/m/Y H:i:s") . "\tDEBUG\tHello World\n" . date("d/m/Y H:i:s") . "\tDEBUG\tGumdrop", trim(file_get_contents($this->logPath)));

    }

    public function testCanLogStringsWithCustomCategoryToLogFile() {
        $this->logger->log("Custom Test", 0);
        $this->assertEquals(date("d/m/Y H:i:s") . "\tEMERGENCY\tCustom Test", trim(file_get_contents($this->logPath)));

        $this->logger->log("Another One", 3);
        $this->assertEquals(date("d/m/Y H:i:s") . "\tEMERGENCY\tCustom Test\n" . date("d/m/Y H:i:s") . "\tERROR\tAnother One", trim(file_get_contents($this->logPath)));
    }

    public function testCanLogExceptionsDirectlyAndTheseGetLoggedAsErrors() {
        $this->logger->log(new \Exception("Test exception"));
        $this->assertEquals(date("d/m/Y H:i:s") . "\tWARNING\tException\nTest exception", trim(file_get_contents($this->logPath)));
    }

    public function testArraysAreLoggedUsingVarExportOnNewLine() {
        $this->logger->log([1, 2, 3, 4, 5]);
        $this->assertEquals(date("d/m/Y H:i:s") . "\tDEBUG\tArray\n" . var_export([1, 2, 3, 4, 5], true), trim(file_get_contents($this->logPath)));
    }

    public function testObjectsAreLoggedCorrectly() {
        $obj = new TestAttributePOPO(3, "John");
        $this->logger->log($obj);
        $this->assertEquals(date("d/m/Y H:i:s") ."\tDEBUG\tKinikit\Core\Reflection\TestAttributePOPO\nKinikit\Core\Reflection\TestAttributePOPO::__set_state(array(\n   'id' => 3,\n   'name' => 'John',\n   'dob' => '01/01/2016',\n   'publicPOPO' => NULL,\n))", trim(file_get_contents($this->logPath)));
    }

}
