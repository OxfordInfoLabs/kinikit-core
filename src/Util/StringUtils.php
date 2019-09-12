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

    
}

?>
