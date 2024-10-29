<?php

namespace Kinikit\Core\Caching;

/**
 * @implementationConfigParam cache.provider
 *
 * @implementation file Kinikit\Core\Caching\FileCacheProvider
 * @implementation memcache Kinikit\Core\Caching\MemcacheCacheProvider
 *
 * @defaultImplementation Kinikit\Core\Caching\FileCacheProvider
 */
interface CacheProvider {

    // Set a value in the cache
    public function set(string $key, mixed $value, int $ttl): void;

    // Get a value from the cache
    public function get(string $key);

    // Lookup a value - get if exists, else call the function and set.
    public function lookup(string $key, callable $generatorFunction, int $ttl, array $params = []);

    // Flush the cache
    public function clearCache(): void;

}