<?php


namespace Kinikit\Core\Asynchronous;


/**
 * Handy implementation of the Asynchronous class which accepts an anonymous function on construction which
 * is run instead of a run implementation.
 *
 * The return value from the function will be assigned to the returnValue member on completion to facilitate
 * data transfer back to the parent.
 *
 * Class AsynchronousFunction
 * @package Kinikit\Core\Asynchronous
 */
class AsynchronousFunction extends Asynchronous {


    /**
     * @var \Closure
     */
    private $function;

    /**
     * AsynchronousFunction constructor.
     *
     * @param \Closure $function
     */
    public function __construct($function) {
        $this->function = $function;
    }


    /**
     * Call the configured function
     *
     * @return mixed|void
     */
    public function run() {
        return $this->function->call($this);
    }
}