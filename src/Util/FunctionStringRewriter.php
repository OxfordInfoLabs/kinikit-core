<?php

namespace Kinikit\Core\Util;

class FunctionStringRewriter {

    public static function rewrite($string, $search, $replace, $defaultValues = [], &$parameterValues = []) {
        if (substr_count($replace, "$") != sizeof($defaultValues)) {
            throw new \Exception("Number of default values doesn't match.");
        }

        if (!$replace || !$search) {
            return $string;
        }

        $string = str_replace(", ", ",", $string);

        $result = "";
        $cursor = 0;

        do {
            if ($cursor > strlen($string)) {
                $result .= substr($string, $cursor) ?? "";
                break;
            }


            $bracketCount = 0;
            $remainingString = substr($string, $cursor);

            preg_match("/[\s,(]$search\(|^$search\(/i", $remainingString, $matches, PREG_OFFSET_CAPTURE);

            $relativeInstanceStartPos = $matches[0][1] ?? null;


            if (is_null($relativeInstanceStartPos)) {
                $result .= substr($string, $cursor) ?? "";
                break;
            }

            if ($relativeInstanceStartPos > 0) {
                $relativeInstanceStartPos += 1;
                $result .= substr($remainingString, 0, $relativeInstanceStartPos);
                $cursor += $relativeInstanceStartPos;
            }

            $trueInstanceStartPos = $relativeInstanceStartPos + (strlen($string) - strlen($remainingString));

            $argStart = $cursor + strlen($search);

            $found = false;

            for ($i = $argStart; $i < strlen($string); $i++) {
                $char = $string[$i];

                switch ($char) {
                    case "(":
                        $bracketCount++;
                        break;
                    case  ")":
                        $bracketCount--;
                        break;
                }

                if ($bracketCount == 0) {
                    $found = true;
                    $instanceEndPos = $i + 1;
                    $length = $instanceEndPos - $trueInstanceStartPos + 1;
                    $functionInstance = substr($string, $trueInstanceStartPos, $length);
                    $cursor += strlen($functionInstance);
                    break;
                }

            }

            $paramCount = substr_count($result, "?");
            $nestedParameterValues = array_slice($parameterValues, $paramCount);

            if ($found) {
                $resultInstance = self::rewriteInstance($functionInstance, $search, $replace, $defaultValues, $nestedParameterValues);
                array_splice($parameterValues, $paramCount, null, $nestedParameterValues);
                $result .= $resultInstance;
            } else { 
                $result .= $remainingString;
                break;
            }

            if ($result == "") {
                break;
            }

        } while (true);

        return $result;

    }

    private static function rewriteInstance($string, $search, $replace, $defaultValues = [], &$parameterValues = []) {
        $args = self::extractArgs($string, $search);

        if ($args == []) {
            return $string;
        }

        $newArgs = [];
        $paramOffset = 0;
        foreach ($args as $arg) {
            $argParamCount = substr_count($arg ?? "", "?");
            $argumentParamValues = array_slice($parameterValues, $paramOffset, $argParamCount);

            $newArg = self::rewriteInstance($arg, $search, $replace, $defaultValues, $argumentParamValues);
            $newArgs[] = $newArg;
            array_splice($parameterValues, $paramOffset, substr_count($newArg ?? "", "?"), $argumentParamValues);
            $paramOffset += $argParamCount;

        }

        preg_match_all("/\\\$([0-9]+)/", $replace, $matches);
        $matches = $matches[1];
        $count = 0;

        $function = $search . "(" . implode(",", $args) . ")";
        for ($i = 1; $i < sizeof($defaultValues) + 1; $i++) {
            $arg = $newArgs[$i - 1] ?? $defaultValues[$i - 1];
            $replace = str_replace("$" . $i, $arg ?? "", $replace);
            if ($arg == "?")
                $count++;
        }


        if ($string == $function) {
            $newParams = [];
            foreach ($matches as $match) {
                if (($newArgs[$match - 1] ?? $defaultValues[$match - 1]) == '?') {
                    $newParams[] = $parameterValues[$match - 1];
                }
            }

            $parameterValues = array_splice($parameterValues, 0, $count, $newParams);
        }

        $result = str_ireplace($function, $replace, $string);

        return $result;
    }

    /**
     * Iterate through the string, using commas to identify the end of arguments
     *
     * @param string $functionString
     * @param string $functionName
     * @param bool $atStart
     *
     * @return string[]
     */
    public static function extractArgs($functionString, $functionName, $atStart = true) {

        $functionString = $functionString ?? "";
        $args = [];
        $currentArg = "";
        $pos = strpos($functionString, $functionName) + strlen($functionName) + 1;
        $bracketCount = 0;

        // Deal with the case of the function name being a substring of a different function
        if (!$atStart) {
            preg_match_all("/[^a-zA-Z_]" . $functionName . "[^a-zA-Z_]/", $functionString, $matches, PREG_OFFSET_CAPTURE);
            $matches = $matches[0];
        }

        if (isset($matches[0])) {
            $pos = $matches[0][1] + strlen($functionName) + 2;
        }

        // Carry out bracket counting to parse arguments by commas
        $length = strlen($functionString);
        for ($i = $pos; $i < $length; $i++) {
            $char = $functionString[$i];

            switch ($char) {
                case '(':
                    $bracketCount++;
                    $currentArg .= $char;
                    break;
                case ')':
                    $bracketCount--;
                    if ($bracketCount == -1) {
                        $currentArg = trim($currentArg);
                        if ($currentArg == "") {
                            $currentArg = null;
                        }
                        $args[] = $currentArg;
                        return $args;
                    }
                    $currentArg .= $char;
                    break;
                case ',':
                    if ($bracketCount == 0) {
                        $args[] = trim($currentArg);
                        $currentArg = "";
                    } else {
                        $currentArg .= $char;
                    }
                    break;
                default:
                    $currentArg .= $char;

            }

        }

        return [];

    }
}