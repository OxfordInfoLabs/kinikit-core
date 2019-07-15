<?php


namespace Kinikit\Core\Configuration;

/**
 * File resolver.  Designed as an injected entity for resolving files using a array of configured search paths.
 *
 * Class FileResolver
 * @package Kinikit\Core\Configuration
 */
class FileResolver {

    // Initialise the search paths with the current directory.
    private $searchPaths = ["."];


    /**
     * Construct - add the configuration search paths if they exist.
     *
     * FileResolver constructor.
     */
    public function __construct() {
        if ($configuredSearchPaths = Configuration::readParameter("search.paths")) {
            $configuredPaths = explode(";", $configuredSearchPaths);
            $this->searchPaths = array_merge($this->searchPaths, $configuredPaths);
        }
    }


    /**
     * Add a search path to the file resolver programmatically .
     *
     * @param $searchPath
     */
    public function addSearchPath($searchPath) {
        $this->searchPaths[] = $searchPath;
    }


    /**
     * @return mixed
     */
    public function getSearchPaths() {
        return $this->searchPaths;
    }

    /**
     * Resolve a relative file to a full path using the attached search paths.
     *
     * @param string $string
     * @return string
     */
    public function resolveFile(string $relativeFilePath) {

        // Loop through each search path looking for a match.
        foreach ($this->searchPaths as $searchPath) {
            $targetFilename = $searchPath . "/" . ltrim($relativeFilePath, "/");
            if (file_exists($targetFilename)) {
                return $targetFilename;
            }
        }

        // Return null if unresolvable.
        return null;
    }


}
