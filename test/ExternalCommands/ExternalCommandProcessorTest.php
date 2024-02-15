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
        } catch (\Exception $e) {
            $this->assertStringContainsString("not whitelisted", $e->getMessage());
            //Success
        }

        $resultCode = $commandProcessor->process("false");
        $this->assertEquals(1, $resultCode);
    }
}