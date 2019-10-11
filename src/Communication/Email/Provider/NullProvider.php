<?php


namespace Kinikit\Core\Communication\Email\Provider;


use Kinikit\Core\Communication\Email\Email;
use Kinikit\Core\Communication\Email\EmailSendResult;

/**
 * Null provider which does nothing - default for testing and development.
 *
 */
class NullProvider implements EmailProvider {

    /**
     * Send an email.
     *
     * @param Email $email
     *
     * @return EmailSendResult
     *
     */
    public function send($email) {
        return new EmailSendResult(EmailSendResult::STATUS_SENT);
    }
}
