<?php


namespace Kinikit\Core\Communication\Email;


class FileEmailAttachment implements EmailAttachment {


    /**
     * @var string
     */
    private $filename;

    /**
     * @var string
     */
    private $attachmentFilename;

    /**
     * Construct with a filename for the source file and an optional attachment filename
     * if a different filename is desired for the attachment.
     *
     * FileEmailAttachment constructor.
     */
    public function __construct($filename, $attachmentFilename = null) {
        $this->filename = $filename;
        $explodedFilename = explode("/", $filename);
        $this->attachmentFilename = $attachmentFilename ?? array_pop($explodedFilename);
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
        return mime_content_type($this->filename);
    }

    /**
     * Return the content itself as a string
     *
     * @return mixed
     */
    public function getContent() {
        return file_get_contents($this->filename);
    }
}
