<?php

namespace Kinikit\Core\Util;

/**
 * Static string utilities
 *
 * @author mark
 *
 */
class StringUtils {

    const MATCH_WITH_NUMBERS = "/[0-9\pL]+/u";
    const MATCH_WITHOUT_NUMBERS = "/[\pL]+/u";


    /**
     * Generate a random string of the given length (for e.g. passwords etc).
     * Optional flags allow for capital letters, numbers and symbols
     *
     * @param int $numberOfChars
     * @param bool $includeCaps
     * @param bool $includeNumbers
     * @param bool $includeSymbols
     * @return string
     */
    public static function generateRandomString(int $numberOfChars, bool $includeCaps = true, bool $includeNumbers = true, bool $includeSymbols = false): string {

        $possibleChars = range(97, 122);

        // If include caps, add in capital letters
        if ($includeCaps) $possibleChars = array_merge($possibleChars, range(65, 90));
        if ($includeNumbers) $possibleChars = array_merge($possibleChars, range(48, 57));
        if ($includeSymbols) $possibleChars = array_merge($possibleChars,[33, 64, 35, 43, 61, 42, 63]);


        $string = "";
        for ($i = 0; $i < $numberOfChars; $i++) {
            $string .= chr($possibleChars[random_int(0, count($possibleChars) - 1)]);
        }


        return $string;
    }


    /**
     * Convert a string with spaces etc to Camel Case
     *
     * @param string|null $string
     * @return string
     */
    public static function convertToCamelCase(?string $string, bool $includeNumbers = false): string {

        $string = $string ?? "";

        // Grab all words first of all in a unicode supporting manner
        preg_match_all($includeNumbers ? self::MATCH_WITH_NUMBERS : self::MATCH_WITHOUT_NUMBERS, $string, $allWords);

        $newString = "";
        foreach ($allWords[0] as $index => $word) {
            $newString .= ($index === 0 ? lcfirst($word) : ucfirst($word));
        }

        return $newString;

    }


    /**
     * Convert a string from camel case to space formatted with caps etc
     *
     * @param string $string
     * @return string
     */
    public static function convertFromCamelCase(string $string): string {

        $string = preg_replace_callback('/(^|\W)([a-zA-Z].*?)($|\W)/', static function ($matches) {

            $splitCase = preg_split("/([A-Z0-9_]+)/", ucfirst($matches[2]), -1, PREG_SPLIT_DELIM_CAPTURE);

            $replacedString = "";
            foreach ($splitCase as $index => $item) {
                if (($index + 1) % 2 === 0) {
                    $replacedString .= " ";
                }
                if ($item !== "_")
                    $replacedString .= (($splitCase[$index - 1] ?? "") === "_") ? ucfirst($item) : $item;
            }

            return $matches[1] . trim($replacedString) . $matches[3];

        }, $string);


        return $string;

    }

    /**
     * Convert a string with spaces to snake_case
     *
     * @param string $string
     * @param bool $includeNumbers
     * @return string
     */
    public static function convertToSnakeCase(string $string, bool $includeNumbers = false): string {

        // Undo any camel case
        $string = self::convertFromCamelCase($string);

        // Grab all words first of all in a unicode supporting manner
        preg_match_all($includeNumbers ? self::MATCH_WITH_NUMBERS : self::MATCH_WITHOUT_NUMBERS,
            $string, $allWords);

        $newString = implode("_", $allWords[0]);

        return strtolower($newString);

    }

}
