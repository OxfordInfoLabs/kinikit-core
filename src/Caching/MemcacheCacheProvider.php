<?php

namespace Kinikit\Core\Caching;

use Google\AppEngine\Api\Memcache\Memcache;
use Kinikit\Core\DependencyInjection\Container;
use Kinikit\Core\Logging\Logger;
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

    public function lookup(string $key, callable $generatorFunction, int $ttl, array $params = [], ?string $returnClass = null) {

        // Check if it exists in the cache
        $value = $this->memcache->get($key);

        // If so, return the output
        if ($value) {
            return $value;
        }

        // Execute the callable
        $value = $generatorFunction(...$params);

        // Cache the output
        try {
            $this->memcache->set($key, $value, null, $ttl);
        } catch (\Exception $e) {
            Logger::log($e);
        }

        return $value;
    }

    public function clearCache(): void {
        $this->memcache->flush();
    }

}