<?php


namespace Kinikit\Core\Exception;


use Exception;

class WrongPropertyTypeException extends Exception {

    public function __construct($message) {
        parent::__construct($message);
    }

}
