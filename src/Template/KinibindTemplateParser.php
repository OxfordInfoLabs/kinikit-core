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
    private $prefix;

    /**
     * @var string[]
     */
    private $enclosures;

    /**
     * @var ValueFunctionEvaluator
     */
    private $valueFunctionEvaluator;

    public function __construct($prefix = "k", $enclosures = ["{{", "}}"]) {
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
        $output = preg_replace("/<\/?block\s*>/", "", $output);

        return $output;

    }

    /**
     * @param \DOMElement $fragment
     * @return void
     */
    private function processHTMLFragment($fragment, &$model) {

        if (is_a($fragment, 'DOMText')) {
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

                switch ($attr->name) {
                    case $this->prefix . "-if":
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
                        $text = $model[$attr->value] ?? "";
                        $newNode = new \DOMText($text);
                        $fragment->appendChild($newNode);
                        $fragment->removeAttributeNode($attr);
                        break;

                    case $this->prefix . "-html":
                        $html = $model[$attr->value] ?? "";
                        $newFragment = $fragment->ownerDocument->createDocumentFragment();
                        $newFragment->appendXML($html);
                        while ($fragment->hasChildNodes()) {
                            $fragment->removeChild($fragment->firstChild);
                        }
                        $fragment->appendChild($newFragment);
                        $fragment->removeAttributeNode($attr);
                        break;

                    default:
                        if (strpos($attr->name, $this->prefix . "-each-") === 0) {
                            $this->processEachStatement($fragment, $attr, $model);
                            $processSubFragments = false;
                            break;
                        }
                        if (strpos($attr->name, $this->prefix . "-set-") === 0) {
                            $variable = substr($attr->name, 6);
                            $model[$variable] = $this->parseFormatters($attr->value, $model);
                            $fragment->removeAttributeNode($attr);
                            break;
                        }
                        if (strpos($attr->name, $this->prefix . "-class-") === 0) {
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
                        if (strpos($attr->name, $this->prefix . "-") === 0) {
                            $attrName = substr($attr->name, 2);
                            $newAttrValue = $this->parseFormatters($attr->value, $model);
                            $fragment->setAttribute($attrName, $newAttrValue);
                            $fragment->removeAttributeNode($attr);
                            break;
                        }

                }
            }
        }

        if ($processSubFragments) {
            for ($i = 0;
                 $i < $fragment->childNodes->length;
                 $i++) {
                $this->processHTMLFragment($fragment->childNodes->item($i), $model);
            }
        }

    }

    /**
     * @param \DOMElement $fragment
     * @param \DOMAttr $attr
     * @param array $model
     * @return void
     */
    private function processEachStatement($fragment, $attr, &$model) {
        $variable = substr($attr->name, 7);;
        $values = $this->parseFormatters($attr->value, $model);
        $fragment->removeAttributeNode($attr);

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
        $evaluatedString = $this->valueFunctionEvaluator->evaluateString("[[$text]]", $model);
        return $evaluatedString;
    }

    /**
     * @param \DOMText $textNode
     * @param array $model
     * @return void
     */
    private
    function parseTextContent($textNode, $model) {
        $text = $textNode->textContent;
        $text = $this->valueFunctionEvaluator->evaluateString($text, $model, $this->enclosures) ?? "";
        $newTextNode = new \DOMText($text);
        $textNode->parentNode->replaceChild($newTextNode, $textNode);
    }

}