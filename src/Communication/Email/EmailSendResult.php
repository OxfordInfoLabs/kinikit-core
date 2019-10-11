<?php


namespace Kinikit\Core\Communication\Email;


/**
 *
 * Class EmailSendResult
 */
class EmailSendResult {

    private $status;
    private $errorMessage;


    const STATUS_SENT = "SENT";
    const STATUS_FAILED = "FAILED";

    /**
     * Email send result
     *
     * @param string $status
     * @param string $errorMessage
     */
    public function __construct($status, $errorMessage = null) {
        $this->status = $status;
        $this->errorMessage = $errorMessage;
    }


    /**
     * @return mixed
     */
    public function getStatus() {
        return $this->status;
    }


    /**
     * @return mixed
     */
    public function getErrorMessage() {
        return $this->errorMessage;
    }


}
