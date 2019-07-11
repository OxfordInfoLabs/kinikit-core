<?php


namespace Kinikit\Core\DependencyInjection;

/**
 * @implementationConfigParam interface.class
 * @implementation first Kinikit\Core\DependencyInjection\ImplementationMapping1
 * @implementation second Kinikit\Core\DependencyInjection\ImplementationMapping2
 *
 * @defaultImplementation Kinikit\Core\DependencyInjection\ImplementationMapping1
 *
 * @package Kinikit\Core\DependencyInjection
 */
interface InterfaceWithMappings {

    /**
     * Do something method
     *
     * @return mixed
     */
    public function doSomething();

}
