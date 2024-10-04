<?php


namespace Kinikit\Core\Configuration;

use Kinikit\Core\Reflection\ClassInspectorProvider;

/**
 * A global container object containing any detected search namespaces.
 * This processes the search paths in the file resolver to identify the base namespace
 * for included libraries.
 *
 * @noProxy
 * @package Kinikit\Core\Configuration
 *
 */
class SearchNamespaces {

    /**
     * @var FileResolver
     */
    private $fileResolver;

    /**
     * @var ClassInspectorProvider
     */
    private $classInspectorProvider;

    /**
     * @var string[]
     */
    private $namespaces;


    /**
     * SearchNamespaces constructor.
     *
     * @param FileResolver $fileResolver
     * @param ClassInspectorProvider $classInspectorProvider
     */
    public function __construct($fileResolver, $classInspectorProvider) {
        $this->fileResolver = $fileResolver;
        $this->classInspectorProvider = $classInspectorProvider;
    }


    /**
     * Return namespaces
     *
     * @return string[]
     */
    public function getNamespaces() {

        // Gather search namespaces
        if (!$this->namespaces) {
            $this->namespaces = [];
            foreach ($this->fileResolver->getSearchPaths() as $searchPath) {
                if ($searchPath == ".")
                    continue;

                // Look for a bootstrap file to detect search namespace.
                if (file_exists($searchPath . "/Bootstrap.php")) {
                    $this->namespaces[] = $this->classInspectorProvider->getClassInspector($searchPath . "/Bootstrap.php")->getNamespace();
                }

            }
            $this->namespaces[] = Configuration::readParameter("application.namespace");
        }

        return $this->namespaces;
    }

}
