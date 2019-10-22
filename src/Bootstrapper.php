<?php


namespace Kinikit\Core;

use Kinikit\Core\Configuration\SearchNamespaces;
use Kinikit\Core\DependencyInjection\Container;



/**
 * Generic initialiser.  This should be called to initialise the framework with default setup stuff.
 * This should be initialised explicitly for e.g. Command Line Applications but is called automatically
 * by the Dispatcher if using MVC framework.
 *
 * Class Init
 * @package Kinikit\Core
 *
 * @noProxy
 */
class Bootstrapper {

    /**
     * @var SearchNamespaces
     */
    private $searchNamespaces;

    /**
     * Init constructor.  Automatically sets things up.
     *
     * @param SearchNamespaces $searchNamespaces
     * @param Init $init
     */
    public function __construct($searchNamespaces, $init) {

        // Process our bootstrap
        $this->searchNamespaces = $searchNamespaces;
        $this->process();
    }


    // Process bootstrap logic.
    private function process() {

        $namespaces = $this->searchNamespaces->getNamespaces();

        // Process search namespaces in reverse order.
        for ($i = sizeof($namespaces) - 1; $i >= 0; $i--) {
            if (class_exists($namespaces[$i] . "\\Bootstrap")) {
                $bootstrap = Container::instance()->get($namespaces[$i] . "\\Bootstrap");
                $bootstrap->setup();
            }
        }

    }


}
