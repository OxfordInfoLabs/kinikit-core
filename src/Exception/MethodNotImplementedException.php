<?php


namespace Kinikit\Core\Exception;


use Throwable;

class MethodNotImplementedException extends \Exception {

    public function __construct($message = "") {
        parent::__construct($message ?? "You have attempted to access a method which has no implementation");
    }

}
