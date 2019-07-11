<?php


namespace Kinikit\Core;


class Bootstrap implements ApplicationBootstrap {


    public $timezoneBefore;
    public $timezoneAfter;


    /**
     * Logic to be run before the main framework init.
     *
     */
    public function preInit() {
        $this->timezoneBefore = date_default_timezone_get();
    }

    /**
     * Logic to be run after the main framework init.
     *
     */
    public function postInit() {
        $this->timezoneAfter = date_default_timezone_get();
    }
}
