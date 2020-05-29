<?php


namespace Kinikit\Core\Exception;


class WrongParameterTypeException extends \Exception {

    public function __construct($message) {
        parent::__construct($message);
    }

}
