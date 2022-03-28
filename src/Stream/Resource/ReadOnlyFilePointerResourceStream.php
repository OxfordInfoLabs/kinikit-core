<?php


namespace Kinikit\Core\Stream\Resource;


use Kinikit\Core\Stream\ReadableStream;
use Kinikit\Core\Stream\StreamException;

class ReadOnlyFilePointerResourceStream implements ReadableStream {

    /**
     * @var resource
     */
    protected $resource;


    /**
     * @var bool
     */
    private $hasBeenRead = false;


    /**
     * Construct with a file pointer resource already configured - base class for
     * other readable streams
     *
     * @param $resource
     */
    public function __construct($resource) {
        $this->resource = $resource;
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
     * Read a line from a stream
     *
     * @return mixed|void
     */
    public function readLine() {
        $this->checkReadOperationPossible();

        return trim(fgets($this->resource));
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
        try {
            fclose($this->resource);
        } catch (\Exception $e) {
            // Ignore
        }
    }

    /**
     * @return resource
     */
    public function getResource() {
        return $this->resource;
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