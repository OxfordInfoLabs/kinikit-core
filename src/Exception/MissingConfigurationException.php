<?php

namespace Kinikit\Core\Exception;

class MissingConfigurationException extends \Exception {

    public function __construct($configParameter) {
        parent::__construct("The configuration parameter " . $configParameter . " is required but has not been supplied.");
    }

} 