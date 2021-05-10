<?php

namespace Kinikit\Core\Stream\Http;

use Kinikit\Core\Exception\FileNotFoundException;
use Kinikit\Core\Stream\StreamException;

include_once "autoloader.php";

class ReadOnlyHttpStreamTest extends \PHPUnit\Framework\TestCase {


    public function testResponseHeadersCapturedCorrectlyForValidHTTPRequest() {

        $stream = new ReadOnlyHttpStream("https://jsonplaceholder.typicode.com/posts");
        $this->assertTrue(sizeof($stream->getResponseHeaders()) > 0);
        $this->assertStringContainsString("200", $stream->getResponseHeaders()[0]);

    }

    public function testIfStreamTimesOutStreamExceptionRaisedWithTimeoutMessage() {

        $contextOptions["http"]["timeout"] = 0.25;
        $context = stream_context_create($contextOptions);

        try {
            new ReadOnlyHttpStream("https://httpstat.us/200?sleep=5000", $context);
            $this->fail("Should have thrown here");
        } catch (StreamException $e) {
            $this->assertEquals("Request timed out for stream", $e->getMessage());
        }
    }


}