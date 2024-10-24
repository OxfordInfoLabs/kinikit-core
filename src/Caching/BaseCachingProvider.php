<?php

namespace Kinikit\Core\Caching;

use Kinikit\Core\Logging\Logger;

class BaseCachingProvider implements CacheProvider {

    public function lookup(string $key, callable $generatorFunction, int $ttl, array $params = [], ?string $returnClass = null) {

        // Check if it exists in the cache
        $value = $this->get($key, $returnClass);

        // If so, return the output
        if ($value) {
            return $value;
        }

        // Execute the callable
        $value = $generatorFunction(...$params);

        // Cache the output
        try {
            $this->set($key, $value, $ttl);
        } catch (\Exception $e) {
            Logger::log($e);
        }

        return $value;

    }

    // Define in actual cache providers
    public function set(string $key, mixed $value, int $ttl): void {

    }

    // Define in actual cache providers
    public function get(string $key, ?string $returnClass = null) {

    }

    // Define in actual providers
    public function clearCache(): void {

    }
}