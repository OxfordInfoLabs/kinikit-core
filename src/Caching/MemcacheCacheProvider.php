<?php

namespace Kinikit\Core\Caching;

use Google\AppEngine\Api\Memcache\Memcache;
use Kinikit\Core\DependencyInjection\Container;
use Kinikit\Core\Serialisation\JSON\JSONToObjectConverter;
use Kinikit\Core\Serialisation\JSON\ObjectToJSONConverter;

class MemcacheCacheProvider implements CacheProvider {

    private $memcache;

    private ObjectToJSONConverter $objectToJSONConverter;

    private JSONToObjectConverter $JSONToObjectConverter;

    public function __construct(Memcache $memcache) {
        $this->memcache = $memcache;
        $this->objectToJSONConverter = Container::instance()->get(ObjectToJSONConverter::class);
        $this->JSONToObjectConverter = Container::instance()->get(JSONToObjectConverter::class);
    }

    public function lookup(string $key, callable $generatorFunction, int $ttl, string $returnClass = null) {

        // Check if it exists in the cache
        $value = $this->memcache->get($key);

        // If so, return the output
        if ($value) {
            return $value;
        }

        // Execute the callable
        $value = $generatorFunction();

        // Cache the output
        $this->memcache->set($key, $value, null, $ttl);

        return $value;
    }

    public function clearCache(): void {
        $this->memcache->flush();
    }

}