<?php

namespace Kinikit\Core\Template\ValueFunction;

class ObjectValueFunction extends ValueFunctionWithArguments {

    const supportedFunctions = [
        "member",
        "keyValueArray",
        "keys",
        "values",
        "combine",
        "wrapAsArray",
        "setMember",
        "unsetMember",
        "model"
    ];

    /**
     * Get the supported functions returned for this value function
     *
     * @return string[]|void
     */
    protected function getSupportedFunctionNames() {
        return self::supportedFunctions;
    }


    /**
     * Apply one of the supported functions and return
     *
     * @param $functionName
     * @param $functionArgs
     * @param $value
     * @param $model
     * @return mixed|void
     */
    protected function applyFunctionWithArgs($functionName, $functionArgs, $value, $model) {

        // Special case for model
        if ($functionName === "model") {
            return $model;
        }

        if (is_array($value)) {
            switch ($functionName) {
                case "member":
                    return $value[$functionArgs[0]] ?? null;

                case "keyValueArray":
                    $returnArray = [];
                    $propertyKey = $functionArgs[0] ?? 'key';
                    $valueKey = $functionArgs[1] ?? 'value';

                    foreach ($value as $key => $item) {
                        $object = [];
                        $object[$propertyKey] = $key;
                        $object[$valueKey] = $item;

                        $returnArray[] = $object;
                    }

                    return $returnArray;

                case "keys":
                    return array_keys($value);

                case "values":
                    return array_values($value);

                case "combine":
                    foreach ($functionArgs as $arg) {
                        if (is_array($arg)) {
                            $value = array_merge($value, $arg);
                        }
                    }
                    return $value;

                case "wrapAsArray":
                    return [$value];

                case "setMember":
                    $key = $functionArgs[0];
                    $val = $functionArgs[1];
                    $notNullOnly = $functionArgs[2] ?? false;

                    if ($notNullOnly && (is_null($val) || $val === "")) {

                    } else {
                        $value[$key] = $val;
                    }

                    return $value;

                case "unsetMember":
                    unset($value[$functionArgs[0]]);
                    return $value;

                default:
                    return $value;
            }
        } else {
            return $value;
        }

    }
}
