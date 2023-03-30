<?php

namespace Kinikit\Core\Template\ValueFunction;

class StringValueFunction extends ValueFunctionWithArguments {
    const supportedFunctions = [
        "substring",
        "concat",
        "toUTF8",
        "trim",
        "explode",
        "replace",
        "contains",
        "uppercase",
        "lowercase",
        "append",
        "prepend",
        "split",
        "initialCaps",
        "words",
        "hash",
        "md5",
        "startsWith",
        "endsWith"
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

        if (is_string($value)) {

            switch ($functionName) {
                case "substring":
                    $offset = $functionArgs[0];
                    $length = $functionArgs[1] ?? null;

                    if ($length) {
                        return substr($value, $offset, $length);
                    } else {
                        return substr($value, $offset);
                    }

                case "concat":
                    $string = $value;
                    foreach ($functionArgs as $arg) {
                        $string .= $arg;
                    }

                    return $string;

                case "toUTF8":
                    return preg_replace('/(\xF0\x9F[\x00-\xFF][\x00-\xFF])/', "", $value) == $value ? $value : null;

                case "trim":
                    return trim($value, $functionArgs[0]);

                case "split":
                case "explode":
                    return explode($functionArgs[0], $value);

                case "replace":
                    $search = $functionArgs[0] ?? null;
                    $replace = $functionArgs[1] ?? null;

                    if (substr($search, 0, 1) == "/" &&
                        substr($search, -1, 1) == "/") {

                        return preg_replace($search, $replace, $value);
                    } else {
                        return str_replace($search, $replace, $value);
                    }

                case "contains":
                    return (bool)strpos($value, $functionArgs[0]);

                case "lowercase":
                    return strtolower($value);

                case "uppercase":
                    return strtoupper($value);

                case "append":
                    foreach ($functionArgs as $arg) {
                        $value .= $arg;
                    }
                    return $value;

                case "prepend":
                    foreach ($functionArgs as $arg) {
                        $value = $arg . $value;
                    }
                    return $value;

                case "initialCaps":
                    return strlen($value) > 1 ? strtoupper(substr($value, 0, 1)) . strtolower(substr($value, 1)) : strtoupper($value);

                case "words":
                    return explode(" ", $value);

                case "hash":
                    return hash("sha512", $value);

                case "md5":
                    return md5($value);

                case "startsWith":
                    return substr($value, 0, 1);

                case "endsWith":
                    return substr($value, -1);
            }

            return $value;

        } else {
            return $value;
        }

    }

}
