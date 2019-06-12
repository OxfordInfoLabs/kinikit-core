<?php

namespace Kinikit\Core;


include_once "autoloader.php";

/**
 * Test cases for the application configuration.
 *
 */
class ConfigurationTest extends \PHPUnit\Framework\TestCase {

    public function testStaticallyCallingConfigurationReadParameterUsesConfigTxtFileInConfigDirectory() {

        $this->assertNotNull(Configuration::readParameter("db.username"));
        $this->assertNotNull(Configuration::readParameter("db.host"));

        // Check explicitly for test params too.
        $this->assertEquals("ford", Configuration::readParameter("param1"));
        $this->assertEquals("vauxhall", Configuration::readParameter("param2"));
        $this->assertEquals("bmw", Configuration::readParameter("param3"));
        $this->assertEquals("porsche", Configuration::readParameter("param4"));

        $this->assertNull(Configuration::readParameter("param5"));

    }


    public function testIfKinikitEnvironmentVariableSetDifferentConfigIsRead() {

        putenv("KINIKIT_CONFIG_FILE=config.txt.test");

        // Reset config
        Configuration::instance(true);


        $this->assertNotNull(Configuration::readParameter("db.username"));
        $this->assertNotNull(Configuration::readParameter("db.host"));

        // Check explicitly for test params too.
        $this->assertEquals("nissan", Configuration::readParameter("param1"));
        $this->assertEquals("toyota", Configuration::readParameter("param2"));
        $this->assertEquals("saab", Configuration::readParameter("param3"));
        $this->assertEquals("vauxhall", Configuration::readParameter("param4"));




    }

}

?>
