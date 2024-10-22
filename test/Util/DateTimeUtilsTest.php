<?php

namespace Kinikit\Core\Util;

use Kinikit\Core\ExternalCommands\ExternalCommandProcessor;
use PHPUnit\Framework\TestCase;

include_once "autoloader.php";
class DateTimeUtilsTest extends TestCase {
    /**
     * @group nontravis
     */
    public function testWasUpdatedInTheLast() {
        passthru("touch -c ~/.bashrc", $rc1);
        passthru("mkdir -p ~/tmp", $rc2);
        passthru("touch ~/tmp/example.txt", $rc3);
        $this->assertSame(0, $rc1);
        $this->assertSame(0, $rc2);
        $this->assertSame(0, $rc3);

        $out = DateTimeUtils::wasUpdatedInTheLast(
            \DateInterval::createFromDateString("+1 hour"),
            "~/.bashrc"
        );
        $this->assertFalse($out);

        // PHP is in a different timezone from Linux so we need 2 hours
        $out = DateTimeUtils::wasUpdatedInTheLast(
            \DateInterval::createFromDateString("+2 hour"),
            "~/tmp/example.txt"
        );
        $this->assertTrue($out);

        $out = DateTimeUtils::wasUpdatedInTheLast(
            \DateInterval::createFromDateString("+1 hour"),
            "~/SOMETHING_WHICH_DOESNT_EXIST"
        );
        $this->assertFalse($out);
    }
}