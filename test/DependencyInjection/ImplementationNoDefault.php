<?php


namespace Kinikit\Core\DependencyInjection;

/**
 *
 * @noProxy
 * @package Kinikit\Core\DependencyInjection
 */
class ImplementationNoDefault implements InterfaceNoDefault {

    /**
     * Do something method
     *
     * @return mixed
     */
    public function doSomething() {
        echo "BING";
    }
}
