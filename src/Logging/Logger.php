<?php

namespace Kinikit\Core\Logging;

use Kinikit\Core\DependencyInjection\Container;


/**
 * Simple logger class for writing log entries to the standard configured log file.
 *
 * Class Logger
 */
class Logger {

    const SEVERITY_MAP = [
        0 => "Emergency",
        1 => "Alert",
        2 => "Critical",
        3 => "Error",
        4 => "Warning",
        5 => "Notice",
        6 => "Informational",
        7 => "Debug"
    ];

    /**
     * @param mixed $message
     * @param int $severity
     * @return void
     */
    public static function log(mixed $message, int $severity = 7): void {

        $logger = Container::instance()->get(LoggingProvider::class);

        if ($message instanceof \Exception) {
            if ($severity > 4) $severity = 4; // Exceptions have a minimum severity of 4
            $logger->logException($message, $severity);
        } else if (is_array($message))
            $logger->logArray($message, $severity);
        else if (is_object($message))
            $logger->logObject($message, $severity);
        else
            $logger->log($message, $severity);

    }

} 
