<?php


namespace Kinikit\Core\Stream;


use Throwable;

class StreamException extends \Exception {

    public function __construct($message) {
        parent::__construct($message);
    }

}