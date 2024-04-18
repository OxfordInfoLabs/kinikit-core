<?php

namespace Kinikit\Core\Exception;

use Throwable;

class DebugException extends \Exception {

    /**
     * @var string
     */
    private string $debugMessage;

    /**
     * @param string $message
     * @param int $code
     * @param null|Throwable $previous
     * @param string $debugMessage
     */
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null, string $debugMessage = "") {
        parent::__construct($message, $code, $previous);
        $this->debugMessage = $debugMessage;
    }

    /**
     * @param string $debugMessage
     * @return void
     */
    public function setDebugMessage(string $debugMessage): void {
        $this->debugMessage = $debugMessage;
    }

    public function returnDebugMessage(): string {
        return $this->debugMessage;
    }

}