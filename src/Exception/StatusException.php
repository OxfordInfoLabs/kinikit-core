<?php


namespace Kinikit\Core\Exception;


use Throwable;

/**
 * Generic base exception class which encodes a status code (in addition to the exception code) as part of the Exception params.
 * This is useful downstream where status codes are required for e.g. responses etc.
 *
 * In MVC, the status code will be used as the response code when returning an Exception / exception view.
 *
 * Class StatusException
 * @package Kinikit\Core\Exception
 */
class StatusException extends \Exception {

    /**
     * @var integer
     */
    private $statusCode;


    public function __construct($message, $statusCode, $code = null) {
        parent::__construct($message, $code ?? 0);
        $this->statusCode = $statusCode;
    }

    /**
     *
     *
     * @return integer
     */
    public function getStatusCode() {
        return $this->statusCode;
    }


}
