<?php

namespace Kinikit\Core\Exception;

class NoneExistentClassException extends \Exception {

    /**
     * Construct with class name and method name.
     *
     * @param string $className
     */
    public function __construct($className) {
        parent::__construct("An attempt was made to access the none existent class '" . $className . "'");
    }


}