<?php

namespace Kinikit\Core\Caching;

use Kinikit\Core\Logging\Logger;

class BaseCachingProvider implements CacheProvider {

    public function lookup(string $key, callable $generatorFunction, int $ttl, array $params = []) {

        // Check if it exists in the cache
        $value = $this->get($key);
        Logger::log("Attempting to read cache key: " . $key);

        // If so, return the output
        if ($value) {
            Logger::log("Cache Returned:");
            Logger::log($value);
            return $value;
        }

        // Execute the callable
        $value = $generatorFunction(...$params);
        Logger::log("Executed callback - ttl is $ttl");

        // Cache the output
        if ($ttl > 0) {
            try {
                Logger::log("Setting key $key with value:");
                Logger::log($value);
                $this->set($key, $value, $ttl);
            } catch (\Exception $e) {
                Logger::log($e);
            }
        }

        Logger::log("Cached.");

        return $value;

    }

    // Define in actual cache providers
    public function set(string $key, mixed $value, int $ttl): void {
    }

    // Define in actual cache providers
    public function get(string $key) {
    }

    // Define in actual providers
    public function clearCache(): void {
    }
}