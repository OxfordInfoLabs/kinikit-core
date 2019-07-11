<?php


namespace Kinikit\Core\DependencyInjection;


class MissingInterfaceImplementationException extends \Exception {

    public function __construct($interfaceName) {
        parent::__construct("You have attempted to create or inject an instance of $interfaceName which has no default or explicit implementation defined");
    }

}
