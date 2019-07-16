<?php

namespace Kinikit\Core\Template;

use Kinikit\Core\Binding\ObjectBinder;
use Mustache_Engine;

/**
 * @noProxy
 *
 * Mustache View Parser.
 */
class MustacheTemplateParser implements TemplateParser {

    private $objectBinder;
    private $phpTemplateParser;

    /**
     * Construct with Object Binder
     *
     * MustacheTemplateParser constructor.
     *
     * @param ObjectBinder $objectBinder
     * @param PHPTemplateParser $phpTemplateParser
     */
    public function __construct($objectBinder, $phpTemplateParser) {
        $this->objectBinder = $objectBinder;
        $this->phpTemplateParser = $phpTemplateParser;
    }

    /**
     * Evaluate the template text as mustache.
     *
     * @param $templateText
     * @param $model
     */
    public function parseTemplateText($templateText, &$model) {

        // Do a PHP parse initially
        $templateText = $this->phpTemplateParser->parseTemplateText($templateText, $model);

        $mustacheEngine = new Mustache_Engine(array("escape" => function ($value) {
            return $value;
        }));


        $newModel = $this->objectBinder->bindToArray($model);

        return $mustacheEngine->render($templateText, $newModel);

    }
}
