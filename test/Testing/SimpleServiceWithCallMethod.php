<?php


namespace Kinikit\Core\Testing;


use Kinikit\Core\DependencyInjection\SimpleService;

class SimpleServiceWithCallMethod extends SimpleService {

    // Call method
    public function __call($methodName, $arguments) {

    }

}