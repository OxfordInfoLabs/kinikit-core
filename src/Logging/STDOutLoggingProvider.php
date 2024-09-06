<?php

namespace Kinikit\Core\Logging;

use Kinikit\Core\Binding\ObjectBinder;
use Kinikit\Core\DependencyInjection\Container;

/**
 * Logs JSON blocks to STD out – compatible with Google's AppEngine
 */
class STDOutLoggingProvider implements LoggingProvider {

    private ObjectBinder $objectBinder;

    public function __construct() {
        $this->objectBinder = Container::instance()->get(ObjectBinder::class);
    }

    public function log($message, $category = null): void {

        $log = [];

        if ($message instanceof \Exception) {
            // Do Exception-y things
            $log["severity"] = "warning";
            $log["message"] = $message->getMessage();
            $log["exception_type"] = get_class($message);
            fwrite(STDOUT, json_encode($log));
        } else if (is_object($message)) {
            // Do object-y things
            $log["type"] = get_class($message);
            $log["object" ] = $this->objectBinder->bindToArray($message);
            fwrite(STDOUT, json_encode($log));
        } else if (is_array($message)) {
            // Do array-y things
            $log = var_export($message, true);
            fwrite(STDOUT, $log);
        } else {
            // Assuming text based
            fwrite(STDOUT, $message);
        }

    }
}