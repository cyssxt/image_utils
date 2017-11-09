<?php
/**
 * Created by IntelliJ IDEA.
 * User: 520Cloud
 * Date: 2017/11/9
 * Time: 10:41
 */
namespace CommonUtils;

/**
 * @param $detail 内容array（字典）
 * @param $key 属性（键）
 * @param null $default (默认值,可选)
 * @return null 返回值
 */
function getProperty($detail,$key,$default=null){
    return isset($detail[$key])?$detail[$key]:$default;
}


/**
 * @param $hexColor example #ffffff color
 * @return array  like example array(255,255,255)
 */
function hex2rgb($hexColor) {

    $color = str_replace('#', '', $hexColor);
    if (strlen($color) > 3) {
        $rgb = array(
            'r' => hexdec(substr($color, 0, 2)),
            'g' => hexdec(substr($color, 2, 2)),
            'b' => hexdec(substr($color, 4, 2))
        );
    } else {
        $color = str_replace('#', '', $hexColor);
        $r = substr($color, 0, 1) . substr($color, 0, 1);
        $g = substr($color, 1, 1) . substr($color, 1, 1);
        $b = substr($color, 2, 1) . substr($color, 2, 1);
        $rgb = array(
            hexdec($r),
            hexdec($g),
            hexdec($b)
        );
    }

    return $rgb;
}

function int($value){
    return $value;
}

function arrayToStr($data){
    $d = "array(";
    foreach ($data as $k=>$v) {
        $tmp = sprintf("'%s'=>'%s'",$k,$v);
        if(is_numeric($v)){
            $tmp=sprintf("'%s'=>%s",$k,$v);
        }
        $d = $d.$tmp.",";
    }
    $d = $d.")";
    return $d;
}

function parseArray($array){
    if(is_array($array)){
        return arrayToStr($array);
    }
    return $array;
}
