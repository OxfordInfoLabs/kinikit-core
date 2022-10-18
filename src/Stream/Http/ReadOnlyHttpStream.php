<?php


namespace Kinikit\Core\Stream\Http;


use Kinikit\Core\Logging\Logger;
use Kinikit\Core\Stream\File\ReadOnlyFileStream;
use Kinikit\Core\Stream\Resource\ReadOnlyFilePointerResourceStream;
use Kinikit\Core\Stream\StreamException;

/**
 * Simple extension of the read only file stream to ensure we capture HTTP response headers
 *
 * Class ReadOnlyHttpStream
 * @package Kinikit\Core\Stream\Http
 */
class ReadOnlyHttpStream extends ReadOnlyFilePointerResourceStream {

    // Response headers from stream
    private $responseHeaders = [];

    // Response code
    private $responseCode = 0;

    // Redirect response code
    private $redirectResponseCode;

    /**
     * Construct with a filename and optional context options
     *
     * @param $url
     * @param null $context
     */
    public function __construct($url, $context = null) {

        try {


            if (!$context) {
                $resource = fopen($url, "r", false);
            } else {
                $resource = fopen($url, "r", false, $context);
            }

            // If no resource, throw stream exception with message
            if ($resource === false) {

                if (isset($http_response_header) && count($http_response_header) == 0) {
                    throw new StreamException("Request timed out for stream");
                }

                $this->throwLastStreamError();
            } else {
                parent::__construct($resource);
                if (isset($http_response_header)) {

                    $headers = array();
                    $responseCode = 0;
                    $redirectResponseCode = null;

                    $headersObject = $http_response_header;
                    foreach ($headersObject as $k => $v) {
                        $t = explode(':', $v, 2);
                        if (isset($t[1]))
                            $headers[strtolower(trim($t[0]))] = trim($t[1]);
                        else if (preg_match("#HTTP/[0-9\.]+\s+([0-9]+)#", $v, $out)) {
                            if (!$responseCode)
                                $responseCode = intval($out[1]);
                            else
                                $redirectResponseCode = intval($out[1]);
                        }

                    }

                    // Update response headers and response code
                    $this->responseHeaders = $headers;
                    $this->responseCode = $responseCode;
                    $this->redirectResponseCode = $redirectResponseCode;
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


    /**
     * Get a response code if one was found.
     *
     * @return int
     */
    public function getResponseCode() {
        return $this->responseCode;
    }

    /**
     * @return int
     */
    public function getRedirectResponseCode() {
        return $this->redirectResponseCode;
    }


}
