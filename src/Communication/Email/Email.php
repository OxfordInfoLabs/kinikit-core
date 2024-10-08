<?php

namespace Kinikit\Core\Communication\Email;


use Kinikit\Core\Communication\Email\Attachment\EmailAttachment;
use Kinikit\Core\DependencyInjection\Container;
use Kinikit\Core\Security\Hash\HashProvider;
use Kinikit\Core\Security\Hash\SHA512HashProvider;

/**
 * Class Email
 */
class Email {

    /**
     * From field
     *
     * @var string
     * @required
     *
     */
    protected $from;


    /**
     * To field
     *
     * @var string[]
     * @required
     */
    protected $recipients;


    /**
     * Subject field
     *
     * @var string
     * @required
     */
    protected $subject;


    /**
     * The main text body for this email.
     *
     * @var string
     * @required
     */
    protected $textBody;

    /**
     * Optional CC field
     *
     * @var string[]
     */
    protected $cc;

    /**
     * Optional BCC field
     *
     * @var string[]
     */
    protected $bcc;


    /**
     * Optional reply to
     *
     * @var string
     */
    protected $replyTo;


    /**
     * Array of attachment summary objects summarising any attachments for this email
     *
     * @var EmailAttachment[]
     */
    protected $attachments;

    /**
     * Email constructor.
     * @param string $from
     * @param string[] $recipients
     * @param string $subject
     * @param string $textBody
     * @param string[] $cc
     * @param string[] $bcc
     * @param string $replyTo
     * @param EmailAttachment[] $attachments
     */
    public function __construct($from, $recipients, $subject, $textBody, $cc = null, $bcc = null, $replyTo = null, $attachments = []) {
        $this->from = $from;
        $this->recipients = $recipients;
        $this->subject = $subject;
        $this->textBody = $textBody;
        $this->cc = $cc;
        $this->bcc = $bcc;
        $this->replyTo = $replyTo;
        $this->attachments = $attachments;
    }


    /**
     * @return string
     */
    public function getFrom() {
        return $this->from;
    }


    /**
     * @return array
     */
    public function getRecipients() {
        return $this->recipients;
    }


    /**
     * @return string
     */
    public function getSubject() {
        return $this->subject;
    }


    /**
     * @return string
     */
    public function getTextBody() {
        return $this->textBody;
    }


    /**
     * @return array
     */
    public function getCc() {
        return $this->cc;
    }


    /**
     * @return array
     */
    public function getBcc() {
        return $this->bcc;
    }


    /**
     * @return string
     */
    public function getReplyTo() {
        return $this->replyTo;
    }


    /**
     * @return EmailAttachment[]
     */
    public function getAttachments() {
        return $this->attachments;
    }

    /**
     * @param EmailAttachment[] $attachments
     */
    public function setAttachments($attachments) {
        $this->attachments = $attachments;
    }


    /**
     * Get a hash for this email - useful for detecting whether an email has been sent before
     * The default implementation for this is to combine the subject, recipients and text body
     * and hash using built in hash implementation
     */
    public function getHash() {
        /**
         * @var HashProvider $hasher
         */
        $hasher = Container::instance()->get(SHA512HashProvider::class);

        $joinedRecipients = $this->getRecipients() ? implode(",", $this->getRecipients()) : "";
        
        // Return hash value based upon recipients, subject and text body
        return $hasher->generateHash($joinedRecipients . $this->getSubject() . $this->getTextBody());
    }

}
