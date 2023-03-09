<?php
namespace Pioc\Utils;

class TokenValidator {

    public static function validate($token) {
        return !empty($token) &&
            is_string($token) &&
            preg_match('/^[a-z1-9]{7}$/', $token) || false;

        // $retval = false;
        //
        // if (!empty($token) &&
        //     is_string($token) &&
        //     preg_match('/^[a-z1-9]{7}$/', $token)) {
        //     $retval = true;
        // }
        //
        // return $retval;
    }

}
