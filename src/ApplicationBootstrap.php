<?php


namespace Kinikit\Core;


interface ApplicationBootstrap {

    /**
     * Logic to be run before the main framework init.
     *
     */
    public function preInit();


    /**
     * Logic to be run after the main framework init.
     *
     */
    public function postInit();

}
