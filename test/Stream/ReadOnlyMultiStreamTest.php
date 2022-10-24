<?php

namespace Kinikit\Core\Stream;


use Kinikit\Core\Stream\File\ReadOnlyFileStream;
use Kinikit\Core\Stream\String\ReadOnlyStringStream;
use PHPUnit\Framework\TestCase;

include_once "autoloader.php";

class ReadOnlyMultiStreamTest extends TestCase {


    public function testCanReadStreamGivenLength() {

        $stream1 = new ReadOnlyStringStream("The first test stream.");
        $stream2 = new ReadOnlyStringStream("Another test stream.");

        $stream = new ReadOnlyMultiStream([$stream1, $stream2]);

        $this->assertEquals("The ", $stream->read(4));
        $this->assertEquals("first", $stream->read(5));
        $this->assertEquals(" test ", $stream->read(6));

    }

    public function testCanCloseAllStreams() {

        $stream1 = new ReadOnlyFileStream(__DIR__ . "/test1.txt");
        $stream2 = new ReadOnlyFileStream(__DIR__ . "/test2.txt");

        $stream = new ReadOnlyMultiStream([$stream1, $stream2]);

        $this->assertTrue($stream->isOpen());

        $stream->close();

        $this->assertFalse($stream->isOpen());
        $this->assertFalse($stream1->isOpen());
        $this->assertFalse($stream2->isOpen());

    }

    public function testCanDetectEndOfFile() {

        $stream1 = new ReadOnlyFileStream(__DIR__ . "/test1.txt");
        $stream2 = new ReadOnlyFileStream(__DIR__ . "/test2.txt");

        $stream = new ReadOnlyMultiStream([$stream1, $stream2]);

        $this->assertFalse($stream->isEof());

        $stream->read(1000);

        $this->assertTrue($stream->isEof());

    }

    public function testCanGetWholeContents() {

        $stream1 = new ReadOnlyFileStream(__DIR__ . "/test1.txt");
        $stream2 = new ReadOnlyFileStream(__DIR__ . "/test2.txt");

        $stream = new ReadOnlyMultiStream([$stream1, $stream2]);

        $this->assertEquals("The first test stream.\nA second line.\nAnother test stream.\n", $stream->getContents());

    }

    public function testCanReadAcrossStreamsWithSameType() {

        $stream1 = new ReadOnlyFileStream(__DIR__ . "/test1.txt");
        $stream2 = new ReadOnlyFileStream(__DIR__ . "/test2.txt");

        $stream = new ReadOnlyMultiStream([$stream1, $stream2]);

        $stream->read(32);
        $this->assertEquals("line.Anoth", $stream->read(10));

    }

    public function testCanReadAcrossStreamsWithDifferentTypes() {

        $stream1 = new ReadOnlyFileStream(__DIR__ . "/test1.txt");
        $stream2 = new ReadOnlyStringStream("Another test stream.");

        $stream = new ReadOnlyMultiStream([$stream1, $stream2]);

        $stream->read(33);
        $this->assertEquals("ine.Anothe", $stream->read(10));

    }

    public function testCanReadLineByLineAcrossStreams() {

        $stream1 = new ReadOnlyFileStream(__DIR__ . "/test1.txt");
        $stream2 = new ReadOnlyFileStream(__DIR__ . "/test2.txt");

        $stream = new ReadOnlyMultiStream([$stream1, $stream2]);

        $this->assertEquals("The first test stream.", $stream->readLine());
        $this->assertEquals("A second line.", $stream->readLine());
        $this->assertEquals("Another test stream.", $stream->readLine());

        try {
            $stream->readLine();
            $this->fail("Should have thrown here");
        } catch (StreamException $e) {
            $this->assertEquals("Cannot read bytes as end of stream reached", $e->getMessage());
        }
    }

    public function testCanReadCSVLineByLineAcrossStreams() {

        $stream1 = new ReadOnlyFileStream(__DIR__ . "/csvtest1.txt");
        $stream2 = new ReadOnlyFileStream(__DIR__ . "/csvtest2.txt");

        $stream = new ReadOnlyMultiStream([$stream1, $stream2]);

        $this->assertEquals(["John", 24], $stream->readCSVLine());
        $this->assertEquals(["Robert",45], $stream->readCSVLine());
        $this->assertEquals(["William",33], $stream->readCSVLine());
        $this->assertEquals(["Philip",86], $stream->readCSVLine());
        $this->assertEquals(["Matthew",12], $stream->readCSVLine());

        try {
            $stream->readLine();
            $this->fail("Should have thrown here");
        } catch (StreamException $e) {
            $this->assertEquals("Cannot read bytes as end of stream reached", $e->getMessage());
        }
    }
}