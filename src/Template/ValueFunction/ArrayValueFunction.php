<?php


namespace Kinikit\Core\Template\ValueFunction;

class ArrayValueFunction extends ValueFunctionWithArguments {

    const supportedFunctions = [
        "memberValues",
        "join",
        "slice",
        "item",
        "pop",
        "shift",
        "mergeValues",
        "distinct",
        "filter",
        "sort",
        "sum"
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

        if (is_array($value)) {

            switch ($functionName) {

                case "memberValues":
                    $member = $functionArgs[0] ?? "";
                    $values = [];
                    foreach ($value as $item) {
                        $values[] = $item[$member] ?? null;
                    }
                    return $values;


                case "join":
                    $separator = $functionArgs[0] ?? ",";
                    return implode($separator, $value);


                case "slice":
                    $offset = $functionArgs[0];
                    $length = $functionArgs[1] ?? null;

                    return array_slice($value, $offset, $length);


                case "item":
                    return $value[$functionArgs[0]] ?? null;


                case "pop":
                    return array_pop($value);


                case "shift":
                    return array_shift($value);

                case "mergeValues":
                    $result = [];
                    foreach ($value as $array) {
                        $result = array_merge($result, $array);
                    }
                    return $result;

                case "distinct":
                    return array_values(array_unique($value));

                case "sort":
                    asort($value);
                    return array_values($value);

                case "sum":
                    return array_sum($value);

                case "filter":
                    return $this->filter($value, $functionArgs[0], $functionArgs[1], $functionArgs[2] ?? "equals");

                default:
                    return $value;

            }
        } else {
            return $value;
        }

    }

    /**
     * @param array $array
     * @param string $fieldName
     * @param string $fieldValue
     * @param string $filterType
     * @return array
     */
    private function filter($array, $fieldName, $fieldValue, $filterType) {

        $filteredArray = [];

        foreach ($array as $elt) {
            if (!is_array($elt)) {
                continue;
            }

            switch ($filterType) {
                case "equals":
                    if ($elt[$fieldName] == $fieldValue) {
                        $filteredArray[] = $elt;
                    }
                    break;
                case "notequals":
                    if ($elt[$fieldName] != $fieldValue) {
                        $filteredArray[] = $elt;
                    }
                    break;
                case "like":
                    if (strpos($elt[$fieldName], $fieldValue)) {
                        $filteredArray[] = $elt;
                    }
                    break;
                case "startsWith":
                    if (strtolower(substr($elt[$fieldName], 0, 1)) == strtolower($fieldValue)) {
                        $filteredArray[] = $elt;
                    }
                    break;
                case "gte":
                    if ($elt[$fieldName] >= $fieldValue) {
                        $filteredArray[] = $elt;
                    }
                    break;
                case "gt":
                    if ($elt[$fieldName] > $fieldValue) {
                        $filteredArray[] = $elt;
                    }
                    break;
                case "lte":
                    if ($elt[$fieldName] <= $fieldValue) {
                        $filteredArray[] = $elt;
                    }
                    break;
                case "lt":
                    if ($elt[$fieldName] < $fieldValue) {
                        $filteredArray[] = $elt;
                    }
                    break;
                case "in":
                    if (is_array($fieldValue)) {
                        if (is_array($elt[$fieldName])) {
                            if (in_array($fieldValue, $elt[$fieldName])) {
                                $filteredArray[] = $elt;
                            }
                        } else {
                            if (in_array($elt[$fieldName], $fieldValue)) {
                                $filteredArray[] = $elt;
                            }
                        }
                    }
                    break;
                case "contains":
                    if ($elt[$fieldName] == $fieldValue) {
                        $filteredArray[] = $elt;
                    }
                    break;
                case "notContains":
                    if ($elt[$fieldName] != $fieldValue) {
                        $filteredArray[] = $elt;
                    }
                    break;
            }
        }

        return $filteredArray;
    }
}