<?php


namespace Kinikit\Core\Asynchronous;

/**
 * Base Asynchronous class - sub classes need to implement
 * the run method to do their logic.  All sub class data will be serialised
 * into a temporary file and then restored onto the parent instance after execution
 * completes for transparent usage.
 *
 * @package Kinikit\Core\Asynchronous
 */
abstract class Asynchronous {

    /**
     * Status of this asynchronous task
     *
     * @var string
     */
    protected $status = self::STATUS_PENDING;


    /**
     * The return value from the run function if successful
     *
     * @var mixed
     */
    protected $returnValue;


    /**
     * The exception instance if the run function failed
     *
     * @var \Exception
     */
    protected $exception;


    // Status constants
    const STATUS_PENDING = "PENDING";
    const STATUS_RUNNING = "RUNNING";
    const STATUS_COMPLETED = "COMPLETED";
    const STATUS_FAILED = "FAILED";

    /**
     * Get the status for this job
     *
     * @return string
     */
    public function getStatus() {
        return $this->status;
    }


    /**
     * Get the return value
     *
     * @return mixed
     */
    public function getReturnValue() {
        return $this->returnValue;
    }

    /**
     * @return \Exception
     */
    public function getException() {
        return $this->exception;
    }


    /**
     * Run method, should be implemented to execute this asynchronous task
     *
     * @return mixed
     * @throws \Exception
     */
    public abstract function run();

}