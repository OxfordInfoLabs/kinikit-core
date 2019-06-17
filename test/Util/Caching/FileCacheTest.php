<?php

namespace Kinikit\Core\Util\Caching;

include_once "autoloader.php";

class FileCacheTest extends \PHPUnit\Framework\TestCase {

    /**
     * @var FileCache
     */
    private $fileCache;

    public function setUp():void {
        // Remove cache upfront
        passthru("rm -rf /tmp/filecache");

        $this->fileCache = new FileCache("/tmp/filecache");
    }


    public function testCanCacheFileUsingSimpleStringKey() {

        $this->fileCache->cacheFile("simplekey", "Hello world");

        $this->assertTrue(file_exists("/tmp/filecache/simplekey"));
        $this->assertEquals("Hello world", file_get_contents("/tmp/filecache/simplekey"));

        $this->fileCache->cacheFile("anotherkey", "My Wonderful Monkey");

        $this->assertTrue(file_exists("/tmp/filecache/anotherkey"));
        $this->assertEquals("My Wonderful Monkey", file_get_contents("/tmp/filecache/anotherkey"));


        // Check we can retrieve both files
        $this->assertEquals("Hello world", $this->fileCache->getCachedFile("simplekey"));
        $this->assertEquals("My Wonderful Monkey", $this->fileCache->getCachedFile("anotherkey"));


    }


    public function testCanCacheFileUsingCompoundArrayKey() {

        $this->fileCache->cacheFile(array("documents", "mark", "letters",
            "MyLetter.doc"), "A new document in a sub tree");

        $this->assertTrue(file_exists("/tmp/filecache/documents/mark/letters/MyLetter.doc"));
        $this->assertEquals("A new document in a sub tree", file_get_contents("/tmp/filecache/documents/mark/letters/MyLetter.doc"));
        $this->assertEquals("A new document in a sub tree", $this->fileCache->getCachedFile(array("documents", "mark",
            "letters", "MyLetter.doc")));


        $this->fileCache->cacheFile(array("plans", "Bingo"), "Bingo Bongo");

        $this->assertTrue(file_exists("/tmp/filecache/plans/Bingo"));
        $this->assertEquals("Bingo Bongo", file_get_contents("/tmp/filecache/plans/Bingo"));
        $this->assertEquals("Bingo Bongo", $this->fileCache->getCachedFile(array("plans", "Bingo")));


    }


    public function testCannotRetrieveCacheEntryForNoneLeaf() {
        $this->fileCache->cacheFile(array("documents", "mark", "letters",
            "MyLetter.doc"), "A new document in a sub tree");

        $this->assertNull($this->fileCache->getCachedFile(array("documents")));
        $this->assertNull($this->fileCache->getCachedFile(array("documents", "mark")));
        $this->assertNull($this->fileCache->getCachedFile(array("documents", "mark", "letters")));


    }


    public function testCanPurgeCachedFilesIncludingHierarchicalOnes() {

        $this->fileCache->cacheFile(array("documents", "mark", "letters",
            "MyLetter.doc"), "A new document in a sub tree");

        $this->fileCache->cacheFile(array("documents", "mark", "letters",
            "AnotherLetter.doc"), "A second new document in a sub tree");


        $this->fileCache->purgeCachedFiles(array("documents", "mark", "letters", "MyLetter.doc"));

        $this->assertFalse(file_exists("/tmp/filecache/documents/mark/letters/MyLetter.doc"));
        $this->assertTrue(file_exists("/tmp/filecache/documents/mark/letters/AnotherLetter.doc"));


        // Purge hierarchy
        $this->fileCache->purgeCachedFiles(array("documents", "mark"));
        $this->assertFalse(file_exists("/tmp/filecache/documents/mark"));
        $this->assertTrue(file_exists("/tmp/filecache/documents"));

        $this->fileCache->purgeCachedFiles(array("documents"));
        $this->assertFalse(file_exists("/tmp/filecache/documents"));
        $this->assertTrue(file_exists("/tmp/filecache"));


    }

}
