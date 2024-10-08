<?php

namespace Kinikit\Core\Configuration;


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
        parent::__construct("Config/" . ($envVariable ?: "config.txt"));
    }

    /**
     * Get the singleton configuration instance.
     *
     * @return Configuration
     */
    public static function instance($newInstance = false) {
        if (self::$instance === null || $newInstance) {
            self::$instance = new Configuration();
        }

        return self::$instance;
    }

    /**
     * Static get parameter function.  Convenience quicky for calling internal getParameter
     *
     * @param string $key
     * @return string|null
     */
    public static function readParameter(string $key): ?string {
        return self::instance()->getParameter($key);
    }

}
