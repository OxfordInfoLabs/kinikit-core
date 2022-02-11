<?php

namespace Kinikit\Core\Stream\String;

use Kinikit\Core\Stream\File\ReadOnlyFileStream;
use Kinikit\Core\Stream\StreamException;

include_once "autoloader.php";

class ReadOnlyStringStreamTest extends \PHPUnit\Framework\TestCase {


    public function testCanReadFromStringStreamUntilEOF() {


        $stream = new ReadOnlyStringStream("Hello world of fun");
        $this->assertEquals("Hello", $stream->read(5));
        $this->assertFalse($stream->isEof());
        $this->assertEquals(" world of ", $stream->read(10));
        $this->assertFalse($stream->isEof());
        $this->assertEquals("fun", $stream->read(100));
        $this->assertTrue($stream->isEof());

        try {
            $stream->read(100);
            $this->fail("Should have thrown here");
        } catch (StreamException $e) {
            $this->assertEquals("Cannot read bytes as end of stream reached", $e->getMessage());
        }


    }

    public function testCanReadCSVFromStringStreamUntilEOF() {

        $stream = new ReadOnlyStringStream(file_get_contents(__DIR__ . "/../File/test-csv-with-headers.csv"));

        $this->assertEquals(["Name of Person", "Surname", "Current Age"], $stream->readCSVLine());
        $this->assertEquals(["Mark", "Robertshaw", 30], $stream->readCSVLine());
        $this->assertEquals(["James", "Smith", 10], $stream->readCSVLine());

    }


    public function testCanReadLineFromStringsStream() {

        $stream = new ReadOnlyStringStream(file_get_contents(__DIR__ . "/../File/test-multiline.txt"));
        $this->assertEquals("Hello", $stream->readLine());
        $this->assertEquals("World", $stream->readLine());
        $this->assertEquals("Of", $stream->readLine());
        $this->assertEquals("Fun and Games", $stream->readLine());

    }

    public function testCanGetContentsAtAnyTime() {

        $stream = new ReadOnlyStringStream("Bingo Bango");
        $this->assertEquals("Bingo Bango", $stream->getContents());

        $stream->read(10);
        $this->assertEquals("Bingo Bango", $stream->getContents());


    }


    public function testOpenAlwaysTrueAndCloseDoesNothing() {

        $stream = new ReadOnlyStringStream("Bingo Bango");
        $this->assertTrue($stream->isOpen());
        $stream->close();
        $this->assertTrue($stream->isOpen());


    }

}