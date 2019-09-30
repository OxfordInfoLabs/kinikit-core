<?php


namespace Kinikit\Core;


interface ApplicationBootstrap {

    /**
     * Set up logic, run on each request, first before any request processing.
     *
     */
    public function setup();


}
