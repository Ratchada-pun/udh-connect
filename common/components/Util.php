<?php

namespace common\components;

class Util
{
    public static function sprintf($str = '', $len = 6) //ตัดช่องว่างข้างหน้า
    {
        if(empty($str)) return '';
        $str = preg_replace('/\s+/', '', $str);
        $strlen = mb_strlen($str);
        $n = $len - $strlen;
        return sprintf("% " . $n . "s", '') . $str;
    }

    public static function sprintfAfter($str = '', $len = 6) //ตัดช่องว่างข้างหลัง
    {
        if(empty($str)) return '';
        $str = preg_replace('/\s+/', '', $str);
        $strlen = mb_strlen($str);
        $n = $len - $strlen;
        return $str .sprintf("% " . $n . "s", '') ;
    }
}
