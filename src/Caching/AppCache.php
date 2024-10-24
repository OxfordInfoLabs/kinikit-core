<?php

namespace Kinikit\Core\Caching;

use Kinikit\Core\DependencyInjection\Container;

class AppCache {

    public static function lookup(string $key, callable $generatorFunction, int $ttl, array $params = [], ?string $returnClass = null): mixed {
        return Container::instance()->get(CacheProvider::class)->lookup($key, $generatorFunction, $ttl, $params, $returnClass);
    }

    public static function clearCache(): void {
        Container::instance()->get(CacheProvider::class)->clearCache();
    }

}