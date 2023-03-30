<?php

namespace Kinikit\Core\Template\ValueFunction;

class ObjectValueFunction extends ValueFunctionWithArguments {

    const supportedFunctions = [
        "member",
        "keyValueArray",
        "keys",
        "values",
        "combine",
        "wrapAsArray"
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
     * @param $dataItem
     * @return mixed|void
     */
    protected function applyFunctionWithArgs($functionName, $functionArgs, $value, $dataItem) {

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
                    if (is_array($value)) {
                        return $value;
                    } else {
                        return [$value];
                    }
                default:
                    return $value;
            }
        } else {
            return $value;
        }

    }
}
