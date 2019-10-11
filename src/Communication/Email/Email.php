<?php

namespace Kinikit\Core\Communication\Email;

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
    public function __construct($from, $recipients, $subject, $textBody, $cc = null, $bcc = null, $replyTo = null, array $attachments = []) {
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


}
