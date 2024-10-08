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
    private ReadableStream $stream;

    /**
     * The status code for this response
     *
     * @var integer
     */
    private int $statusCode;


    /**
     * The response headers for this response
     *
     * @var Headers
     */
    private Headers $headers;

    /**
     * The request which generated this response
     *
     * @var Request
     */
    private Request $request;

    /**
     * Response constructor.
     *
     * @param ReadableStream $stream
     * @param int $statusCode
     * @param Headers $headers
     * @param Request $request
     */
    public function __construct(ReadableStream $stream, int $statusCode, Headers $headers, Request $request) {
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
    public function getStream(): ReadableStream {
        return $this->stream;
    }

    /**
     * Get the body, provided the stream hasn't already been read
     *
     * @return string
     */
    public function getBody(): string {
        return $this->stream->getContents();
    }

    /**
     * @return int
     */
    public function getStatusCode(): int {
        return $this->statusCode;
    }

    /**
     * @return Headers
     */
    public function getHeaders(): Headers {
        return $this->headers;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request {
        return $this->request;
    }


}