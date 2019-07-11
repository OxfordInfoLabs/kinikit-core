<?php
/**
 * Created by PhpStorm.
 * User: markrobertshaw
 * Date: 21/09/2018
 * Time: 15:34
 */

namespace Kinikit\Core\Template\Parser;

use Kinikit\Core\Template\TemplateParser;
use Kinikit\Core\Util\ObjectArrayUtils;


class TwigTemplateParser extends TemplateParser {

    /**
     * Evaluate the view as mustache.
     *
     * @param $viewText
     * @param $model
     */
    public function parseTemplateText($viewText, &$model) {

        $twigLoader = new \Twig_Loader_Array(array("template" => $viewText));

        $twig = new \Twig_Environment($twigLoader);

        $newModel = ObjectArrayUtils::convertSerialisableObjectsToAssociativeArrays($model);

        return $twig->render("template", $newModel);

    }
}
