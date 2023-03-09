<?php
namespace Pioc\Utils;

class TimeHelper {

    public static function miliSecondNow() {

        $microtime = microtime();
        $comps = explode(' ', $microtime);

        // Note: Using a string here to prevent loss of precision
        // in case of "overflow" (PHP converts it to a double)
        return sprintf('%d%03d', $comps[1], $comps[0] * 1000);
    }

    // public static function floatTimeToMiliSecond($tf) {
    //     return $tf * 1000;
    //     // $comps = explode('.', $tf);
    //     // $c0 = $comps[0];
    //     // $c1 = isset($comps[1]) ? $comps[1] : 0;
    //     // return sprintf('%d%03d', $c0, $c1);
    // }

}
