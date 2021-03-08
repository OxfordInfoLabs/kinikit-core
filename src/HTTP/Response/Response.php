<?php


namespace Kinikit\Core\HTTP\Response;


use Kinikit\Core\HTTP\Request\Request;

class Response {


    /**
     * The raw response body
     *
     * @var string
     */
    private $body;

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
     * @param string $body
     * @param int $statusCode
     * @param Headers $headers
     * @param Request $request
     */
    public function __construct($body, $statusCode, $headers, $request) {
        $this->body = $body;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->request = $request;
    }


    /**
     * @return string
     */
    public function getBody() {
        return $this->body;
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