<?php


namespace Kinikit\Core\Exception;


class InsufficientParametersException extends \Exception {

    public function __construct($message) {
        parent::__construct($message);
    }

}
