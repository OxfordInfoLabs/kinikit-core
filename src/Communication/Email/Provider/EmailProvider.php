<?php


namespace Kinikit\Core\Communication\Email\Provider;


use Kinikit\Core\Communication\Email\Email;
use Kinikit\Core\Communication\Email\EmailSendResult;

/**
 * Base email transport class.  Only one method required to be implemented (send)
 *
 * @implementationConfigParam email.provider
 * @implementation php \Kinikit\Core\Communication\Email\Provider\PHPMailerProvider
 * @defaultImplementation \Kinikit\Core\Communication\Email\Provider\NullProvider
 *
 */
interface EmailProvider {

    /**
     * Send an email.
     *
     * @param Email $email
     * @return EmailSendResult
     *
     */
    public function send($email);


}
