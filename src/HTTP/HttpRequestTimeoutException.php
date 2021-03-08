<?php


namespace Kinikit\Core\HTTP;


class HttpRequestTimeoutException extends \Exception {

    public function __construct() {
        parent::__construct("The Http Request has exceeded the configured timeout.");
    }

}