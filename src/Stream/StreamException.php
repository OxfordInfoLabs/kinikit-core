<?php


namespace Kinikit\Core\Stream;


class StreamException extends \Exception {

    public function __construct($message) {
        parent::__construct($message);
    }

}