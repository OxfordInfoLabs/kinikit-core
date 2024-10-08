<?php

namespace Kinikit\Core\Stream\Http;

use Kinikit\Core\Stream\StreamException;

include_once "autoloader.php";

class ReadOnlyHttpStreamTest extends \PHPUnit\Framework\TestCase {


    public function testStatusAndResponseHeadersCapturedCorrectlyForValidHTTPRequest() {

        $stream = new ReadOnlyHttpStream("https://jsonplaceholder.typicode.com/posts");
        $this->assertTrue(count($stream->getResponseHeaders()) > 0);
        $this->assertEquals("200", $stream->getResponseCode());
        $this->assertEquals("1000", $stream->getResponseHeaders()["x-ratelimit-limit"]);

    }

    public function testIfStreamTimesOutStreamExceptionRaisedWithTimeoutMessage() {

        $contextOptions["http"]["timeout"] = 0.25;
        $context = stream_context_create($contextOptions);

        try {
            new ReadOnlyHttpStream("https://httpstat.us/200?sleep=5000", $context);
            $this->fail("Should have thrown here");
        } catch (StreamException $e) {
            $this->assertStringContainsString("timed out", $e->getMessage());
        }
    }


    public function testIfRedirectedDomainRedirectionStatusIsCapturedToo(){
        $stream = new ReadOnlyHttpStream("https://apple.co.uk");
        $this->assertTrue(count($stream->getResponseHeaders()) > 0);
        $this->assertEquals("301", $stream->getResponseCode());
        $this->assertEquals("200", $stream->getRedirectResponseCode());

    }


}
