<?php


namespace Kinikit\Core\Stream\Http;


use Kinikit\Core\Stream\File\ReadOnlyFileStream;
use Kinikit\Core\Stream\StreamException;

/**
 * Simple extension of the read only file stream to ensure we capture HTTP response headers
 *
 * Class ReadOnlyHttpStream
 * @package Kinikit\Core\Stream\Http
 */
class ReadOnlyHttpStream extends ReadOnlyFileStream {

    // Response headers from stream
    private $responseHeaders = [];

    /**
     * Construct with a filename and optional context options
     *
     * @param $url
     * @param null $contextOptions
     */
    public function __construct($url, $contextOptions = null) {

        try {

            if (!$contextOptions) {
                $this->resource = fopen($url, "r", false);
            } else {
                $this->resource = fopen($url, "r", false, $contextOptions);
            }

            // If no resource, throw stream exception with message
            if ($this->resource === false) {

                if (isset($http_response_header) && count($http_response_header) == 0) {
                    throw new StreamException("Request timed out for stream");
                }

                $this->throwLastStreamError();
            } else {
                if (isset($http_response_header)) {
                    $this->responseHeaders = $http_response_header;
                }
            }


        } catch (\ErrorException $e) {
            if (isset($http_response_header) && count($http_response_header) == 0) {
                throw new StreamException("Request timed out for stream");
            }

            $this->throwLastStreamError($e->getMessage());
        }


    }

    /**
     * Get the array of response headers from this stream
     *
     * @return array
     */
    public function getResponseHeaders() {
        return $this->responseHeaders;
    }


}