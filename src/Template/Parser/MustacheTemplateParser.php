<?php

namespace Kinikit\Core\Template\Parser;

use Kinikit\Core\Template\TemplateParser;
use Kinikit\Core\Util\SerialisableArrayUtils;
use Mustache_Engine;

/**
 *
 * Mustache View Parser.
 */
class MustacheTemplateParser extends TemplateParser {

    /**
     * Evaluate the view as mustache.
     *
     * @param $viewText
     * @param $model
     */
    public function parseTemplateText($viewText, &$model) {

        $mustacheEngine = new Mustache_Engine(array("escape" => function ($value) {
            return $value;
        }));

        $newModel = SerialisableArrayUtils::convertSerialisableObjectsToAssociativeArrays($model, true);

        return $mustacheEngine->render($viewText, $newModel);

    }
}