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

    public function lookup(string $key, callable $generatorFunction, int $ttl, string $returnClass = null);

    public function clearCache(): void;

}