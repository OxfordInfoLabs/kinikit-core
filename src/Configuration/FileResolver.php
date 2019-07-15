<?php


namespace Kinikit\Core\Configuration;

/**
 * File resolver.  Designed as an injected entity for resolving files using a array of configured search paths.
 *
 * @noProxy
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
    public function resolveFile(string $relativeFilePath, $caseInsensitive = false) {


        // Slower more careful algorithm for case insensitive.
        if ($caseInsensitive) {


            foreach ($this->searchPaths as $searchPath) {

                $path = explode("/", $relativeFilePath);

                // Create search and built paths and look for items
                $searchDir = $searchPath;
                $builtPath = $searchPath;
                $fullPath = true;
                foreach ($path as $pathElement) {

                    $iterator = new \DirectoryIterator($searchDir);
                    $elementMatch = false;
                    foreach ($iterator as $item) {
                        if ($item->isDot()) continue;
                        if (strtolower($item->getFilename()) == strtolower($pathElement)) {
                            $builtPath .= "/" . $item->getFilename();
                            $elementMatch = true;
                            break;
                        }
                    }

                    // Break if no element match
                    if (!$elementMatch) {
                        $fullPath = false;
                        break;
                    }

                    $searchDir.= "/$pathElement";

                }

                if ($fullPath) {
                    return $builtPath;
                }

            }


        } // Much faster algorithm where we know the file is case sensitive.
        else {

            // Check for direct matches first for maximum performance.
            foreach ($this->searchPaths as $searchPath) {
                $targetFilename = $searchPath . "/" . ltrim($relativeFilePath, "/");
                if (file_exists($targetFilename)) {
                    return $targetFilename;
                }
            }
        }


        // Return null if unresolvable.
        return null;
    }


}
