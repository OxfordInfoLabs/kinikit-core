<?php


namespace Kinikit\Core\Stream\String;


use Kinikit\Core\Stream\ReadableStream;
use Kinikit\Core\Stream\StreamException;

class ReadOnlyStringStream implements ReadableStream {

    /**
     * @var string
     */
    private $string;

    /**
     * Position in string
     *
     * @var int
     */
    private $pointer = 0;

    /**
     * Construct with string.
     *
     * ReadOnlyStringStream constructor.
     * @param $string
     */
    public function __construct($string) {
        $this->string = $string;
    }

    /**
     * Read bytes from string
     *
     * @param $length
     * @return string|void
     */
    public function read($length) {

        if (!$this->isEof()) {
            $bytes = substr($this->string, $this->pointer, $length);
            $this->pointer += strlen($bytes);
            return $bytes;
        } else {
            throw new StreamException("Cannot read bytes as end of stream reached");
        }
    }

    /**
     * Read a CSV line
     *
     * @param string $separator
     * @param string $enclosure
     * @return array
     * @throws StreamException
     */
    public function readCSVLine($separator = ",", $enclosure = '"') {
        if (!$this->isEof()) {
            $lineEnding = strpos(substr($this->string, $this->pointer), "\n");
            $csvEntries = str_getcsv(substr($this->string, $this->pointer, $lineEnding), $separator, $enclosure);
            $this->pointer += $lineEnding + 1;
            return $csvEntries;
        } else {
            throw new StreamException("Cannot read bytes as end of stream reached");
        }
    }

    /**
     * Always return contents
     *
     * @return string
     */
    public function getContents() {
        return $this->string;
    }

    /**
     * String stream always open
     *
     * @return bool
     */
    public function isOpen() {
        return true;
    }

    // If pointer exceeded it must be eof
    public function isEof() {
        return $this->pointer >= strlen($this->string);
    }

    /**
     * Close does nothing for string stream
     *
     * @return mixed|void
     */
    public function close() {
    }
}