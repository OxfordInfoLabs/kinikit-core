<?php

namespace Kinikit\Core\Template;

use Kinikit\Core\Template\ValueFunction\ValueFunctionEvaluator;

/**
 * @noProxy
 */
class KinibindTemplateParser implements TemplateParser {

    /**
     * @var string
     */
    private string $prefix;

    /**
     * @var string[]
     */
    private array $enclosures;

    /**
     * @var ValueFunctionEvaluator
     */
    private ValueFunctionEvaluator $valueFunctionEvaluator;

    /**
     * @param string $prefix
     * @param string[] $enclosures
     */
    public function __construct(string $prefix = "k", array $enclosures = ["{{", "}}"]) {
        $this->prefix = $prefix;
        $this->enclosures = $enclosures;
        $this->valueFunctionEvaluator = new ValueFunctionEvaluator();
    }

    public function parseTemplateText($templateText, &$model = [], $includeBasePath = null) {

        $wrapped = false;

        if (!is_numeric(strpos($templateText, "<html"))) {
            $templateText = '<html><body><div id="KINIBIND">' . $templateText . "</div></body></html>";
            $wrapped = true;
        }

        libxml_use_internal_errors(true);
        $domDoc = new \DOMDocument();
        $domDoc->loadHTML($templateText);


        // Iterate through child nodes and process them
        for ($i = 0; $i < $domDoc->getElementsByTagName("html")->item(0)->childNodes->length; $i++) {
            $this->processHTMLFragment($domDoc->getElementsByTagName("html")->item(0)->childNodes->item($i), $model);
        }


        if ($wrapped) {
            $element = $domDoc->getElementById("KINIBIND");
            $output = "";
            $children = $element->childNodes;
            foreach ($children as $child) {
                $output .= $element->ownerDocument->saveHTML($child);
            }
        } else {
            $output = $domDoc->saveHTML();
        }

        // Strip any <block> tags
        return preg_replace("/<\/?block\s*>/", "", $output);

    }

    /**
     * Process a DOMDocument html fragment
     *
     * @param $fragment
     * @param $model
     * @return void
     */
    private function processHTMLFragment($fragment, &$model): void {

        if ($fragment instanceof \DOMText) {
            $this->parseTextContent($fragment, $model);
            return;
        }

        $processSubFragments = true;

        if ($fragment->hasAttributes()) {
            for ($i = 0; $i < $fragment->attributes->length; $i++) {
                /**
                 * @var \DOMAttr $attr
                 */
                $attr = $fragment->attributes->item($i);

                // Deal with kinibind attributes
                switch ($attr->name) {
                    case $this->prefix . "-if":
                        // Remove fragment if 'if' statement is false
                        $ifStatement = $attr->value;
                        $evaluated = $this->parseFormatters($ifStatement, $model);
                        if ($evaluated) {
                            $fragment->removeAttributeNode($attr);
                        } else {
                            $fragment->parentNode->removeChild($fragment);
                            return;
                        }
                        break;

                    case $this->prefix . "-text":
                        // Inserts text into the tag content
                        $text = $model[$attr->value] ?? "";
                        $newNode = new \DOMText($text);
                        $fragment->appendChild($newNode);
                        $fragment->removeAttributeNode($attr);
                        break;

                    case $this->prefix . "-html":
                        // Inserts html into the tag content
                        $html = $model[$attr->value] ?? "";
                        $newFragment = $fragment->ownerDocument->createDocumentFragment();
                        $newFragment->appendXML($html);
                        // Remove inner HTML and replace with new
                        while ($fragment->hasChildNodes()) {
                            $fragment->removeChild($fragment->firstChild);
                        }
                        $fragment->appendChild($newFragment);
                        $fragment->removeAttributeNode($attr);
                        break;

                    default:
                        // Duplicates a html fragment as per the 'each' statement
                        if (str_starts_with($attr->name, $this->prefix . "-each-")) {
                            $this->processEachStatement($fragment, $attr, $model);
                            $processSubFragments = false;
                            break;
                        }
                        // Sets a variable which can be used within the block
                        if (str_starts_with($attr->name, $this->prefix . "-set-")) {
                            $variable = substr($attr->name, 6);
                            $model[$variable] = $this->parseFormatters($attr->value, $model);
                            $fragment->removeAttributeNode($attr);
                            break;
                        }
                        // Conditionally sets formatting classes
                        if (str_starts_with($attr->name, $this->prefix . "-class-")) {
                            $className = substr($attr->name, 8);
                            $classAttr = $fragment->getAttribute("class");
                            if ($this->parseFormatters($attr->value, $model)) {
                                if ($classAttr) {
                                    if (strpos($classAttr, $className) < 0) {
                                        $fragment->setAttribute("class", "$classAttr $className");
                                    }
                                } else {
                                    $fragment->setAttribute("class", $className);
                                }
                            } else {
                                $newClasses = str_replace($className, "", $classAttr);
                                $fragment->setAttribute("class", trim($newClasses));
                            }
                            $fragment->removeAttributeNode($attr);
                            break;
                        }
                        // For eg. k-fruit='pineapple' sets attribute fruit='pineapple' - good for variables
                        if (str_starts_with($attr->name, $this->prefix . "-")) {
                            $attrName = substr($attr->name, 2);
                            $newAttrValue = $this->parseFormatters($attr->value, $model);
                            $fragment->setAttribute($attrName, $newAttrValue);
                            $fragment->removeAttributeNode($attr);
                            break;
                        }

                }
            }
        }

        // Process the subfragments - turned off after an 'each'
        if ($processSubFragments) {
            for ($i = 0;
                 $i < $fragment->childNodes->length;
                 $i++) {
                $this->processHTMLFragment($fragment->childNodes->item($i), $model);
            }
        }

    }

    /**
     * Process an 'each' statement
     *
     * @param \DOMElement $fragment
     * @param \DOMAttr $attr
     * @param array $model
     * @return void
     */
    private function processEachStatement($fragment, $attr, &$model): void {

        // Set the loop variable and what is being looped
        $variable = substr($attr->name, 7);
        $values = $this->parseFormatters($attr->value, $model);
        $fragment->removeAttributeNode($attr);

        // Update the model with the current item and process each new piece of content
        foreach ($values as $index => $value) {
            $model[$variable] = $value;
            $model['$index'] = $index;
            $newNode = $fragment->cloneNode(true);
            $this->processHTMLFragment($newNode, $model);
            $fragment->parentNode->insertBefore($newNode, $fragment);
        }

        unset($model[$variable]);
        unset($model['$index']);
        $fragment->parentNode->removeChild($fragment);

    }

    private function parseFormatters($text, &$model) {
        return $this->valueFunctionEvaluator->evaluateString("[[$text]]", $model);
    }

    /**
     * Process text content
     *
     * @param \DOMText $textNode
     * @param array $model
     * @return void
     */
    private function parseTextContent($textNode, $model): void {
        $text = $textNode->textContent;
        $text = $this->valueFunctionEvaluator->evaluateString($text, $model, $this->enclosures) ?? "";
        $newTextNode = new \DOMText($text);
        $textNode->parentNode->replaceChild($newTextNode, $textNode);
    }

}