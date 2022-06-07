<?php


namespace Kinikit\Core\Communication\Email;


use Kinikit\Core\Configuration\FileResolver;
use Kinikit\Core\DependencyInjection\Container;
use Kinikit\Core\Logging\Logger;
use Kinikit\Core\Template\TemplateParser;
use Kinikit\Core\Validation\ValidationException;
use Kinikit\Core\Validation\Validator;

/**
 * Templated email which parses out data from an html template file found in Config/email-templates using the supplied model.
 * You do not need to supply the html extension for the template name.
 *
 * Class TemplatedEmail
 * @package Kinikit\Core\Communication\Email
 */
class TemplatedEmail extends Email {

    /**
     * Model for this email
     *
     * @var mixed[]
     */
    private $model;

    // Template root
    const TEMPLATE_ROOT = "Config/email-templates";


    /**
     * Constructor - accepts a template name (relative to Config/email-templates in any of the FileResolver paths).
     * The template is in HTML format with a header section with key/value pairs for Subject, From, ReplyTo, etc if required.
     *
     * @param string $templateName
     * @param array $model
     * @param string[] $recipients
     * @param string $from
     * @param string $subject
     * @param string[] $cc
     * @param string[] $bcc
     * @param string $replyTo
     * @param EmailAttachment[] $attachments
     *
     * @throws MissingEmailTemplateException
     * @throws ValidationException
     */
    public function __construct($templateName, $model = [], $recipients = null, $from = null, $subject = null, $cc = null, $bcc = null, $replyTo = null, $attachments = []) {

        $this->model = $model;

        $templateData = $this->parseTemplate($templateName, $model);

        parent::__construct($from ?? $templateData["from"] ?? null,
            $recipients ?? $templateData["to"] ?? null,
            $subject ?? $templateData["subject"] ?? null,
            $templateData["body"] ?? null,
            $cc ?? $templateData["cc"] ?? null,
            $bcc ?? $templateData["bcc"] ?? null,
            $replyTo ?? $templateData["replyto"] ?? null,
            $attachments);


        if ($validationErrors = Container::instance()->get(Validator::class)->validateObject($this))
            throw new ValidationException($validationErrors);
    }


    // Parse out the template, return an array of data
    protected function parseTemplate($templateName, $model) {


        $templateFile = Container::instance()->get(FileResolver::class)->resolveFile(self::TEMPLATE_ROOT . "/$templateName.html");
        if (!$templateFile) {
            throw new MissingEmailTemplateException($templateName);
        }

        // Grab the template file
        $template = file_get_contents($templateFile);

        /**
         * Create a template parser
         *
         * @var TemplateParser $templateParser
         */
        $templateParser = Container::instance()->get(TemplateParser::class);

        $data = [];

        // Check for header fields and parse them out.
        $explodedTemplate = explode("---", $template);
        if (sizeof($explodedTemplate) > 1) {

            // Template is the second half
            $template = trim($explodedTemplate[1]);

            $headerLines = explode("\n", $explodedTemplate[0]);
            foreach ($headerLines as $headerLine) {
                $headerLine = explode(":", $headerLine);
                $key = strtolower($headerLine[0]);
                $value = join(":", array_slice($headerLine, 1));

                // Handle array types
                if ($key == "to" || $key == "cc" || $key == "bcc") {
                    $data[$key] = [];
                    $explodedValue = explode(",", $value);
                    foreach ($explodedValue as $valueComponent) {
                        $data[$key][] = $templateParser->parseTemplateText(trim($valueComponent), $model, self::TEMPLATE_ROOT);
                    }
                } else {
                    $data[$key] = $templateParser->parseTemplateText(trim($value), $model, self::TEMPLATE_ROOT);
                }
            }

        }

        $model = array_merge($data, $model);

        $data["body"] = $templateParser->parseTemplateText($template, $model, self::TEMPLATE_ROOT);

        return $data;
    }

    /**
     * @return mixed[]
     */
    public function getModel() {
        return $this->model;
    }

}
