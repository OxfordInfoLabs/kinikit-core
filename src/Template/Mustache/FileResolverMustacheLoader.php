<?php


namespace Kinikit\Core\Template\Mustache;


use Kinikit\Core\Configuration\FileResolver;
use Mustache_Source;

class FileResolverMustacheLoader implements \Mustache_Loader {

    /**
     * @var FileResolver
     */
    private $fileResolver;

    /**
     * @var string
     */
    private $includeBasePath;

    const EXTENSIONS = [
        "html",
        "htm"
    ];


    /**
     * FileResolverMustacheLoader constructor.
     *
     * @param FileResolver $fileResolver
     * @param string $includeBasePath
     */
    public function __construct($fileResolver, $includeBasePath) {
        $this->fileResolver = $fileResolver;
        $this->includeBasePath = $includeBasePath;
    }

    /**
     * Load a template by name
     *
     * @param string $name
     * @return Mustache_Source|string|void
     */
    public function load($name) {

        $fullyQualified = rtrim($this->includeBasePath, " /") . "/" . $name;

        foreach (self::EXTENSIONS as $extension) {
            $resolvedFile = $this->fileResolver->resolveFile($fullyQualified . "." . $extension);
            if ($resolvedFile)
                return file_get_contents($resolvedFile);
        }

        return "";
    }
}