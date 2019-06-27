<?php


namespace Kinikit\Core;

use ErrorException;
use Kinikit\Core\Util\Logging\Logger;
use Kinikit\MVC\Framework\HTTP\HttpSession;


/**
 * Generic initialiser.  This should be called to initialise the framework with default setup stuff.
 * This should be initialised explicitly for e.g. Command Line Applications but is called automatically
 * by the Dispatcher if using MVC framework.
 *
 * Class Init
 * @package Kinikit\Core
 */
class Init {

    /**
     * Init constructor.  Automatically sets things up.
     */
    public function __construct() {

        // Set the default timezone to prevent issues with dates
        date_default_timezone_set("Europe/London");

        // Set a catch all error handler
        set_error_handler(array($this, "genericErrorHandler"), E_ALL);

        // Register an autoload function for application namespaces.
        spl_autoload_register(array($this, "genericClassAutoloader"));


        if (file_exists("ApplicationAnnouncement.php")) {
            include_once "ApplicationAnnouncement.php";
            $appAnnouncement = new \ApplicationAnnouncement ();
            $appAnnouncement->announce();
        }


        // Start a session early in the flow
        HttpSession::instance();
    }


    /**
     * Generic class auto loader.
     */
    function genericClassAutoloader($class) {

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
    function genericErrorHandler($severity, $message, $file, $line) {
        Logger::log($message . ": at line $line in file $file");
        throw new ErrorException($message, 0, $severity, $file, $line);
    }

}
