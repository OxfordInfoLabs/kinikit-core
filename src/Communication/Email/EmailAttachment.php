<?php


namespace Kinikit\Core\Communication\Email;

/**
 * Email attachment interface - used for both string and file attachments here.
 *
 * @package Kinikit\Core\Communication\Email
 */
interface EmailAttachment {

    /**
     * Get the attachment filename
     *
     * @return string
     */
    public function getAttachmentFilename();


    /**
     * Return the content mime type.
     *
     * @return string
     */
    public function getContentMimeType();


    /**
     * Return the content itself as a string
     *
     * @return mixed
     */
    public function getContent();

}
