<?php


namespace Kinikit\Core\Communication\Email\Attachment;


class StringEmailAttachment implements EmailAttachment {


    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $attachmentFilename;

    /**
     * @var string
     */
    private $contentType;

    /**
     * Construct with a filename for the source file and an optional attachment filename
     * if a different filename is desired for the attachment.
     *
     * FileEmailAttachment constructor.
     */
    public function __construct($content, $attachmentFilename, $contentType = "text/plain") {
        $this->content = $content;
        $this->attachmentFilename = $attachmentFilename;
        $this->contentType = $contentType;
    }

    /**
     * Get the attachment filename
     *
     * @return string
     */
    public function getAttachmentFilename() {
        return $this->attachmentFilename;
    }

    /**
     * Return the content mime type.
     *
     * @return string
     */
    public function getContentMimeType() {
        return $this->contentType;
    }

    /**
     * Return the content itself as a string
     *
     * @return mixed
     */
    public function getContent() {
        return $this->content;
    }
}
