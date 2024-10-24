<?php

namespace Kinikit\Core\Caching;

use Google\AppEngine\Api\Memcache\Memcache;
use Kinikit\Core\Logging\Logger;

class MemcacheCacheProvider extends BaseCachingProvider {

    private $memcache;

    public function __construct(Memcache $memcache) {
        $this->memcache = $memcache;
    }

    public function clearCache(): void {
        $this->memcache->flush();
    }

    public function set(string $key, mixed $value, int $ttl): void {
        $this->memcache->set($key, $value, null, $ttl);
    }

    public function get(string $key, ?string $returnClass = null) {
        return $this->memcache->get($key);
    }
}