<?php


namespace Kinikit\Core\HTTP\Response;


use Kinikit\Core\HTTP\Request\Request;
use Kinikit\Core\Stream\ReadableStream;

class Response {

    /**
     * The raw response body
     *
     * @var ReadableStream
     */
    private $stream;

    /**
     * The status code for this response
     *
     * @var integer
     */
    private $statusCode;


    /**
     * The response headers for this response
     *
     * @var Headers
     */
    private $headers;

    /**
     * The request which generated this response
     *
     * @var Request
     */
    private $request;

    /**
     * Response constructor.
     *
     * @param ReadableStream $stream
     * @param int $statusCode
     * @param Headers $headers
     * @param Request $request
     */
    public function __construct($stream, $statusCode, $headers, $request) {

        $this->stream = $stream;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->request = $request;
    }


    /**
     * Get the Readable File stream
     *
     * @return ReadableStream
     */
    public function getStream() {
        return $this->stream;
    }

    /**
     * Get the body, provided the stream hasn't already been read
     *
     * @return string
     */
    public function getBody() {
        return $this->stream->getContents();
    }

    /**
     * @return int
     */
    public function getStatusCode() {
        return $this->statusCode;
    }

    /**
     * @return Headers
     */
    public function getHeaders() {
        return $this->headers;
    }

    /**
     * @return Request
     */
    public function getRequest() {
        return $this->request;
    }


}