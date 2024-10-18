<?php

namespace Kinikit\Core\Caching;

/**
 * @implementationConfigParam cache.provider
 *
 * @implementation file Kinikit\Core\Caching\FileCacheProvider
 *
 * @defaultImplementation Kinikit\Core\Logging\FileLoggingProvider
 */
interface CacheProvider {

    public function lookup(string $key, callable $generatorFunction, int $ttl, string $returnClass = null);

    public function clearCache(): void;

}