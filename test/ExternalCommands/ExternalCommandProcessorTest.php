<?php

namespace Kinikit\Core\ExternalCommands;

use PHPUnit\Framework\TestCase;

include_once "autoloader.php";

class ExternalCommandProcessorTest extends TestCase {
    public function testEcho() {
        $commandProcessor = new ExternalCommandProcessor();
        $resultCode = $commandProcessor->process("echo fishing!");
        $this->assertEquals(0, $resultCode);

        try {
            $resultCode = $commandProcessor->process("maliciouscommand 1000");
            $this->fail();
        } catch (ExternalCommandException $e) {
            $this->assertStringContainsString("not whitelisted", $e->getMessage());
            //Success
        }

        $resultCode = $commandProcessor->process("false", false);
        $this->assertEquals(1, $resultCode);

        try {
            $resultCode = $commandProcessor->process("false", true);
            $this->fail();
        } catch( ExternalCommandException $e) {
            // Success
            $this->assertStringContainsString("error code 1", $e->getMessage());
        }
    }

    public function testProcessToOutput() {
        $commandProcessor = new ExternalCommandProcessor();
        $out = $commandProcessor->processToOutput("echo fishing!");
        $this->assertEquals("fishing!", $out);
        try {
            $out = $commandProcessor->processToOutput("maliciouscommand 1000");
            $this->fail();
        } catch (ExternalCommandException $e) {
            // Success
        }
        try {
            $commandProcessor->processToOutput("false");
            $this->fail();
        } catch (ExternalCommandException $e) {
            // Success
        }
    }
}