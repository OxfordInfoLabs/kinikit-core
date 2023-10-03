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
     * The core data for the exception if one was raised
     *
     * @var array
     */
    protected $exceptionData;


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
     * @return array
     */
    public function getExceptionData() {
        return $this->exceptionData;
    }

    /**
     * @param string $status
     */
    public function setStatus($status) {
        $this->status = $status;
    }

    /**
     * @param mixed $returnValue
     */
    public function setReturnValue($returnValue) {
        $this->returnValue = $returnValue;
    }

    /**
     * @param array $exceptionData
     */
    public function setExceptionData($exceptionData) {
        $this->exceptionData = $exceptionData;
    }


    /**
     * Run method, should be implemented to execute this asynchronous task
     *
     * @return mixed
     * @throws \Exception
     */
    public abstract function run();

}