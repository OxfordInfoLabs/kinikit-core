<?php

namespace Kinikit\Core\Stream;

/**
 * Convenience Stream Implementation to allow chaining of multiple input streams as a single stream.
 */
class ReadOnlyMultiStream implements ReadableStream {

    /**
     * @var ReadableStream[]
     */
    private $streams;

    /**
     * @var ReadableStream
     */
    private $currentStream;

    /**
     * @var bool
     */
    private $open = true;

    /**
     * @var integer
     */
    private $streamCounter = 0;


    public function __construct($streams = []) {
        $this->streams = $streams;
        $this->currentStream = $streams[0];
    }

    public function read($length) {
        $readString = "";
        $remaining = $length;
        do {
            $readChunk = $this->currentStream->read($remaining);

            $readString .= $readChunk;

            if ($this->currentStream->isEof()) {
                $remaining -= strlen($readChunk);
                $this->streamCounter++;

                if (!isset($this->streams[$this->streamCounter])) {
                    return $readString;
                }
                $this->currentStream = $this->streams[$this->streamCounter];
            }

        } while (strlen($readString) < $length);

        return $readString;
    }

    public function readLine() {
        if (!$this->currentStream->isEof()) {
            return $this->currentStream->readLine();
        }

        $this->streamCounter++;

        if (!isset($this->streams[$this->streamCounter])) {
            throw new StreamException("Cannot read bytes as end of stream reached");
        }

        $this->currentStream = $this->streams[$this->streamCounter];
        return $this->currentStream->readLine();
    }

    public function readCSVLine($separator = ",", $enclosure = '"') {

        if (!$this->currentStream->isEof()) {
            return $this->currentStream->readCSVLine($separator, $enclosure);
        }

        $this->streamCounter++;

        if (!isset($this->streams[$this->streamCounter])) {
            throw new StreamException("Cannot read bytes as end of stream reached");
        }

        $this->currentStream = $this->streams[$this->streamCounter];
        return $this->currentStream->readCSVLine($separator, $enclosure);
    }

    public function getContents() {
        $contents = "";
        foreach ($this->streams as $stream) {
            $contents .= $stream->getContents() . "\n";
        }
        return $contents;
    }

    public function isOpen() {
        return $this->open;
    }

    public function isEof() {
        return end($this->streams)->isEof();
    }

    public function close() {
        $this->open = false;
        foreach ($this->streams as $stream) {
            $stream->close();
        }

    }
}