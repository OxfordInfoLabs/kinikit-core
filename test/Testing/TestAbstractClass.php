<?php


namespace Kinikit\Core\Testing;


abstract class TestAbstractClass {

    // Abstract method
    public abstract function abstractMethod();

    public function baseMethod() {
        return "Hello world";
    }

}