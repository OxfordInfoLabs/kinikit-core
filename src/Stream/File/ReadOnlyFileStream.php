<?php


namespace Kinikit\Core\Stream\File;


use Kinikit\Core\Stream\ReadableStream;
use Kinikit\Core\Stream\StreamException;

/**
 * Read only file stream - great for reading from files and urls
 *
 * @package Kinikit\Core\Stream\File
 */
class ReadOnlyFileStream implements ReadableStream {


    /**
     * @var resource
     */
    protected $resource;


    /**
     * @var bool
     */
    private $hasBeenRead = false;


    /**
     * Construct with a filename and optional context options
     *
     * @param $filename
     * @param null $contextOptions
     */
    public function __construct($filename, $contextOptions = null) {

        try {
            if (!$contextOptions) {
                $this->resource = fopen($filename, "r", false);
            } else {
                $this->resource = fopen($filename, "r", false, $contextOptions);
            }

            // If no resource, throw stream exception with message
            if ($this->resource === false) {
                $this->throwLastStreamError();
            }
        } catch (\ErrorException $e) {
            $this->throwLastStreamError($e->getMessage());
        }
    }


    /**
     * Read the specified number of bytes from the stream
     *
     * @param $length
     * @return string|void
     */
    public function read($length) {

        $this->checkReadOperationPossible();

        $bytes = fread($this->resource, $length);

        // if couldn't read bytes throw the last error
        if ($bytes === false) {
            $this->throwLastStreamError();
        }

        // Record read
        $this->hasBeenRead = true;

        return $bytes;
    }


    /**
     * Read a CSV line using the passed separator and enclosures
     */
    public function readCSVLine($separator = ",", $enclosure = '"') {

        $this->checkReadOperationPossible();

        $csvLine = fgetcsv($this->resource, 0, $separator, $enclosure);

        return $csvLine;
    }

    /**
     * Get the full contents of this stream
     *
     * @return string|void
     */
    public function getContents() {

        $this->checkReadOperationPossible();

        if ($this->hasBeenRead) {
            throw new StreamException("Cannot read stream contents because bytes have already been read");
        }

        $contents = stream_get_contents($this->resource);
        if ($contents === false) {
            $this->throwLastStreamError();
        }
        return $contents;

    }

    /**
     * Boolean indicator as to whether or not the stream is open
     *
     * @return bool|void
     */
    public function isOpen() {
        return is_resource($this->resource);
    }


    /**
     * Boolean indicator of EOF
     *
     * @return bool|void
     */
    public function isEof() {
        try {
            return feof($this->resource);
        } catch (\ErrorException $e) {
            return true;
        }
    }

    /**
     * Close this stream
     *
     * @return mixed|void
     */
    public function close() {
        fclose($this->resource);
    }


    // Throw the last stream error
    protected function throwLastStreamError($message = null) {
        if (!$message)
            $message = error_get_last()["message"];

        $lastErrorArray = explode(":", $message, 2);
        $lastError = trim(array_pop($lastErrorArray));

        throw new StreamException($lastError);
    }

    // Check a read operation is possible
    private function checkReadOperationPossible() {

        if (!$this->isOpen()) {
            throw new StreamException("The stream has been closed");
        }

        if ($this->isEof()) {
            throw new StreamException("Cannot read bytes as end of stream reached");
        }
    }
}