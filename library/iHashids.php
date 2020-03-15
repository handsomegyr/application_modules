<?php

use Hashids\Hashids;

class iHashids
{
    private static function getHasids($length = 16, $prefix = "")
    {
        $appkey = 'base64://CwKb6DveXMqB34OgbObH0jEKI/T6v42CaAW2WzWww=';
        $appname = 'hashids';
        return new Hashids(md5($prefix . $appkey . 'hashids'), $length);
    }
    public static function encodeHex($id, $length = 16, $prefix = "")
    {
        return self::getHasids($length, $prefix)->encodeHex($id);
    }
    public static function decodeHex($value, $length = 16, $prefix = "")
    {
        return self::getHasids($length, $prefix)->decodeHex($value);
    }
    public static function getID($value)
    {
        if (is_numeric($value)) {
            return $value;
        }
        return self::decodeHex($value);
    }
}
