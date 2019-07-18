<?php


namespace Kinikit\Core\Exception;


class WrongParametersException extends \Exception {

    public function __construct($message = null) {
        parent::__construct($message ? $message : "The wrong parameters were supplied");
    }

}
