<?php

namespace common\components;

class Util
{
    public static function sprintf($str = '', $len = 6)
    {
        if(empty($str)) return '';
        $str = preg_replace('/\s+/', '', $str);
        $strlen = mb_strlen($str);
        $n = $len - $strlen;
        return sprintf("% " . $n . "s", '') . $str;
    }

    public static function sprintfAfter($str = '', $len = 6)
    {
        if(empty($str)) return '';
        $str = preg_replace('/\s+/', '', $str);
        $strlen = mb_strlen($str);
        $n = $len - $strlen;
        return $str .sprintf("% " . $n . "s", '') ;
    }
}
