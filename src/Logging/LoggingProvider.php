<?php

namespace Kinikit\Core\Logging;

/**
 * @implementationConfigParam log.provider
 *
 * @implementation file Kinikit\Core\Logging\FileLoggingProvider
 * @implementation stdout Kinikit\Core\Logging\STDOutLoggingProvider
 *
 * @defaultImplementation Kinikit\Core\Logging\FileLoggingProvider
 */
interface LoggingProvider {

    /**
     * @param string $message
     * @param int $severity
     * @return void
     */
    public function log(string $message, int $severity): void;

    /**
     * @param array $array
     * @param int $severity
     * @return void
     */
    public function logArray(array $array, int $severity): void;

    /**
     * @param $object
     * @param int $severity
     * @return void
     */
    public function logObject($object, int $severity): void;

    /**
     * @param \Exception $exception
     * @param int $severity
     * @return void
     */
    public function logException(\Exception $exception, int $severity): void;

}