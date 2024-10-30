<?php

namespace Kinikit\Core\Caching;

use Google\AppEngine\Api\Memcache\Memcache;
use Kinikit\Core\Testing\MockObjectProvider;
use PHPUnit\Framework\TestCase;

include_once "autoloader.php";

class MemcacheCacheProviderTest extends TestCase {

    private MemcacheCacheProvider $cacheProvider;

    private $memcache;

    public function setUp(): void {
        $this->memcache = MockObjectProvider::mock(Memcache::class);
        $this->cacheProvider = new MemcacheCacheProvider($this->memcache);
    }

    public function testCachingFlowForSimpleFunction() {

        // Can it return and write to the Memcache
        $myFunction = function () {
            return "Hello!";
        };

        $response = $this->cacheProvider->lookup("someKey", $myFunction, 30);

        $this->assertEquals("Hello!", $response);
        $this->assertTrue($this->memcache->methodWasCalled("set", ["someKey", "Hello!", null, 30]));


        // Does it read from the cache when a value exists?
        $myFunction = function () {
            return "I'm a new value!";
        };

        $this->memcache->returnValue("get", "Hello!", ["someKey"]);

        $response = $this->cacheProvider->lookup("someKey", $myFunction, 20);

        $this->assertEquals("Hello!", $response);

    }

    public function testCanClearCache() {

        $this->cacheProvider->clearCache();
        $this->assertTrue($this->memcache->methodWasCalled("flush"));

    }

}