<?php

namespace Kinikit\Core\Util\Multithreading;

include_once "autoloader.php";

/**
 *
 * Test cases for the thread class
 */
class ThreadTest extends \PHPUnit\Framework\TestCase {

    public function testTest(){
        self::assertTrue(true);
    }

//    public function testCanCreateSimpleExampleThreadAndRunAccordingly() {
//
//        if (file_exists("/tmp/thread"))
//            unlink("/tmp/thread");
//
//        if (file_exists("/tmp/master"))
//            unlink("/tmp/master");
//
//
//        $newThread = new ExampleThread();
//        $newThread->run();
//
//        file_put_contents("/tmp/master", "BING", FILE_APPEND);
//
//
//        // Master thread should see master file
//        $this->assertEquals("BING", file_get_contents("/tmp/master"));
//
//        sleep(1);
//
//        // Now the child thread has written, we should see it.
//        $this->assertEquals("BOO", file_get_contents("/tmp/thread"));
//
//
//    }
//
//
//    public function testCanWaitAndReadDataFromChildThreadOnCompletion() {
//
//
//        if (file_exists("/tmp/thread"))
//            unlink("/tmp/thread");
//
//        if (file_exists("/tmp/master"))
//            unlink("/tmp/master");
//
//
//        $newThread = new ExampleThread();
//        $newThread->run();
//
//        // Wait until the child thread has run.
//        $newThread->wait();
//
//        // Master thread should now see the child output immediately.
//        $this->assertEquals("BOO", file_get_contents("/tmp/thread"));
//
//
//    }
//
//
//    public function testCanPassParametersThroughToProcess() {
//
//        if (file_exists("/tmp/thread"))
//            unlink("/tmp/thread");
//
//        if (file_exists("/tmp/master"))
//            unlink("/tmp/master");
//
//
//        $newThread = new ExampleThread();
//        $newThread->run(array("param1" => "Bingo", "param2" => "Fred"));
//
//        // Wait until the child thread has run.
//        $newThread->wait();
//
//        // Master thread should now see the child output immediately.
//        $this->assertEquals("BOO,Bingo,Fred", file_get_contents("/tmp/thread"));
//
//    }


}
