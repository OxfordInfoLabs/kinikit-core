<?php

namespace Kinikit\Core\Util;

/**
 * Number utility classes.
 */
class NumberUtils {

    /**
     * Convenience function for formatting a number as money using the supplied currency symbol
     *
     * @param $value
     * @param string $currencySymbol
     */
    public static function formatAsMoney($value, $currencySymbol = "&pound;", $roundToInteger = false) {
        if (is_numeric($value))
            return $currencySymbol . number_format($value, $roundToInteger ? 0 : 2, ".", "");
        else
            return null;
    }

}