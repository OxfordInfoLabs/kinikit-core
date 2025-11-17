<?php

namespace Kinikit\Core\Util;

use Kinikit\Core\ExternalCommands\ExternalCommandProcessor;
use PHPUnit\Framework\TestCase;

include_once "autoloader.php";

class DateTimeUtilsTest extends TestCase {

    public function testCanConvertDate() {

        $t1 = DateTimeUtils::convertDate("U", "Y-m-d H:i:s", "1763377990");
        $this->assertEquals("2025-11-17 11:13:10", $t1);

        $t2 = DateTimeUtils::convertDate("Y-m-d H:i:s", "Y-m-d", $t1);
        $this->assertEquals("2025-11-17", $t2);

        // Mismatch of format
        $t3 = DateTimeUtils::convertDate("Ymd", "Y", "2025-11-17");
        $this->assertNull($t3);

    }

//    public function testWasUpdatedInTheLast() {
//        passthru("mkdir -p ~/tmp", $rc2);
//        passthru("touch ~/tmp/example.txt", $rc3);
//        $this->assertSame(0, $rc2);
//        $this->assertSame(0, $rc3);
//
//        $out = DateTimeUtils::wasUpdatedInTheLast(
//            \DateInterval::createFromDateString("+1 hour"),
//            "/etc/hosts"
//        );
//        $this->assertTrue(file_exists("/etc/hosts"));
//        $this->assertFalse($out);
//
//        // PHP is in a different timezone from Linux so we need 2 hours
//        $this->assertTrue(file_exists(getenv('HOME') . "/tmp/example.txt"));
//        $out = DateTimeUtils::wasUpdatedInTheLast(
//            \DateInterval::createFromDateString("+2 hour"),
//            "~/tmp/example.txt"
//        );
//        $this->assertTrue($out);
//
//        $out = DateTimeUtils::wasUpdatedInTheLast(
//            \DateInterval::createFromDateString("+1 hour"),
//            "~/SOMETHING_WHICH_DOESNT_EXIST"
//        );
//        $this->assertFalse($out);
//    }

}