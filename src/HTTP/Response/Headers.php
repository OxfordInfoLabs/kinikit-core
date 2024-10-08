<?php


namespace Kinikit\Core\HTTP\Response;


class Headers {

    /**
     * @var string[]
     */
    private array $headers = [];

    // Mapping constants for header names

    // Access control
    const ACCESS_CONTROL_ALLOW_ORIGIN = "access-control-allow-origin";
    const ACCESS_CONTROL_ALLOW_CREDENTIALS = "access-control-allow-credentials";
    const ACCESS_CONTROL_EXPOSE_HEADERS = "access-control-expose-headers";
    const ACCESS_CONTROL_MAX_AGE = "access-control-max-age";
    const ACCESS_CONTROL_ALLOW_METHODS = "access-control-allow-methods";
    const ACCESS_CONTROL_ALLOW_HEADERS = "access-control-allow-headers";

    // Content
    const CONTENT_DISPOSITION = "content-disposition";
    const CONTENT_ENCODING = "content-encoding";
    const CONTENT_LANGUAGE = "content-language";
    const CONTENT_LENGTH = "content-length";
    const CONTENT_LOCATION = "content-location";
    const CONTENT_MD5 = "content-md5";
    const CONTENT_RANGE = "content-range";
    const CONTENT_TYPE = "content-type";


    // Caching and Lifecycle
    const AGE = "age";
    const CACHE_CONTROL = "cache-control";
    const DATE = "date";
    const ETAG = "etag";
    const EXPIRES = "expires";
    const LAST_MODIFIED = "last-modified";
    const RETRY_AFTER = "retry-after";
    const LOCATION = "location";

    // Rate Limiting
    const RATELIMIT_LIMIT = "x-ratelimit-limit";
    const RATELIMIT_REMAINING = "x-ratelimit-remaining";
    const RATELIMIT_RESET = "x-ratelimit-reset";

    // General
    const SET_COOKIE = "set-cookie";
    const COOKIE = "cookie";

    /**
     * Construct with array of headers from response
     *
     * Headers constructor.
     * @param array $headers
     */
    public function __construct(array $headers = []) {

        foreach ($headers as $key => $value) {
            $this->headers[strtolower($key)] = $value;
        }


    }

    /**
     * Return the array of headers as constructed here
     *
     * @return array
     */
    public function getHeaders(): array {
        return $this->headers;
    }

    /**
     * Get a specific header or return null if not set
     *
     * @param $headerName
     * @return string|null
     */
    public function get($headerName): ?string {
        return $this->headers[$headerName] ?? null;
    }

}