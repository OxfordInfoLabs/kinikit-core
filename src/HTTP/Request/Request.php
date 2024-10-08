<?php


namespace Kinikit\Core\HTTP\Request;


class Request {

    /**
     * URL to post to
     *
     * @var string
     */
    private string $url;

    /**
     * Method (one of the constants below)
     *
     * @var string
     */
    private string $method;

    /**
     * Array of parameters (key / value pairs).  If the request is a
     * GET request the params will be appended to the URL.  Otherwise
     * the params will be passed in the body unless a payload is supplied.
     *
     * @var array<string, string> $parameters
     */
    private array $parameters;

    /**
     * @var string|null
     */
    private ?string $payload;


    /**
     * Headers object for encoding headers
     *
     * @var Headers
     */
    private Headers $headers;

    /**
     * Timeout in seconds for this request can be set to null
     *
     * @var integer|null
     */
    private ?int $timeout;


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
     * @param array $parameters
     * @param string|null $payload
     * @param Headers $headers
     * @param int|null $timeout
     */
    public function __construct(string $url, string $method = self::METHOD_POST, array $parameters = [], ?string $payload = null, $headers = null, $timeout = null) {
        $this->url = $url;
        $this->method = $method;
        $this->parameters = $parameters;
        $this->payload = $payload;
        $this->headers = $headers ?: new Headers();
        $this->timeout = $timeout;
    }

    /**
     * @return string
     */
    public function getUrl(): string {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getMethod(): string {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod(string $method): void {
        $this->method = $method;
    }

    /**
     * @return array<string, string>
     */
    public function getParameters(): array {
        return $this->parameters;
    }

    /**
     * @param array<string,string> $parameters
     */
    public function setParameters(array $parameters): void {
        $this->parameters = $parameters;
    }

    /**
     * @return string|null
     */
    public function getPayload(): ?string {
        return $this->payload;
    }

    /**
     * @param string|null $payload
     */
    public function setPayload(?string $payload): void {
        $this->payload = $payload;
    }

    /**
     * @return Headers
     */
    public function getHeaders(): Headers {
        return $this->headers;
    }

    /**
     * @param Headers $headers
     */
    public function setHeaders(Headers $headers): void {
        $this->headers = $headers;
    }

    /**
     * @return int
     */
    public function getTimeout(): ?int {
        return $this->timeout;
    }

    /**
     * @param int|null $timeout
     */
    public function setTimeout(?int $timeout): void {
        $this->timeout = $timeout;
    }


    /**
     * Get the URL with any params appended as required
     */
    public function getEvaluatedUrl(): string {

        $url = $this->getUrl();

        if ($this->method === self::METHOD_GET || $this->payload) {
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
    public function getBody(): ?string {

        // If a payload
        if ($this->payload) {
            return $this->payload;
        }

        if ($this->method !== self::METHOD_GET) {
            $queryString = http_build_query($this->parameters);
            if ($queryString) {
                return $queryString;
            }
        }

        return null;

    }


}