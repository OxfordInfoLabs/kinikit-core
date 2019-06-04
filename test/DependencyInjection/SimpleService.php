<?php


namespace Kinikit\Core\DependencyInjection;

/**
 * Class SimpleService
 * @package Kinikit\Core\DependencyInjection
 */
class SimpleService {


    /**
     * Get the name
     *
     * @return string
     */
    public function getName() {
        return "Hello wonderful world of fun";
    }


    /**
     * Echo the params
     *
     * @param $a
     * @param $b
     * @param $c
     * @param $d
     */
    public function echoParams($a, $b, $c, $d) {
        return array($a, $b, $c, $d);
    }

}
