<?php


namespace Kinikit\Core\Exception;

/**
 * Generic Item not found exception - sets a 404 status for use in HTTP systems.
 *
 * Class ItemNotFoundException
 * @package Kinikit\Core\Exception
 */
class ItemNotFoundException extends StatusException {

    public function __construct($message = null) {
        parent::__construct($message ? $message : "The item accessed does not exist", 404);
    }

}
