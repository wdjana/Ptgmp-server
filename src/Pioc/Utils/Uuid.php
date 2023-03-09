<?php
namespace Pioc\Utils;

class Uuid {
    public static function v4($type = 0xA0)
    {
        $data = openssl_random_pseudo_bytes(16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x0f | $type);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public static function validate($uuid)
    {
        return !empty($uuid) &&
            is_string($uuid) &&
            preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $uuid);
    }
}
