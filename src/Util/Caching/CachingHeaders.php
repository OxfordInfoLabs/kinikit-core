<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 07/03/2019
 * Time: 20:01
 */

namespace Kinikit\Core\Util\Caching;

/**
 * Augment the current request with caching headers
 *
 * Class CachingHeaders
 * @package Kinikit\Core\Util\Caching
 *
 */
class CachingHeaders {

    private static $instance;

    // Private construction only via static.
    private function __construct() {
    }

    /**
     * Singleton pattern
     *
     * @return CachingHeaders
     */
    public static function instance() {
        if (!self::$instance) {
            self::$instance = new CachingHeaders();
        }

        return self::$instance;
    }


    /**
     * Add caching headers to the current request.
     */
    public function addCachingHeaders($numberOfMinutes = 60, $mustRevalidate = true) {

        // Add cache control header if revalidate
        $numberOfSeconds = $numberOfMinutes * 60;
        if ($mustRevalidate)
            header("Cache-Control: max-age=" . ($numberOfSeconds) . " must-revalidate");
        else
            header("Cache-Control: max-age=" . ($numberOfSeconds));

        // Add the Expires header.
        $expStr = "Expires: " . gmdate("D, d M Y H:i:s", time() + $numberOfSeconds) . " GMT";
        header($expStr);

        // Add the last modified header.
        $lastModified = "Last-Modified: " . gmdate("D, d M Y H:i:s", time()) + " GMT";
        header($lastModified);

        header_remove("Pragma");

    }

}