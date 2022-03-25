<?php

namespace Kinikit\Core\Stream\Process;

include_once "autoloader.php";

class ReadOnlyProcessStreamTest extends \PHPUnit\Framework\TestCase {


    public function testCanOpenStreamToProcessAndReadAccordingly() {

        $stream = new ReadOnlyProcessStream("echo 'bing\nbong\nbang'");

        $this->assertEquals("bing", $stream->readLine());
        $this->assertEquals("bong", $stream->readLine());
        $this->assertEquals("bang", $stream->readLine());

        $stream->readLine();
        $this->assertTrue($stream->isEof());
    }

}