<?php
/**
 * Created by PhpStorm.
 * User: markrobertshaw
 * Date: 21/09/2018
 * Time: 15:34
 */

namespace Kinikit\Core\Template;

use Kinikit\Core\Binding\ObjectBinder;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

/**
 * @noProxy
 *
 * Class TwigTemplateParser
 * @package Kinikit\Core\Template
 */
class TwigTemplateParser implements TemplateParser {

    private $objectBinder;
    private $phpTemplateParser;

    /**
     * Construct with Object Binder
     *
     * TwigTemplateParser constructor.
     *
     * @param ObjectBinder $objectBinder
     * @param PHPTemplateParser $phpTemplateParser
     */
    public function __construct($objectBinder, $phpTemplateParser) {
        $this->objectBinder = $objectBinder;
        $this->phpTemplateParser = $phpTemplateParser;
    }

    /**
     * Evaluate the template as twig.
     *
     * @param $templateText
     * @param $model
     */
    public function parseTemplateText($templateText, &$model, $includePathBase = null) {

        // Do a PHP parse initially
        $templateText = $this->phpTemplateParser->parseTemplateText($templateText, $model);

        $twigLoader = new ArrayLoader(array("template" => $templateText));

        $twig = new Environment($twigLoader);

        $newModel = $this->objectBinder->bindToArray($model);

        return $twig->render("template", $newModel);

    }
}
