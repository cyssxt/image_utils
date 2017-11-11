<?php
/**
 * Created by IntelliJ IDEA.
 * User: 520Cloud
 * Date: 2017/11/9
 * Time: 10:53
 */

include(dirname(__FILE__)."/../utils/CommonUtils.php");
require(dirname(__FILE__)."/./ImageGenerator.php");
require(dirname(__FILE__)."/./CyLang.php");
define ("default_font_path",dirname(__FILE__)."/../sources/PingFang_Bold.ttf");
define("CYSSXT_IMAGEUTIL_CORE_PATH_ENTER",__FILE__);
class Enter
{
    static private $DetailParams = array(
        "color" => Array(255, 255, 255),
        "font" => default_font_path,
        "scale" => 1);

    static function  createImageByJson($width, $height = 1000,$fileName, $detailJson, $configs=null, $selectParams=array())
    {
        if(is_string($configs)){
            $configs = json_decode($configs,true);
        }
        $selectParams = array_merge(Enter::$DetailParams,$selectParams);
        return Enter::createImage($width, $height, $fileName, $detailJson,
            $configs, \CommonUtils\getProperty($selectParams, "color", Array(255, 255, 255)),
            \CommonUtils\getProperty($selectParams, "font", "../sources/font/PingFang_Bold.ttf")
            , \CommonUtils\getProperty($selectParams, "scale", 1),$selectParams);
    }

    static function createImageByDefault($width, $height = 1000, $detailFile, $configFileList, $fileName, $selectParams = array())
    {
        if (!$selectParams) {
            $selectParams = Enter::$DetailParams;
        }
        $configs = array();
        foreach ($configFileList as $config) {
            $configArray = json_decode(file_get_contents($config), true);
            $configs = array_merge($configs,$configArray);
        }
        $detailJson = file_get_contents($detailFile);
        return Enter::createImageByJson($width, $height, $fileName, $detailJson, $configs, $selectParams);
    }


    static function createImage($width, $height, $fileName, $detailJson, $configs, $color = Array(255, 255, 255), $font = "", $scale = 2,$selectParams = null)
    {
        $cy = new CyLang($configs);
        $detailJson = $cy->parseAll($detailJson);
        $detail = json_decode($detailJson,true);
        $padding = \CommonUtils\getProperty($detail,"padding",0);
        $width = \CommonUtils\getProperty($detail,"width",$width);
        $height = \CommonUtils\getProperty($detail,"height",$height);
        $bgColor = \CommonUtils\getProperty($detail,"bgColor",$color);
        $contents = \CommonUtils\getProperty($detail,"content",array());
        $image = new ImageGenerator($width, $height, $bgColor, $font, $scale,$detail);
        foreach ($contents as $d) {
            $type = \CommonUtils\getProperty($d, "type");
            switch ($type) {
                case "image":
                    $image->drawImageByDetail($d);
                    break;
                case "text":
                    $image->drawTextByDetail($d);
                    break;
                case "bar":
                    $image->drawBar($d);
                    break;
                case "rect":
                    $image->drawRectByDetail($d);
                    break;
                case "list":
                    $image->drawList($d);
                default:
                    break;
            }
        }
        $image->save($fileName);
    }
}

function test()
{
    Enter::createImageByJson(350, 5000,"../2.png", '[{"type":"text","content":"{{data.content}}","color":##array(0,0,0)##,"marginTop":10,"fontSize":20}]', '{"data":{"type":"text","content":"aaaaa"}}');

}
//test();