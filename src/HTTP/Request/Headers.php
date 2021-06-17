<?php


namespace Kinikit\Core\HTTP\Request;

/**
 * Class Headers
 * @package Kinikit\Core\HTTP\Request
 *
 * Request headers class
 */
class Headers {

    /**
     * @var string[string]
     */
    private $headers;

    const ACCEPT = "Accept";
    const ACCEPT_CHARSET = "Accept-Charset";
    const ACCEPT_DATETIME = "Accept-Datetime";
    const ACCEPT_ENCODING = "Accept-Encoding";
    const ACCEPT_LANGUAGE = "Accept-Language";
    const ACCESS_CONTROL_REQUEST_METHOD = "Access-Control-Request-Method";
    const AUTHORISATION = "Authorization";
    const CACHE_CONTROL = "Cache-Control";
    const CONNECTION = "Connection";
    const CONTENT_ENCODING = "Content-Encoding";
    const CONTENT_LENGTH = "Content-Length";
    const CONTENT_MD5 = "Content-MD5";
    const CONTENT_TYPE = "Content-Type";
    const COOKIE = "Cookie";
    const DATE = "Date";
    const EXPECT = "Expect";
    const FORWARDED = "Forwarded";
    const FROM = "From";
    const HOST = "Host";
    const ORIGIN = "Origin";
    const PRAGMA = "Pragma";
    const PREFER = "Prefer";
    const PROXY_AUTHORISATION = "Proxy-Authorisation";
    const REFERER = "Referer";
    const TRANSFER_ENCODING = "Transfer-Encoding";
    const USER_AGENT = "User-Agent";

    /**
     * Construct with array of headers if wish
     *
     * Headers constructor.
     * @param array $headers
     */
    public function __construct($headers = []) {
        $this->headers = $headers;
    }


    /**
     * Set a header with a value
     *
     * @param string $headerName
     * @param string $value
     */
    public function set($headerName, $value) {
        $this->headers[$headerName] = $value;
    }

    /**
     * @return string
     */
    public function getHeaders() {
        return $this->headers;
    }


}