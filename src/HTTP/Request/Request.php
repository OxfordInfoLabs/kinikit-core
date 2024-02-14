<?php


namespace Kinikit\Core\HTTP\Request;


class Request {

    /**
     * URL to post to
     *
     * @var string
     */
    private $url;

    /**
     * Method (one of the constants below)
     *
     * @var string
     */
    private $method;

    /**
     * Array of parameters (key / value pairs).  If the request is a
     * GET request the params will be appended to the URL.  Otherwise
     * the params will be passed in the body unless a payload is supplied.
     *
     * @var string[string] $parameters
     */
    private $parameters;

    /**
     * @var string
     */
    private $payload;


    /**
     * Headers object for encoding headers
     *
     * @var Headers
     */
    private $headers;

    /**
     * Timeout in seconds for this request can be set to null
     *
     * @var integer
     */
    private $timeout;


    // Method type constants for method type
    const METHOD_GET = "GET";
    const METHOD_POST = "POST";
    const METHOD_PUT = "PUT";
    const METHOD_DELETE = "DELETE";
    const METHOD_PATCH = "PATCH";
    const METHOD_HEAD = "HEAD";
    const METHOD_OPTIONS = "OPTIONS";

    /**
     * Request constructor.
     * @param string $url
     * @param string $method
     * @param string $parameters
     * @param string $payload
     * @param Headers $headers
     */
    public function __construct($url, $method = self::METHOD_POST, $parameters = [], $payload = null, $headers = null, $timeout = null) {
        $this->url = $url;
        $this->method = $method;
        $this->parameters = $parameters;
        $this->payload = $payload;
        $this->headers = $headers ? $headers : new Headers();
        $this->timeout = $timeout;
    }

    /**
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url) {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method) {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getParameters() {
        return $this->parameters;
    }

    /**
     * @param string $parameters
     */
    public function setParameters($parameters) {
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getPayload() {
        return $this->payload;
    }

    /**
     * @param string $payload
     */
    public function setPayload($payload) {
        $this->payload = $payload;
    }

    /**
     * @return Headers
     */
    public function getHeaders() {
        return $this->headers;
    }

    /**
     * @param Headers $headers
     */
    public function setHeaders($headers) {
        $this->headers = $headers;
    }

    /**
     * @return int
     */
    public function getTimeout() {
        return $this->timeout;
    }

    /**
     * @param int $timeout
     */
    public function setTimeout($timeout) {
        $this->timeout = $timeout;
    }


    /**
     * Get the URL with any params appended as required
     */
    public function getEvaluatedUrl() {

        $url = $this->getUrl();

        if ($this->method == self::METHOD_GET || $this->payload) {
            $queryString = http_build_query($this->parameters);
            if ($queryString) {
                $url .= (strpos($url, "?") ? "&" : "?") . $queryString;
            }
        }
        
        return $url;
    }

    /**
     * Get the body for this request for non GET requests
     */
    public function getBody() {

        // If a payload
        if ($this->payload) {
            return $this->payload;
        } else if ($this->method != self::METHOD_GET) {
            $queryString = http_build_query($this->parameters);
            if ($queryString) {
                return $queryString;
            }
        }

    }


}