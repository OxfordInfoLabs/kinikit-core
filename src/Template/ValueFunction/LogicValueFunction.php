<?php

namespace Kinikit\Core\Template\ValueFunction;

class LogicValueFunction extends ValueFunctionWithArguments {

    const supportedFunctions = [
        "not",
        "ifNot",
        "ternary",
        "equals",
        "notequals",
        "gt",
        "gte",
        "lt",
        "lte",
        "ensureNumeric",
        "between",
        "and",
        "or",
        "andNot",
        "orNot",
        "case"
    ];


    /**
     * Get supported function names
     *
     * @return string[]|void
     */
    protected function getSupportedFunctionNames() {
        return self::supportedFunctions;
    }

    /**
     * Apply a function with arguments
     *
     * @param $functionName
     * @param $functionArgs
     * @param $value
     * @param $model
     *
     * @return mixed|void
     */
    protected function applyFunctionWithArgs($functionName, $functionArgs, $value, $model) {

        switch ($functionName) {

            case "not":
                return $value ? false : true;

            case "ifNot":
                if (!$value) {
                    return $functionArgs[0] ?? "";
                }
                break;

            case "ternary":
                return $value ? $functionArgs[0] : $functionArgs[1];

            case "equals":
                return ($value == $functionArgs[0]);

            case "notequals":
                return ($value != $functionArgs[0]);

            case "gt":
                return ($value > $functionArgs[0]);

            case "gte":
                return ($value >= $functionArgs[0]);

            case "lt":
                return ($value < $functionArgs[0]);

            case "lte":
                return ($value <= $functionArgs[0]);

            case "ensureNumeric":
                return is_numeric($value) ? $value : 0;

            case "between":
                return ($value >= $functionArgs[0]) && ($value <= $functionArgs[1]);

            case "and":
                return $value && $functionArgs[0];

            case "or":
                return $value || $functionArgs[0];

            case "andNot":
                return $value && !$functionArgs[0];

            case "orNot":
                return $value || !$functionArgs[0];

            case "case":
                for ($i = 0; $i < sizeof($functionArgs); $i += 2) {
                    if ($i + 1 == sizeof($functionArgs)) {
                        return $functionArgs[$i];
                    }

                    if ($value == $functionArgs[$i]) {
                        return $functionArgs[$i + 1];
                    }
                }
        }

        return $value;

    }

    // Maths gmp functions
    private function calculate($operator, $arg1, $arg2) {
        switch ($operator) {
            case "add":

        }
    }

}
