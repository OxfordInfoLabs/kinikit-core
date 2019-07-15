<?php


namespace Kinikit\Core\Exception;

/**
 * Generic access denied exception.
 *
 * Class AccessDeniedException
 */
class AccessDeniedException extends StatusException {

    public function __construct($message = null) {
        parent::__construct($message ? $message : "You do not have sufficient access to complete the requested operation", 403);
    }


}
