<?php

namespace Kinikit\Core\Logging;

use Kinikit\Core\Configuration\Configuration;

class FileLoggingProvider implements LoggingProvider {

    public function log(string $message, int $severity): void {

        $severity = strtoupper(Logger::SEVERITY_MAP[$severity]);

        $message = "\n" . date("d/m/Y H:i:s") . "\t" . $severity . "\t" . $message;

        $fileLocation =
            Configuration::readParameter("log.file") ?: "/tmp/application.log";

        // Append a string to the log file.
        file_put_contents($fileLocation, $message, FILE_APPEND);
    }

    public function logArray(array $array, int $severity): void {
        $message = "Array\n" . var_export($array, true);
        $this->log($message, $severity);
    }

    public function logObject($object, int $severity): void {
        $message = get_class($object) . "\n" . var_export($object, true);
        $this->log($message, $severity);
    }

    public function logException(\Exception $exception, int $severity): void {
        $message = get_class($exception) . "\n" . $exception->getMessage();
        $this->log($message, $severity);
    }
}