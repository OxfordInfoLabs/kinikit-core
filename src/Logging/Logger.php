<?php

namespace Kinikit\Core\Logging;

use Kinikit\Core\Configuration\Configuration;


/**
 * Simple logger class for writing log entries to the standard configured log file.
 *
 * Class Logger
 */
class Logger {
    const GENERAL = "GENERAL";
    const PROFILING = "PROFILING";
    const ERROR = "ERROR";

    public static function log($message, $category = self::GENERAL) {

        if ($message instanceof \Exception) {
            $category = self::ERROR;
            $message = get_class($message) . "\n" . $message->getMessage();
        } else if (is_object($message)) {
            $message = get_class($message) . "\n" . var_export($message, true);
        } else if (is_array($message)) {
            $message = "Array\n" . var_export($message, true);
        }

        $message = "\n" . date("d/m/Y H:i:s") . "\t" . $category . "\t" . $message;

        $fileLocation =
            Configuration::readParameter("log.file") ? Configuration::readParameter("log.file") : "/tmp/application.log";

        // Append a string to the log file.
        file_put_contents($fileLocation, $message, FILE_APPEND);

    }

} 
