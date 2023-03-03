<?php

namespace Kinikit\Core\Template;

use Kinikit\Core\Binding\ObjectBinder;
use Kinikit\Core\Configuration\FileResolver;
use Kinikit\Core\Logging\Logger;
use Kinikit\Core\Template\Mustache\FileResolverMustacheLoader;
use Mustache_Engine;

/**
 * @noProxy
 *
 * Mustache View Parser.
 */
class MustacheTemplateParser implements TemplateParser {

    private $objectBinder;
    private $phpTemplateParser;
    private $fileResolver;

    /**
     * Construct with Object Binder
     *
     * MustacheTemplateParser constructor.
     *
     * @param ObjectBinder $objectBinder
     * @param PHPTemplateParser $phpTemplateParser
     * @param FileResolver $fileResolver
     */
    public function __construct($objectBinder, $phpTemplateParser, $fileResolver) {
        $this->objectBinder = $objectBinder;
        $this->phpTemplateParser = $phpTemplateParser;
        $this->fileResolver = $fileResolver;
    }

    /**
     * Evaluate the template text as mustache.
     *
     * @param $templateText
     * @param $model
     */
    public function parseTemplateText($templateText, &$model, $includeBasePath = null) {

        // Do a PHP parse initially
        $templateText = $this->phpTemplateParser->parseTemplateText($templateText, $model) ?? "";

        $config = [
            "escape" => function ($value) {
                return $value;
            }];

        // if an include base path supplied use this, otherwise use the file resolver mustache loader
        if ($includeBasePath) {
            $config["partials_loader"] = new FileResolverMustacheLoader($this->fileResolver, $includeBasePath);
        } else {
            $config["partials"] = $model;
        }

        $mustacheEngine = new Mustache_Engine($config);

        $newModel = $this->objectBinder->bindToArray($model);

        return $mustacheEngine->render($templateText, $newModel);

    }
}
