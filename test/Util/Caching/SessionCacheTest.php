<?php

namespace Kinikit\Core\Util\Caching;


use Kinikit\Core\Util\HTTP\HttpSession;
use PHPUnit\Framework\TestCase;

include_once "autoloader.php";

/**
 * Test cases for the session cache.
 */
class SessionCacheTest extends TestCase {

    private $sessionCache;

    public function setUp(): void {
        $this->sessionCache = new SessionCache();
    }

    public function testCachedItemsAreStoredCorrectlyInHTTPSessionAndRetrievable() {

        $this->sessionCache->cacheObject("my-new-object", "Wandering Aimlessly In The Night");

        $cache = HttpSession::instance()->getValue("__SESSION_CACHED_ITEMS");
        $this->assertTrue(is_array($cache));
        $this->assertEquals("Wandering Aimlessly In The Night", $cache["my-new-object"]);

        $this->assertEquals("Wandering Aimlessly In The Night", $this->sessionCache->getCachedObject("my-new-object"));


        $this->sessionCache->cacheObject("my-best-mate", array(1, 2, 3, 4, 5));

        $cache = HttpSession::instance()->getValue("__SESSION_CACHED_ITEMS");
        $this->assertTrue(is_array($cache));
        $this->assertEquals(array(1, 2, 3, 4, 5), $cache["my-best-mate"]);

        $this->assertEquals(array(1, 2, 3, 4, 5), $this->sessionCache->getCachedObject("my-best-mate"));


    }


    public function testUncachedItemsReturnNullForRetrieval() {
        $this->assertNull($this->sessionCache->getCachedObject("Non-Existent"));
    }


    public function testCachedItemsWhichTimeoutBecomeNullAfterTimeout() {

        $this->sessionCache->cacheObject("my-new-object", "Wandering Aimlessly In The Night", 1);
        $this->sessionCache->cacheObject("my-longer-object", "Bingo", 2);
        $this->assertEquals("Wandering Aimlessly In The Night", $this->sessionCache->getCachedObject("my-new-object"));
        $this->assertEquals("Bingo", $this->sessionCache->getCachedObject("my-longer-object"));

        sleep(1);

        $this->assertNull($this->sessionCache->getCachedObject("my-new-object"));
        $this->assertEquals("Bingo", $this->sessionCache->getCachedObject("my-longer-object"));

        sleep(1);

        $this->assertNull($this->sessionCache->getCachedObject("my-new-object"));
        $this->assertNull($this->sessionCache->getCachedObject("my-longer-object"));


    }


}
