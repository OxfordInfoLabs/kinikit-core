<?php


namespace Kinikit\Core;


use ErrorException;
use Kinikit\Core\Configuration\Configuration;
use Kinikit\Core\Logging\Logger;

class Init {

    /**
     * Constructor
     *
     * Init constructor.
     */
    public function __construct() {
        $this->process();
    }

    // Process method
    private function process(): void {

        // Register an autoload function for application namespaces.
        spl_autoload_register([$this, "genericClassAutoloader"]);

        // Set the default timezone to prevent issues with dates
        $configuredTimezone = Configuration::readParameter("default.timezone");
        date_default_timezone_set($configuredTimezone ?: "Europe/London");

        // Set a catch all error handler
        set_error_handler([$this, "genericErrorHandler"], E_ALL);

    }


    /**
     * Generic class auto loader.
     */
    private function genericClassAutoloader($class) {

        if (Configuration::readParameter("application.namespace")) {
            $newClass = str_replace(Configuration::readParameter("application.namespace") . "\\", "", $class);

            // If no application namespace substitution return false
            if ($newClass === $class) {
                return false;
            }

            $file = str_replace('\\', DIRECTORY_SEPARATOR, $newClass) . '.php';

            if (Configuration::readParameter("application.namespace.root")) {
                $file = Configuration::readParameter("application.namespace.root") . "/$file";
            }

            if (file_exists($file)) {
                require $file;
                return true;
            }

            return false;
        }

    }


    /**
     * Generic Exception handler for fatal errors
     *
     * @param $severity
     * @param $message
     * @param $file
     * @param $line
     * @throws ErrorException
     */
    public function genericErrorHandler($severity, $message, $file, $line): void {
        $logEntry = $message . ": at line $line in file $file";

        $severityLevel = match ($severity) {
            E_NOTICE, E_USER_ERROR, E_DEPRECATED => 5,
            default => 4
        };

        Logger::log($logEntry, $severityLevel);

        if ($severity !== E_DEPRECATED) {
            throw new ErrorException($message, 0, $severity, $file, $line);
        }

    }


}
