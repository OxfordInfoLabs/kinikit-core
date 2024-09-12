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
        $logger->log($message, $severity);

    }

} 
