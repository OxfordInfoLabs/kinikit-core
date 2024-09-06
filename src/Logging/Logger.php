<?php

namespace Kinikit\Core\Logging;

use Kinikit\Core\Configuration\Configuration;
use Kinikit\Core\DependencyInjection\Container;


/**
 * Simple logger class for writing log entries to the standard configured log file.
 *
 * Class Logger
 */
class Logger {

    const GENERAL = "GENERAL";
    const PROFILING = "PROFILING";
    const ERROR = "\033[31mERROR:\033[0m";
    const WARNING = "\033[33mWARNING:\033[0m";

    public static function log($message, $category = self::GENERAL) {

        $logger = Container::instance()->get(LoggingProvider::class);

        $logger->log($message, $category);

    }

} 
