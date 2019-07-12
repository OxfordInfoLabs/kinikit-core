<?php


namespace Kinikit\Core\Exception;


/**
 * Generic file not found exception
 *
 * @package Kinikit\Core\Exception
 */
class FileNotFoundException extends \Exception {

    public function __construct($filename) {
        parent::__construct("The file with filename $filename cannot be found");
    }


}
