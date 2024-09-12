<?php

namespace Kinikit\Core\Logging;

use Kinikit\Core\Binding\ObjectBinder;
use Kinikit\Core\DependencyInjection\Container;

/**
 * Logs JSON blocks to STD out – compatible with Google's AppEngine
 */
class STDOutLoggingProvider implements LoggingProvider {

    public function log(string $message, int $severity): void {

        $log = [
            "severity" => Logger::SEVERITY_MAP[$severity],
            "message" => $message
        ];

        fwrite(STDOUT, json_encode($log));

    }

    public function logArray(array $array, int $severity): void {
        $message = var_export($array, true);
        $this->log($message, $severity);
    }

    public function logObject($object, int $severity): void {
        $message = var_export($object, true);
        $this->log($message, $severity);
    }

    public function logException(\Exception $exception, int $severity): void {
        $className = $this->getClassName($exception);
        $message = $className . ": " . $exception->getMessage();
        $this->log($message, $severity);
    }

    private function getClassName($object): string {
        $classNameSegments = explode("\\", get_class($object));
        return array_pop($classNameSegments);
    }
}