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
     * @var mixed
     */
    private $thisObject;

    /**
     * AsynchronousFunction constructor.
     *
     * @param \Closure $function
     */
    public function __construct($function, $thisObject = null) {
        $this->function = $function;
        $this->thisObject = $thisObject ?? $this;
    }


    /**
     * Call the configured function
     *
     * @return mixed|void
     */
    public function run() {

        $function = $this->function;
        $thisObject = $this->thisObject;

        $this->function = null;
        $this->thisObject = null;

        return $function->call($thisObject);
    }
}