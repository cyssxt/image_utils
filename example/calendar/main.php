<?php

include("../../core/Enter.php");
include ('../../utils/DateUtils.php');
include ('../../utils/Calendar.php');

//Enter::createImageByDefault(750,500,"./calendar.json",array(),"3.png");
//
//return;
$calendar = \DateUtils\getCurrentCalendar();
$arrayList = array();
$format = '{
          "type":"text",
          "content":"%s",
          "height":60
        }';
$bgImage = str_replace("\\","\\\\",__DIR__.('/./timg.jpg'));
$url = $bgImage;
$resultImage = "./result.jpg";
$solarCalendar = new \DateUtils\Calendar();
$today = $solarCalendar->convertSolarToLunarByDate(time());
$weekDay = DateUtils\getWeekeDay(time());
$data = '{
  "marginBottom":30,
  "autoHeight":false,
  "scale":2,
  "bgImage":"'.$url.'",
  "autoHeight":true,
  "content":[
    {
        "type":"text",
        "width":375,
        "lineHeight":45,
        "height":45,
        "color":"##array(255, 255, 255)##",
        "content":"'.date("Y年m日d日",time()).'",
        "bgColor":"##array(218, 87, 81,50)##",
        "textAlign":"center",
        "vertialAlign":"middle",
        "fontSize":17
    }
    ,{
        "type":"rect",
        "width":375,
        "height":2,
        "bgColor":"##array(240, 117, 117,50)##"
    },
    {
        "type":"list",
        "width":375,
        "lineHeight":77,
        "height":77,
        "color":"##array(255, 255, 255)##",
        "vertialAlign":"middle",
        "bgColor":"##array(218, 87, 81,50)##",
        "fontSize":60,
        "content":[
            {
                "type":"text",
                "content":"'.date("d",time()).'",
                "fontSize":60,
                "width":80
            },{
                "type":"text",
                "content":"'.$weekDay.",".$today[1].$today[2].'",
                "fontSize":20,
                "width":80,
                "marginLeft":40
            },{
                "type":"text",
                "content":"'.$today[6].'",
                "fontSize":20,
                "width":80,
                "marginLeft":250
            }
        ]
    },{
        "type":"rect",
        "width":375,
        "height":2,
        "bgColor":"##array(240, 117, 117,50)##"
    },
    {
      "type":"list",
      "color":["##array(255,255,255)##","##array(255,255,255)##"],
      "fontSize":[15,10],
      "colNum":7,
      "marginTop":20,
      "textAlign":"center",
      "content":%s,
      "width":375,
      "childHeight":60,
      "paddingBottom":30,
      "bgColor":"##array(14,14,14,50)##"
    }
  ]
}';
$dataList = array();
$today=date("Y-m-d",time());
$currentMounth = date("m",time());
foreach ($calendar as $item){
    $week = -1;
    $day = -1;
    $str=null;
    $height = 0;
    $borderTop = null;
    $dayMonthFlag = true;
    if(is_string($item)){
        if($item=="星期六" || $item=="星期日"){
            $week = 6;
        }
        $item = preg_replace("/星期/","",$item);
        $str = $item;
        $height = 50;
        $borderTop = "##array(85,170,255)##";
    }else {
        $timestamp = date_timestamp_get($item);
        $day = date("Y-m-d", $timestamp);
        $str = date("d", $timestamp);
        $week = date("w", $timestamp);
        $borderTop = "##array(200,202,204)##";
        $solor = $solarCalendar->convertSolarToLunarByDate($timestamp);
//        echo $str."===".count($solor)."\r\n";
        if(count($solor)>2){
            $str = $str.",".(CommonUtils\getProperty($solor,"name")?$solor["name"]:$solor[2]);
        }
        $dayMonth = date("m",$timestamp);
        $dayMonthFlag = $dayMonth==$currentMounth;
    }
//    echo $str;
    $str = sprintf($format,$str);
    $str = json_decode($str,true);
    if($week==6 || $week == 0){
        $str = array_merge($str,array("color"=>"##array(255,0,0)##"));
    }
    if($today==$day){
        $str = array_merge($str,array("bgColor"=>"##array(236,82,82)##","radius"=>10,"color"=>"##array(255,255,255)##"));
    }
    if($height){
        $str = array_merge($str,array("height"=>$height,"fontSize"=>15));//#C8CACC
    }
    //非本月置灰
    if(!$dayMonthFlag){
        $str = array_merge($str,array("color"=>"##array(200,200,200)##"));
    }
//    if($borderTop){
//        $str = array_merge($str,array("borderTop"=>$borderTop));//#C8CACC
//    }
    array_push($dataList,$str);
}
$result = sprintf($data,json_encode($dataList,JSON_UNESCAPED_SLASHES));
Enter::createImageByJson(375,700,$resultImage,$result,null);
