<?php


namespace Kinikit\Core;


class Bootstrap implements ApplicationBootstrap {


    public $timezone;


    /**
     * Logic to be run before the main framework init.
     *
     */
    public function setup() {
        $this->timezone = date_default_timezone_get();
    }


}
