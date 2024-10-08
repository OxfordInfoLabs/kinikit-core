<?php

namespace Kinikit\Core\Template\ValueFunction;

use UnexpectedValueException;

/**
 * @noProxy
 */
class ValueFunctionEvaluator {

    /**
     * @var ValueFunction[]
     */
    private $functions;


    /**
     * Construct, install standard functions
     *
     * FieldValueFunctionEvaluator constructor.
     */
    public function __construct() {

        // Add built in evaluators
        $this->functions = [
            new RegExValueFunction(),
            new DateFormatValueFunction(),
            new LogicValueFunction(),
            new ConversionValueFunction(),
            new ArrayValueFunction(),
            new StringValueFunction(),
            new ObjectValueFunction(),
            new MathsValueFunction(),
            new SharedValueFunction()
        ];
    }


    /**
     * Add a new function for field value evaluation
     *
     * @param $function
     */
    public function addValueFunction($function) {
        $this->functions[] = $function;
    }

    /**
     * Evaluate a string for field value functions where parameterised values
     * are expected to be supplied surrounded by delimiters and fulfilled using the
     * data array
     *
     * @param $string
     * @param string[] $delimiters
     * @param array $model
     */
    public function evaluateString($string, $model = [], $delimiters = ["[[", "]]"]) {
        $evaluated = preg_replace_callback("/" . preg_quote($delimiters[0]) . "(.*?)" . preg_quote($delimiters[1]) . "/", function ($matches) use ($model, $delimiters) {
            $exploded = explode(" | ", $matches[1]);

            $expression = trim($exploded[0]);
            // Handle special built in expressions
            $specialExpression = $this->evaluateSpecialExpressions($expression);
            if ($specialExpression == $expression) {

                if (is_numeric($expression)) {
                    $value  = $expression;
                } elseif (trim($expression, "'\"") != $expression) {
                    $value = trim($expression, "'\"");
                } else {
                    // assume field expression
                    $value = $this->expandMemberExpression($expression, $model);
                }

            } else {

                // Set as special expression
                $value = $specialExpression;
            }

            if (sizeof($exploded) > 1) {
                for ($i = 1; $i < sizeof($exploded); $i++) {
                    $value = $this->evaluateValueFunction(trim($exploded[$i]), $value, $model);
                }
            }

            if (!is_scalar($value)) {
                $value = "OBJECT||" . json_encode($value);
            }

            return match($value) {
                true => "true",
                false => "false",
                default => $value
            };

        }, $string ?? "");

        // Decode if applicable
        if (str_starts_with($evaluated, "OBJECT||")) {
            $nObjects = preg_match_all("/OBJECT\|\|/", $evaluated);
            if ($nObjects > 1){
                throw new UnexpectedValueException("Cannot have multiple expressions containing objects!");
            }
            $evaluated = json_decode(substr($evaluated, 8), true);
        }

        return match($evaluated) {
            "" => null,
            "true" => true,
            "false" => false,
            default => $evaluated
        };
    }


    /**
     * Evaluate value function based upon first matching function
     *
     * @param $functionString
     * @param $fieldValue
     */
    public function evaluateValueFunction($functionString, $fieldValue, $model) {
        foreach ($this->functions as $function) {
            if ($function->doesFunctionApply($functionString)) {
                return $function->applyFunction($functionString, $fieldValue, $model);
            }
        }
        return $fieldValue;
    }


    public function evaluateSpecialExpressions($expression) {

        if ($expression == "NOW") {
            $expression = date("Y-m-d H:i:s");
        }

        if (is_string($expression)) {

            // Evaluate time offset parameters for days ago and hours ago
            $expression = preg_replace_callback("/([0-9]+)_YEARS_AGO/", function ($matches) use (&$outputParameters) {
                return (new \DateTime())->sub(new \DateInterval("P" . $matches[1] . "Y"))->format("Y-m-d H:i:s");
            }, $expression);

            $expression = preg_replace_callback("/([0-9]+)_MONTHS_AGO/", function ($matches) use (&$outputParameters) {
                return (new \DateTime())->sub(new \DateInterval("P" . $matches[1] . "M"))->format("Y-m-d H:i:s");
            }, $expression);

            $expression = preg_replace_callback("/([0-9]+)_DAYS_AGO/", function ($matches) use (&$outputParameters) {
                return (new \DateTime())->sub(new \DateInterval("P" . $matches[1] . "D"))->format("Y-m-d H:i:s");
            }, $expression);

            $expression = preg_replace_callback("/([0-9]+)_HOURS_AGO/", function ($matches) use (&$outputParameters) {
                return (new \DateTime())->sub(new \DateInterval("PT" . $matches[1] . "H"))->format("Y-m-d H:i:s");
            }, $expression);

            $expression = preg_replace_callback("/([0-9]+)_MINUTES_AGO/", function ($matches) use (&$outputParameters) {
                return (new \DateTime())->sub(new \DateInterval("PT" . $matches[1] . "M"))->format("Y-m-d H:i:s");
            }, $expression);

            $expression = preg_replace_callback("/([0-9]+)_SECONDS_AGO/", function ($matches) use (&$outputParameters) {
                return (new \DateTime())->sub(new \DateInterval("PT" . $matches[1] . "S"))->format("Y-m-d H:i:s");
            }, $expression);

        }

        if ($expression == "true") {
            return true;
        }

        if ($expression == "false") {
            return false;
        }

        return $expression;
    }


    // Expand member expression
    private function expandMemberExpression($expression, $model) {

        $explodedExpression = explode(".", $expression);
        foreach ($explodedExpression as $expression) {
            $model = $model[$expression] ?? null;
        }
        return $model;
    }


}
