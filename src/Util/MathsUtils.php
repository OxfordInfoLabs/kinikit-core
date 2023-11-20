<?php

namespace Kinikit\Core\Util;

class MathsUtils {
    public static function dot($v1, $v2){
        $out = 0;

        if (count($v1) != count($v2)){
            throw new \Exception("Tried to dot two vectors of non-matching lengths");
        }

        for ($i = 0; $i < count($v1); $i++){
            $out += $v1[$i] * $v2[$i];
        }

        return $out;
    }
}