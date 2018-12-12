<?php

namespace Kinikit\Core\Template;

use Kinikit\Core\Configuration;
use Kinikit\Core\Template\Parser\MustacheTemplateParser;
use Kinikit\Core\Template\Parser\TwigTemplateParser;


/**
 * Interface for view parsers which may be injected into the Model and View class statically for custom view parsing in applications.
 *
 *
 * Interface TemplateParser
 */
abstract class TemplateParser {

    private static $customParsers = array();

    // Parse the view text and return the modified string.  The model is passed as a reference to allow mutability of the model if required.
    public abstract function parseTemplateText($viewText, &$model);


    /**
     * Add a custom view parser.
     *
     * @param string $key
     * @param TemplateParser $viewParser
     */
    public static function addCustomParser($key, $viewParser) {
        self::$customParsers[$key] = $viewParser;
    }

    public static function getConfiguredParser() {
        return self::getParserByKey(Configuration::readParameter("template.parser"));
    }

    /**
     * Get a view parser by key.
     */
    public static function getParserByKey($key) {
        switch (strtolower($key)) {
            case "twig":
                return new TwigTemplateParser();
            default:
                if (isset(self::$customParsers[$key]))
                    return self::$customParsers[$key];
                else
                    return new MustacheTemplateParser();
        }
    }


}