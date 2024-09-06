<?php

namespace Kinikit\Core\Logging;

use Kinikit\Core\Configuration\Configuration;

class FileLoggingProvider implements LoggingProvider {

    public function log($message, $category) {

        if ($message instanceof \Exception) {
            $category = Logger::ERROR;
            $message = get_class($message) . "\n" . $message->getMessage();
        } else if (is_object($message)) {
            $message = get_class($message) . "\n" . var_export($message, true);
        } else if (is_array($message)) {
            $message = "Array\n" . var_export($message, true);
        }

        $message = "\n" . date("d/m/Y H:i:s") . "\t" . $category . "\t" . $message;

        $fileLocation =
            Configuration::readParameter("log.file") ?: "/tmp/application.log";

        // Append a string to the log file.
        file_put_contents($fileLocation, $message, FILE_APPEND);
    }
}