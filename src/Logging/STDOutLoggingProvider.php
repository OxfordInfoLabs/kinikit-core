<?php

namespace Kinikit\Core\Logging;


/**
 * Logs JSON blocks to STD out – compatible with Google's AppEngine
 */
class STDOutLoggingProvider implements LoggingProvider {

    public function log(mixed $message, int $severity = 7): void {

        if ($message instanceof \Exception) {

            if ($severity > 4)
                $severity = 4; // Exceptions have a minimum severity of 4

            $className = $this->getClassName($message);
            $log = $className . ": " . $message->getMessage();
            $this->writeLog($log, $severity);

        } else if (is_array($message) || is_object($message)) {

            $log = var_export($message, true);
            $this->writeLog($log, $severity);

        } else
            $this->writeLog($message, $severity);

    }

    private function writeLog(string $message, int $severity): void {
        $log = [
            "severity" => Logger::SEVERITY_MAP[$severity],
            "message" => $message
        ];

        fwrite(STDOUT, json_encode($log));
    }

    private function getClassName($object): string {
        $classNameSegments = explode("\\", get_class($object));
        return array_pop($classNameSegments);
    }
}