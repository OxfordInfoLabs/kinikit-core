<?php

namespace Kinikit\Core\Logging;


include_once "autoloader.php";


/**
 * Created by PhpStorm.
 * User: mark
 * Date: 16/07/2014
 * Time: 10:37
 */
class LoggerTest extends \PHPUnit\Framework\TestCase {

    public function setUp(): void {
        parent::setUp();
        passthru("rm -rf /tmp/ooacorelog.log");
    }

    public function testCanLogSimpleGeneralStringsDirectlyToLogFile() {

        Logger::log("Hello World");
        $this->assertEquals(date("d/m/Y H:i:s") . "\tGENERAL\tHello World", trim(file_get_contents("/tmp/ooacorelog.log")));

        Logger::log("Gumdrop");
        $this->assertEquals(date("d/m/Y H:i:s") . "\tGENERAL\tHello World\n" . date("d/m/Y H:i:s") . "\tGENERAL\tGumdrop", trim(file_get_contents("/tmp/ooacorelog.log")));

    }


    public function testCanLogStringsWithCustomCategoryToLogFile() {
        Logger::log("Custom Test", "BESPOKE");
        $this->assertEquals(date("d/m/Y H:i:s") . "\tBESPOKE\tCustom Test", trim(file_get_contents("/tmp/ooacorelog.log")));

        Logger::log("Another One", "MAIL");
        $this->assertEquals(date("d/m/Y H:i:s") . "\tBESPOKE\tCustom Test\n" . date("d/m/Y H:i:s") . "\tMAIL\tAnother One", trim(file_get_contents("/tmp/ooacorelog.log")));
    }

    public function testCanLogExceptionsDirectlyAndTheseGetLoggedAsErrors() {
        Logger::log(new \Exception("Test exception"));
        $this->assertEquals(date("d/m/Y H:i:s") . "\t\033[31mERROR:\033[0m\tException\nTest exception", trim(file_get_contents("/tmp/ooacorelog.log")));
    }

    public function testArraysAreLoggedUsingVarExportOnNewLine() {
        Logger::log(array(1, 2, 3, 4, 5));
        $this->assertEquals(date("d/m/Y H:i:s") . "\tGENERAL\tArray\n" . var_export(array(1, 2, 3, 4,
                5), true), trim(file_get_contents("/tmp/ooacorelog.log")));
    }




}
