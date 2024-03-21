<?php


namespace Kinikit\Core;

use Kinikit\Core\Configuration\SearchNamespaces;
use Kinikit\Core\DependencyInjection\Container;
use Kinikit\Core\Logging\Logger;


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
        foreach ($namespaces as $namespace) {
            if (class_exists($namespace . "\\Bootstrap")) {
                $bootstrap = Container::instance()->get($namespace . "\\Bootstrap");
                $bootstrap->setup();
            }
        }

    }


}
