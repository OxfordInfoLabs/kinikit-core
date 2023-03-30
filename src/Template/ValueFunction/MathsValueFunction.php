<?php

namespace Kinikit\Core\Template\ValueFunction;

class MathsValueFunction extends ValueFunctionWithArguments {

    const supportedFunctions = [
        "add",
        "subtract",
        "multiply",
        "divide",
        "modulo",
        "decimalplaces",
        "commaseparatedthousands",
        "floor",
        "ceil",
        "round"
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

        if (is_numeric($value)) {

            switch ($functionName) {
                case "add":
                    $addition = $functionArgs[0];
                    if (is_numeric($value) && is_numeric($addition)) {
                        return is_int($value) && is_int($addition) ? gmp_strval(gmp_add("$value", "$addition")) : $value + $addition;
                    } else {
                        return null;
                    }

                case "subtract":
                    $subtraction = $functionArgs[0];
                    if (is_numeric($value) && is_numeric($subtraction)) {
                        return is_int($value) && is_int($subtraction) ? gmp_strval(gmp_sub("$value", "$subtraction")) : $value - $subtraction;
                    } else {
                        return null;
                    }

                case "multiply":
                    $multiplier = $functionArgs[0];
                    if (is_numeric($value) && is_numeric($multiplier)) {
                        return is_int($value) && is_int($multiplier) ? gmp_strval(gmp_mul("$value", "$multiplier")) : $value * $multiplier;
                    } else {
                        return null;
                    }

                case "divide":
                    $divisor = $functionArgs[0];
                    return is_numeric($value) && is_numeric($divisor) ? $value / $divisor : null;

                case "modulo":
                    $modulo = $functionArgs[0];
                    return is_numeric($value) && is_numeric($modulo) && is_int($value) && is_int($modulo) ?
                        gmp_strval(gmp_div_r("$value", "$modulo")) : (is_numeric($value) && is_numeric($modulo) ? $value % $modulo : null);

                case "decimalplaces":
                    return round($value, $functionArgs[0] ?? 0);

                case "commaseparatedthousands":
                    return number_format($value);

                case "floor":
                    return is_numeric($value) ? floor($value) : null;

                case "ceil":
                    return is_numeric($value) ? ceil($value) : null;

                case "round":
                    return round($value);

                default:
                    return $value;

            }

        } else {
            return $value;
        }

    }
}