<?php

namespace Kinikit\Core\Configuration;


use Kinikit\Core\Configuration\ConfigFile;

/**
 * Main Application configuration file.  This is a singleton instance, and has various application specific
 * bits which are useful such as module enumeration.
 *
 */
class Configuration extends ConfigFile {

    private static $instance = null;

    // private only constructor here, since we should only access this through other instance methods.
    public function __construct() {
        $envVariable = getenv("KINIKIT_CONFIG_FILE");
        parent::__construct("Config/" . ($envVariable ? $envVariable : "config.txt"));
    }

    /**
     * Get the singleton configuration instance.
     *
     * @return Configuration   
     */
    public static function instance($newInstance = false) {
        if (Configuration::$instance == null || $newInstance) {
            Configuration::$instance = new Configuration ();
        }

        return Configuration::$instance;
    }

    /**
     * Static get parameter function.  Convenience quicky for calling internal getParameter
     *
     * @param string $key
     */
    public static function readParameter($key) {
        return Configuration::instance()->getParameter($key);
    }

}

?>
