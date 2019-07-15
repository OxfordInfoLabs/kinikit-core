<?php


namespace Kinikit\Core\Configuration;


class FileResolverTest extends \PHPUnit\Framework\TestCase {


    public function testConfiguredSearchPathsIncludeCurrentDirectory() {

        $fileResolver = new FileResolver();
        $this->assertEquals(".", $fileResolver->getSearchPaths()[0]);

    }

    public function testConfiguredSearchPathsIncludeConfiguredSearchPaths() {

        Configuration::instance()->addParameter("search.paths", "../src;../vendor/symfony");

        $fileResolver = new FileResolver();
        $this->assertEquals(".", $fileResolver->getSearchPaths()[0]);
        $this->assertEquals("../src", $fileResolver->getSearchPaths()[1]);
        $this->assertEquals("../vendor/symfony", $fileResolver->getSearchPaths()[2]);


    }


    public function testCanAddCustomSearchPathsProgrammatically() {

        $fileResolver = new FileResolver();
        $fileResolver->addSearchPath("./Template");

        $searchPaths = $fileResolver->getSearchPaths();
        $this->assertEquals("./Template", array_pop($searchPaths));

    }


    public function testCanResolveFilesUsingConfiguredSearchPaths() {

        // Add mixture of configured paths
        Configuration::instance()->addParameter("search.paths", "../src;../vendor/symfony");
        $fileResolver = new FileResolver();
        $fileResolver->addSearchPath("./Template");

        $this->assertEquals("./autoloader.php", $fileResolver->resolveFile("autoloader.php"));
        $this->assertEquals("./Template/MustacheTemplateParserTest.php", $fileResolver->resolveFile("Template/MustacheTemplateParserTest.php"));

        $this->assertEquals("../src/Init.php", $fileResolver->resolveFile("Init.php"));
        $this->assertEquals("../vendor/symfony/polyfill-ctype/bootstrap.php", $fileResolver->resolveFile("polyfill-ctype/bootstrap.php"));

        $this->assertNull($fileResolver->resolveFile("test.php"));

    }


    public function testResolutionIsCaseInsensitiveIfRequested() {


        // Add mixture of configured paths
        Configuration::instance()->addParameter("search.paths", "../src;../vendor/symfony");
        $fileResolver = new FileResolver();
        $fileResolver->addSearchPath("./Template");


        $this->assertEquals("./Template/MustacheTemplateParserTest.php", $fileResolver->resolveFile("template/mustacheTEMPLATEPARSERTEST.php", true));
        $this->assertEquals("../vendor/symfony/polyfill-ctype/bootstrap.php", $fileResolver->resolveFile("polyfill-CType/BOOTSTRAP.php", true));


    }

}
