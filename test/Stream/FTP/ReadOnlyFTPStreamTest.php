<?php


namespace Kinikit\Core\Stream\FTP;

include_once "autoloader.php";

class ReadOnlyFTPStreamTest extends \PHPUnit\Framework\TestCase {


//    public function testCanConnectToInsecureFTPServerUsingUsernameAndPassword() {
//
//        $insecureFTPStream = new ReadOnlyFTPStream("test.rebex.net", "readme.txt", false, "demo", "password");
//
//        $contents = $insecureFTPStream->getContents();
//
//        $this->assertTrue(true);
//
//    }


    public function testCanConnectToSecureFTPServerUsingUsernameAndPassword() {

        $insecureFTPStream = new ReadOnlyFTPStream("test.rebex.net", "readme.txt", true, "demo", "password");

        $contents = $insecureFTPStream->getContents();

        $this->assertStringContainsString("Rebex SFTP", $contents);

    }


}