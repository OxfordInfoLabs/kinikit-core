<?php


namespace Kinikit\Core;

use ErrorException;
use Kinikit\Core\Configuration\SearchNamespaces;
use Kinikit\Core\DependencyInjection\Container;
use Kinikit\Core\Logging\Logger;
use Kinikit\Core\Configuration\Configuration;


/**
 * Generic initialiser.  This should be called to initialise the framework with default setup stuff.
 * This should be initialised explicitly for e.g. Command Line Applications but is called automatically
 * by the Dispatcher if using MVC framework.
 *
 * Class Init
 * @package Kinikit\Core
 *
 * @noProxy
 */
class Init {

    /**
     * @var SearchNamespaces
     */
    private $searchNamespaces;

    /**
     * Init constructor.  Automatically sets things up.
     *
     * @param SearchNamespaces $searchNamespaces
     */
    public function __construct($searchNamespaces) {
        $this->searchNamespaces = $searchNamespaces;
        $this->process();
    }


    // Process core init logic.
    private function process() {

        // Register an autoload function for application namespaces.
        spl_autoload_register(array($this, "genericClassAutoloader"));

        // Set the default timezone to prevent issues with dates
        $configuredTimezone = Configuration::readParameter("default.timezone");
        date_default_timezone_set($configuredTimezone ? $configuredTimezone : "Europe/London");

        // Set a catch all error handler
        set_error_handler(array($this, "genericErrorHandler"), E_ALL);


        $namespaces = $this->searchNamespaces->getNamespaces();

        // Process search namespaces in reverse order.
        for ($i = sizeof($namespaces) - 1; $i >= 0; $i--) {
            if (class_exists($namespaces[$i] . "\\Bootstrap")) {
                $bootstrap = Container::instance()->get($namespaces[$i] . "\\Bootstrap");
                $bootstrap->setup();
            }
        }

    }


    /**
     * Generic class auto loader.
     */
    private function genericClassAutoloader($class) {

        if (Configuration::readParameter("application.namespace")) {
            $class = str_replace(Configuration::readParameter("application.namespace") . "\\", "", $class);
            $file = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';

            if (Configuration::readParameter("application.namespace.root"))
                $file = Configuration::readParameter("application.namespace.root") . "/$file";

            if (file_exists($file)) {
                require $file;
                return true;
            } else
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
    public function genericErrorHandler($severity, $message, $file, $line) {
        Logger::log($message . ": at line $line in file $file");
        throw new ErrorException($message, 0, $severity, $file, $line);
    }

}
