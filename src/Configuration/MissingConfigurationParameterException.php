<?php


namespace Kinikit\Core\Configuration;


use Kinikit\Core\Exception\ItemNotFoundException;

class MissingConfigurationParameterException extends ItemNotFoundException {

    /**
     * MissingConfigurationParameterException constructor.
     */
    public function __construct($parameterName) {
        parent::__construct("The parameter with name $parameterName is required but has not been defined");
    }
}