<?php

namespace Kinikit\Core\Util\Caching;
use Kinikit\Core\Util\HTTP\HttpSession;


/**
 * Session cache which caches objects by key with an optional timeout flag.
 */
class SessionCache {

    private static $instance;

    /**
     * Static singleton method
     *
     * @return SessionCache
     */
    public static function instance() {
        if (!SessionCache::$instance) {
            SessionCache::$instance = new SessionCache();
        }

        return SessionCache::$instance;
    }

    /**
     * Cache an object with a key and an optional timeout in seconds.
     *
     * @param $key
     * @param $object
     */
    public function cacheObject($key, $object, $timeoutSeconds = -1) {

        $cache = HttpSession::instance()->getValue("__SESSION_CACHED_ITEMS");
        if (!is_array($cache)) {
            $cache = array();
        }

        $cache[$key] = $object;

        HttpSession::instance()->setValue("__SESSION_CACHED_ITEMS", $cache);


        if ($timeoutSeconds > 0) {
            $timeouts = HttpSession::instance()->getValue("__SESSION_CACHE_TIMEOUTS");
            if (!is_array($timeouts)) {
                $timeouts = array();
            }

            $dateTime = new \DateTime();
            $dateTime->add(new \DateInterval("PT" . $timeoutSeconds . "S"));
            $timeouts[$key] = $dateTime->format("d/m/Y H:i:s");

            HttpSession::instance()->setValue("__SESSION_CACHE_TIMEOUTS", $timeouts);
        }


    }


    /**
     * Get a cached object by key
     *
     * @param $key
     */
    public function getCachedObject($key) {
        $cache = HttpSession::instance()->getValue("__SESSION_CACHED_ITEMS");

        $timeouts = HttpSession::instance()->getValue("__SESSION_CACHE_TIMEOUTS");
        if (is_array($timeouts) && isset($timeouts[$key])) {
            $now = new \DateTime();
            $expiry = date_create_from_format("d/m/Y H:i:s", $timeouts[$key]);

            if ($now >= $expiry) {
                return null;
            }
        }

        return isset($cache[$key]) ? $cache[$key] : null;
    }


    /**
     * Flush the cache.
     */
    public function flushCache() {
        HttpSession::instance()->setValue("__SESSION_CACHED_ITEMS", array());
        HttpSession::instance()->setValue("__SESSION_CACHE_TIMEOUTS", array());

    }


}