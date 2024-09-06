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

    public function log($message, $category);

}