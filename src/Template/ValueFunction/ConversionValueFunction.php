<?php

namespace Kinikit\Core\Template\ValueFunction;

class ConversionValueFunction extends ValueFunctionWithArguments {

    const supportedFunctions = [
        "toJSON",
        "toNumber",
        "urlencode",
        "urlencodeparams",
        "htmlToText"
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
     * Apply a function with args
     *
     * @param $functionName
     * @param $functionArgs
     * @param $value
     * @param $dataItem
     * @return mixed|void
     */
    protected function applyFunctionWithArgs($functionName, $functionArgs, $value, $dataItem) {
        switch ($functionName) {
            case "toJSON":
                return $value ? json_encode($value) : $value;

            case "toNumber":
                $value = preg_replace("/[^0-9]/", "", $value ?? "");
                if (is_numeric($value)) {
                    return strpos($value, ".") ? floatval($value) : intval($value);
                } else {
                    return $functionArgs[0] ?? null;
                }

            case "urlencodeparams":
            case "urlencode":
                $urlComponents = parse_url($value);

                $url = ($urlComponents["scheme"] ?? null) ? ($urlComponents["scheme"] . "://") : "";
                if (isset($urlComponents["host"])) {
                    if (!$url) $url = "//";
                    $url .= $urlComponents["host"];
                }
                if (isset($urlComponents["port"])) {
                    $url .= ":" . $urlComponents["port"];
                }
                if (isset($urlComponents["path"])) {
                    $url .= $urlComponents["path"];
                }


                if ($urlComponents["query"] ?? null) {
                    $rawParams = explode("&", ltrim($urlComponents["query"], "?"));
                    $queryParams = [];
                    foreach ($rawParams as $param) {
                        $exploded = explode("=", $param);
                        if (sizeof($exploded) == 2)
                            $queryParams[] = $exploded[0] . "=" . rawurlencode($exploded[1]);
                    }
                    $url .= "?" . join("&", $queryParams);
                }
                return $url;

            case "htmlToText":
                $value = strip_tags($value);
                $escapeChars = str_split($functionArgs[0] ?? "");
                foreach ($escapeChars as $char) {
                    $value = str_replace($char, "\\$char", $value);
                }


                return $value;
        }
    }
}