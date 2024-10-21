<?php

namespace Kinikit\Core\ExternalCommands;

use Kinintel\Objects\Datasource\Command\CommandDatasource;
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

    /**
     * @group nontravis
     */
    public function testWasModifiedRecently() {
        passthru("touch -c ~/.bashrc");

        passthru("mkdir -p ~/tmp");
        passthru("touch ~/tmp/example.txt");

        $commandProcessor = new ExternalCommandProcessor();

        $out = $commandProcessor->wasUpdatedInTheLast(
            \DateInterval::createFromDateString("+1 hour"),
            "~/.bashrc"
        );

        $this->assertFalse($out);


        // PHP is in a different timezone from Linux so we need 2 hours
        $out = $commandProcessor->wasUpdatedInTheLast(
            \DateInterval::createFromDateString("+2 hour"),
            "~/tmp/example.txt"
        );

        $this->assertTrue($out);
    }
}