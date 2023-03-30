<?php

namespace Kinikit\Core\Template\ValueFunction;

class SharedValueFunction extends ValueFunctionWithArguments {

    const supportedFunctions = [
        "contains",
        "length",
        "concat"
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
     * @param $dataItem
     *
     * @return mixed|void
     */
    protected function applyFunctionWithArgs($functionName, $functionArgs, $value, $dataItem) {

        switch ($functionName) {
            case "contains":
                if (is_array($value)) {
                    return (bool)array_search($functionArgs[0] ?? null, $value);
                } else if (is_string($value)) {
                    return (bool)strpos($value, $functionArgs[0] ?? null);
                }

            case "length":
                if (is_array($value)) {
                    return sizeof($value);
                } else if (is_string($value)) {
                    return strlen($value);
                }

            case "concat":
                if (is_array($value)) {
                    return array_merge($value, $functionArgs[0] ?? []);
                } elseif (is_string($value)) {
                    return $value . $functionArgs[0] ?? "";
                }

            default:
                return $value;
        }
    }


}