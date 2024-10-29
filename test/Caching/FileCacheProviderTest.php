<?php

namespace Kinikit\Core\Caching;

use Kinikit\Core\Configuration\Configuration;
use Kinikit\Core\DependencyInjection\Container;
use PHPUnit\Framework\TestCase;

include_once "autoloader.php";

class FileCacheProviderTest extends TestCase {

    public FileCacheProvider $cacheProvider;

    public string $cacheDir;

    public function setUp(): void {
        $this->cacheProvider = Container::instance()->get(FileCacheProvider::class);
        $this->cacheDir = Configuration::readParameter("files.root") . "/cache";

        if (!file_exists($this->cacheDir)) {
            mkdir($this->cacheDir, 777, true);
        }
    }

    public function tearDown(): void {
        foreach (glob($this->cacheDir . "/*") as $file) {
            unlink($file);
        }
    }

    public function testCachingFlowForSimpleFunction() {

        // Can it return and write to an empty cache?
        $expiryTime = date_create("+30 seconds")->format("YmdHis");
        $hashedKey = md5("someKey");
        $cacheFile = $this->cacheDir . "/$hashedKey-$expiryTime.txt";

        $myFunction = function () {
            return "Hello!";
        };

        $response = $this->cacheProvider->lookup("someKey", $myFunction, 30);

        $this->assertEquals("Hello!", $response);
        $this->assertFileExists($cacheFile);
        $this->assertEquals('s:6:"Hello!";', file_get_contents($cacheFile));


        // Does it return an unexpired cache value
        $myFunction = function () {
            return "I'm a new value!";
        };

        $response = $this->cacheProvider->lookup("someKey", $myFunction, 20);
        $newExpiryTime = date_create("+20 seconds")->format("YmdHis");

        $this->assertEquals("Hello!", $response);
        $this->assertFileExists($cacheFile);
        $this->assertFileDoesNotExist($this->cacheDir . "/$hashedKey-$newExpiryTime.txt");

        unlink($cacheFile);

        // Does it ignore a stale cache?
        $oldExpiryTime = date_create("-30 seconds")->format("YmdHis");
        $hashedKey = md5("yetAnotherKey");
        $cacheFile = $this->cacheDir . "/$hashedKey-$oldExpiryTime.txt";

        file_put_contents($cacheFile, "My stale cached value.");

        $myFunction = function () {
            return "I'm a new value!";
        };

        $response = $this->cacheProvider->lookup("yetAnotherKey", $myFunction, 30);
        $newExpiryTime = date_create("+30 seconds")->format("YmdHis");

        $this->assertEquals("I'm a new value!", $response);
        $this->assertFileDoesNotExist($cacheFile);
        $this->assertFileExists($this->cacheDir . "/$hashedKey-$newExpiryTime.txt");
        $this->assertEquals('s:16:"I\'m a new value!";', file_get_contents($this->cacheDir . "/$hashedKey-$newExpiryTime.txt"));

    }


    public function testCachingFlowDoesWorkForObjectReturningFunction() {

        // Does it return and write to the cache?
        $expiryTime = date_create("+30 seconds")->format("YmdHis");
        $hashedKey = md5("objectKey");
        $cacheFile = $this->cacheDir . "/$hashedKey-$expiryTime.txt";

        $myFunction = function () {
            return new SimpleObject("blue");
        };

        $response = $this->cacheProvider->lookup("objectKey", $myFunction, 30);

        $this->assertEquals(new SimpleObject("blue"), $response);
        $this->assertFileExists($cacheFile);
        $this->assertEquals(serialize(new SimpleObject("blue")), file_get_contents($cacheFile));

        // Does it read from the cache and convert to the object?
        $newExpiryTime = date_create("+10 seconds")->format("YmdHis");
        $wouldBeCacheFile = $this->cacheDir . "/$hashedKey-$newExpiryTime.txt";

        $myFunction = function () {
            return new SimpleObject("green");
        };

        $response = $this->cacheProvider->lookup("objectKey", $myFunction, 10, [], SimpleObject::class);

        $this->assertInstanceOf(SimpleObject::class, $response);
        $this->assertEquals("blue", $response->getColour());
        $this->assertFileExists($cacheFile);
        $this->assertFileDoesNotExist($wouldBeCacheFile);

    }

    public function testCanClearCache() {

        file_put_contents($this->cacheDir . "/one.txt", "#content");
        file_put_contents($this->cacheDir . "/two.txt", "#content");
        file_put_contents($this->cacheDir . "/three.txt", "#content");

        $this->cacheProvider->clearCache();

        $this->assertFileDoesNotExist($this->cacheDir . "/one.txt");
        $this->assertFileDoesNotExist($this->cacheDir . "/two.txt");
        $this->assertFileDoesNotExist($this->cacheDir . "/three.txt");

    }

}