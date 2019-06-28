<?php


namespace Kinikit\Core\Exception;


class WrongParametersException extends \Exception {

    public function __construct($message) {
        parent::__construct($message);
    }

}
