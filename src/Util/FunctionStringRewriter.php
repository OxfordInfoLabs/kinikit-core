<?php

namespace Kinikit\Core\Util;

use Kinikit\Core\Exception\InsufficientParametersException;

class FunctionStringRewriter {

    /**
     * New rewrite logic.
     *
     * @param $string
     * @param $search
     * @param $replace
     * @param array $defaultValues
     * @param array $parameterValues
     */
    public static function rewrite($string, $search, $replace, $defaultValues = [], &$parameterValues = []) {

        preg_match_all("/(.*?)($search\()/i", $string, $matches, PREG_OFFSET_CAPTURE);

        $functionMatches = array_reverse($matches[2]);
        $prefixCharacterMatches = array_reverse($matches[1]);


        foreach ($functionMatches as $index => $functionMatch) {

            // Work out the match offset
            $matchOffset = $functionMatch[1];

            // Eliminate any matches which are contained in other functions.
            if ($prefixCharacterMatches[$index][0]) {
                preg_match("/[a-zA-Z_]+/", substr($prefixCharacterMatches[$index][0], -1), $prefixCharacterItems);
                if (sizeof($prefixCharacterItems)) continue;
            }

            // Grab any preceding parameters
            $paramsBeforeMatch = substr_count($string, "?", 0, $matchOffset);

            // Now grab the forward arguments including nested ones
            $functionArgs = self::extractArgs(substr($string, $matchOffset), $search, true, $functionStringLength);

            // Now process the function args to grab params
            $totalFunctionParams = 0;
            $argParams = [];
            foreach ($functionArgs as $functionArg) {

                if ($functionArg !== null) {
                    $numberOfFunctionParams = substr_count($functionArg, "?");
                    $argParams[] = array_slice($parameterValues, $paramsBeforeMatch + $totalFunctionParams, $numberOfFunctionParams);
                    $totalFunctionParams += $numberOfFunctionParams;
                }
            }

            // Collect new function params
            $newFunctionParams = [];

            // Substitute values
            $newFunctionString = preg_replace_callback("/\\$([0-9])/", function ($replacePlaceholders) use ($functionArgs, $argParams, $defaultValues, &$newFunctionParams) {
                $placeholderIndex = $replacePlaceholders[1] - 1;
                if (isset($functionArgs[$placeholderIndex])) {
                    $newFunctionParams = array_merge($newFunctionParams, $argParams[$placeholderIndex]);
                    return $functionArgs[$placeholderIndex];
                } else if (isset($defaultValues[$placeholderIndex])) {
                    return $defaultValues[$placeholderIndex];
                } else {
                    throw new InsufficientParametersException("Number of default values doesn't match.");
                }
            }, $replace);

            // Replace the string
            $string = substr($string, 0, $matchOffset) . $newFunctionString . substr($string, $matchOffset + $functionStringLength);

            // Modify parameter values to reference new set
            array_splice($parameterValues, $paramsBeforeMatch, $totalFunctionParams, $newFunctionParams);

        }

        return $string;

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
    public static function extractArgs($functionString, $functionName, $atStart = true, &$functionStringLength = 0) {

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
                        $functionStringLength = $i + 1;
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