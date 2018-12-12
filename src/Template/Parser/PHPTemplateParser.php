<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 14/08/2018
 * Time: 14:57
 */


namespace Kinikit\Core\Template\Parser;
use Kinikit\Core\Template\TemplateParser;

class PHPTemplateParser extends TemplateParser {

    /**
     * Parse the view as PHP.  This also allows for new variables to be defined within a view and as such these will be merged into the model
     * for use in a parent view if required.
     *
     * @param $viewText
     * @param $model
     */
    public function parseTemplateText($viewText, &$model) {

        // Extract all template parameters into scope.
        extract($model);

        // Store defined variables before we evaluate
        $preVariables = get_defined_vars() ? get_defined_vars() : array();
        unset($preVariables["model"]);

        // Now use an object buffer to get the result.
        ob_start();
        include $viewText;
        $result = ob_get_contents();
        ob_end_clean();

        // Add any newly scoped variables
        $postVariables = get_defined_vars() ? get_defined_vars() : array();
        foreach ($postVariables as $key => $value) {
            if ($key == "model") continue;
            if (!isset($preVariables[$key]) || ($value != $preVariables[$key])) {
                $model[$key] = $value;
            }
        }


        return $result;
    }
}