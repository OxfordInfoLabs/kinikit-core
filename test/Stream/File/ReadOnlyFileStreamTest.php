<?php

namespace Kinikit\Core\Stream\File;

use Kinikit\Core\Init;
use Kinikit\Core\Stream\StreamException;

include_once "autoloader.php";

class ReadOnlyFileStreamTest extends \PHPUnit\Framework\TestCase {

    public function setUp(): void {
        new Init(); // Get the error handler set up so errors are raised as exceptions
    }


    public function testExceptionRaisedIfAttemptToOpenStreamForNonExistentFile() {

        try {
            new ReadOnlyFileStream(__DIR__ . "/bad.txt");
            $this->fail("Should have thrown here");
        } catch (StreamException $e) {
            $this->assertStringContainsString("failed to open stream: no such file or directory", strtolower($e->getMessage()));
        }


        try {
            new ReadOnlyFileStream("http://gfdgsdgs.com");
            $this->fail("Should have thrown here");
        } catch (StreamException $e) {
            $this->assertStringContainsString("php_network_getaddresses", $e->getMessage());
        }

    }

    public function testCanReadBytesUpToEOF() {
        $stream = new ReadOnlyFileStream(__DIR__ . "/test.txt");

        $bytes = $stream->read(5);
        $this->assertEquals("Hello", $bytes);

        $this->assertFalse($stream->isEof());


        $bytes = $stream->read(10);
        $this->assertEquals(", this is ", $bytes);

        $this->assertFalse($stream->isEof());


        $bytes = $stream->read(100);
        $this->assertEquals("a test file for stream testing.", $bytes);

        $this->assertTrue($stream->isEof());


        try {
            $stream->read(100);
            $this->fail("Should have thrown here");
        } catch (StreamException $e) {
            $this->assertEquals("Cannot read bytes as end of stream reached", $e->getMessage());
        }
    }


    public function testCanCheckIfStreamIsOpen() {

        $stream = new ReadOnlyFileStream(__DIR__ . "/test.txt");
        $this->assertTrue($stream->isOpen());

        $stream->close();
        $this->assertFalse($stream->isOpen());


    }


    public function testIfConnectionClosedReadingStreamExceptionIsRaised() {

        $stream = new ReadOnlyFileStream(__DIR__ . "/test.txt");
        $stream->close();

        try {
            $stream->read(100);
            $this->fail("Should have thrown here");
        } catch (StreamException $e) {
            $this->assertEquals("The stream has been closed", $e->getMessage());
        }

    }


    public function testCannotGetContentsIfStreamHasAlreadyBeenRead() {

        $stream = new ReadOnlyFileStream(__DIR__ . "/test.txt");
        $stream->read(5);

        try {
            $stream->getContents();
            $this->fail("Should have thrown here");
        } catch (StreamException $e) {
            $this->assertEquals("Cannot read stream contents because bytes have already been read", $e->getMessage());
        }

    }

    public function testCanGetFullContentsForStreamIfNotYetRead() {

        $stream = new ReadOnlyFileStream(__DIR__ . "/test.txt");
        $this->assertEquals("Hello, this is a test file for stream testing.", $stream->getContents());
    }


    public function testCanGetCSVLineFromStream() {

        $stream = new ReadOnlyFileStream(__DIR__ . "/test-csv-with-headers.csv");

        $csvLine = $stream->readCSVLine();
        $this->assertEquals([
            "Name of Person",
            "Surname",
            "Current Age"
        ], $csvLine);

        $csvLine = $stream->readCSVLine();
        $this->assertEquals([
            "Mark",
            "Robertshaw",
            "30"
        ], $csvLine);


    }


    public function testCanGetLineFromStream() {
        $stream = new ReadOnlyFileStream(__DIR__ . "/test-multiline.txt");
        $this->assertEquals("Hello", $stream->readLine());
        $this->assertEquals("World", $stream->readLine());
        $this->assertEquals("Of", $stream->readLine());
        $this->assertEquals("Fun and Games", $stream->readLine());
    }




}