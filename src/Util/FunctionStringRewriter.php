<?php

namespace Kinikit\Core\Util;


use Kinikit\Core\Logging\Logger;

class FunctionStringRewriter {

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


    private static function rewriteInstance($string, $search, $replace, $defaultValues = []) {

        $args = self::extractArgs($string, $search);

        if ($args == []) {
            return $string;
        }

        $newArgs = [];

        foreach ($args as $arg) {
            $newArgs[] = self::rewriteInstance($arg, $search, $replace, $defaultValues);
        }


        $function = $search . "(" . implode(",", $args) . ")";


        for ($i = 1; $i < sizeof($defaultValues) + 1; $i++) {
            $arg = $newArgs[$i - 1] ?? $defaultValues[$i - 1];
            $replace = str_replace("$" . $i, $arg, $replace);
        }

        $result = str_replace($function, $replace, $string);


        return $result;
    }

    public static function rewrite($string, $search, $replace, $defaultValues = []) {

        if (substr_count($replace, "$") != sizeof($defaultValues)) {
            throw new \Exception("Number of default values doesn't match.");
        }

        if (!$replace || !$search) {
            return $string;
        }

        $result = "";
        $cursor = 0;

        do {
            if ($cursor > strlen($string)) {
                $result .= substr($string, $cursor) ?? "";
                break;
            }


            $bracketCount = 0;
            $remainingString = substr($string, $cursor);

            preg_match("/[\s,(]$search\(|^$search\(/", $remainingString, $matches, PREG_OFFSET_CAPTURE);


            $instanceStartPos = $matches[0][1] ?? null;


            if (is_null($instanceStartPos)) {
                $result .= substr($string, $cursor) ?? "";
                break;
            }



            if ($instanceStartPos > 0) {
                $instanceStartPos += 1;
                $result .= substr($remainingString, 0, $instanceStartPos);
                $cursor += $instanceStartPos;
            }

            $cursor += strlen($search);

            $found = false;

            for ($i = $cursor; $i < strlen($string); $i++) {
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
                    $functionInstance = substr($remainingString, $instanceStartPos, $instanceEndPos - $instanceStartPos);
                    $cursor += strlen($functionInstance) - strlen($search);
                    break;
                }

            }

            if ($found) {
                $resultInstance = self::rewriteInstance($functionInstance, $search, $replace, $defaultValues);
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

}