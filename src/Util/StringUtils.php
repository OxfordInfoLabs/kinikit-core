<?php

namespace Kinikit\Core\Util;

/**
 * Static string utilities
 *
 * @author mark
 *
 */
class StringUtils {

    /**
     * Generate a random string of the given length (for e.g. passwords etc).
     * Optional flags allow for capital letters, numbers and symbols
     *
     * @param $numberOfChars
     * @param bool $includeSymbols
     */
    public static function generateRandomString($numberOfChars, $includeCaps = true, $includeNumbers = true, $includeSymbols = false) {

        $possibleChars = range(97, 122);

        // If include caps, add in capital letters
        if ($includeCaps) $possibleChars = array_merge($possibleChars, range(65, 90));
        if ($includeNumbers) $possibleChars = array_merge($possibleChars, range(48, 57));
        if ($includeSymbols) $possibleChars = array_merge($possibleChars, array(33, 64, 35, 43, 61, 42, 63));


        $string = "";
        for ($i = 0; $i < $numberOfChars; $i++) {
            $string .= chr($possibleChars[rand(0, sizeof($possibleChars) - 1)]);
        }


        return $string;
    }


    /**
     * Convert a string with spaces etc to Camel Case
     *
     * @param $string
     * @return string
     */
    public static function convertToCamelCase($string) {

        $string = $string ?? "";

        // Grab all words first of all in a unicode supporting manner
        preg_match_all('/\pL+/u', $string, $allWords);

        $newString = "";
        foreach ($allWords[0] as $index => $word) {
            $newString .= ($index == 0 ? lcfirst($word) : ucfirst($word));
        }

        return $newString;

    }


    /**
     * Convert a string from camel case to space formatted with caps etc
     *
     * @param $string
     */
    public static function convertFromCamelCase($string) {

        $string = preg_replace_callback('/(^|\W)([a-zA-Z].*?)($|\W)/', function ($matches) {

            $splitCase = preg_split("/([A-Z0-9_]+)/", ucfirst($matches[2]), -1, PREG_SPLIT_DELIM_CAPTURE);

            $replacedString = "";
            foreach ($splitCase as $index => $item) {
                if (($index + 1) % 2 == 0) {
                    $replacedString .= " ";
                }
                if ($item !== "_")
                    $replacedString .= (($splitCase[$index - 1] ?? "") == "_") ? ucfirst($item) : $item;
            }

            return $matches[1] . trim($replacedString) . $matches[3];

        }, $string);


        return $string;

    }

    /**
     * Convert a string with spaces to snake_case
     *
     * @param $string
     * @return string
     */
    public static function convertToSnakeCase($string) {

        // Undo any camel case
        $string = self::convertFromCamelCase($string);

        // Grab all words first of all in a unicode supporting manner
        preg_match_all('/[0-9\pL]+/u', $string, $allWords);

        $newString = implode("_", $allWords[0]);

        return strtolower($newString);

    }

}
