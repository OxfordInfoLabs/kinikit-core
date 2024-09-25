<?php

namespace Kinikit\Core\Logging;

/**
 * @implementationConfigParam log.provider
 *
 * @implementation file Kinikit\Core\Logging\FileLoggingProvider
 * @implementation stdout Kinikit\Core\Logging\STDOutLoggingProvider
 * @implementation gcloud Kinikit\Core\Logging\GCloudLoggingProvider
 *
 * @defaultImplementation Kinikit\Core\Logging\FileLoggingProvider
 */
interface LoggingProvider {

    /**
     * @param mixed $message
     * @param int $severity
     * @return void
     */
    public function log($message, int $severity): void;

}