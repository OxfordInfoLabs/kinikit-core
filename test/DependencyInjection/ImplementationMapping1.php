<?php


namespace Kinikit\Core\DependencyInjection;

/**
 * @noProxy
 *
 * Class ImplementationMapping1
 * @package Kinikit\Core\DependencyInjection
 */
class ImplementationMapping1 implements InterfaceWithMappings {

    /**
     * Do something method
     *
     * @return mixed
     */
    public function doSomething() {
       return "BOO";
    }
}
