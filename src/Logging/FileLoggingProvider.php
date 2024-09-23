<?php

namespace Kinikit\Core\Logging;

use Kinikit\Core\Configuration\Configuration;

class FileLoggingProvider implements LoggingProvider {

    public function log(mixed $message, int $severity = 7): void {

        if ($message instanceof \Exception) {

            if ($severity > 4)
                $severity = 4; // Exceptions have a minimum severity of 4

            $message = get_class($message) . "\n" . $message->getMessage();
            $this->log($message, $severity);

        } else if (is_array($message)) {

            $message = "Array\n" . var_export($message, true);
            $this->writeLog($message, $severity);

        } else if (is_object($message)) {

            $message = get_class($message) . "\n" . var_export($message, true);
            $this->writeLog($message, $severity);

        } else

            $this->writeLog($message, $severity);


    }

    private function writeLog(string $logEntry, int $severity): void {

        $severity = strtoupper(Logger::SEVERITY_MAP[$severity]);

        $message = "\n" . date("d/m/Y H:i:s") . "\t" . $severity . "\t" . $logEntry;

        $fileLocation =
            Configuration::readParameter("log.file") ?: "/tmp/application.log";

        // Append a string to the log file.
        file_put_contents($fileLocation, $message, FILE_APPEND);

    }

}