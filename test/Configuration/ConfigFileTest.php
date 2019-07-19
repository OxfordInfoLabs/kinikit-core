<?php


namespace Kinikit\Core\Configuration;

/**
 * Test cases for the config file object.
 *
 * Class ConfigFileTest
 * @package Kinikit\Core\Configuration
 */
class ConfigFileTest extends \PHPUnit\Framework\TestCase {


    public function testCanGetParametersFromConfigFile() {

        $configFile = new ConfigFile("test.txt");
        $configFile->addParameter("param1", "Mark");
        $configFile->addParameter("param2", "Luke");

        $this->assertEquals("Mark", $configFile->getParameter("param1"));
        $this->assertEquals("Luke", $configFile->getParameter("param2"));

        $this->assertEquals(["param1" => "Mark", "param2" => "Luke"], $configFile->getAllParameters());

    }

    public function testCanGetParametersMatchingPrefix() {

        $configFile = new ConfigFile("test.txt");
        $configFile->addParameter("param1", "Mark");
        $configFile->addParameter("param2", "Luke");
        $configFile->addParameter("new.test", "James");
        $configFile->addParameter("new.test2", "John");

        // With prefix intact.
        $this->assertEquals(["new.test" => "James", "new.test2" => "John"], $configFile->getParametersMatchingPrefix("new."));

        // With prefix removed.
        $this->assertEquals(["test" => "James", "test2" => "John"], $configFile->getParametersMatchingPrefix("new.", true));

    }


    public function testCanSaveConfigFile() {

        $configFile = new ConfigFile("test.txt");
        $configFile->addParameter("param1", "Mark");
        $configFile->addParameter("param2", "Luke");
        $configFile->save();

        $configContents = file_get_contents("test.txt");

        $this->assertEquals("param1=Mark\nparam2=Luke\n", $configContents);

    }


}
