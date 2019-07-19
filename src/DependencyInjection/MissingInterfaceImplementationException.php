<?php


namespace Kinikit\Core\DependencyInjection;


class MissingInterfaceImplementationException extends \Exception {

    public function __construct($message = null) {
        parent::__construct($message);
    }

}
