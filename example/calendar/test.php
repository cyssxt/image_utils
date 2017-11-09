<?php

include("../../core/Enter.php");
include ('../../utils/DateUtils.php');

//Enter::createImageByDefault(750,500,"./calendar.json",array(),"3.png");
//
//return;
$calendar = \DateUtils\getCurrentCalendar();
$arrayList = array();
$format = '{
          "type":"text",
          "content":"%s"
        }';
$bgImage = str_replace("\\","\\\\",__DIR__.('/./timg.png'));
$data = '{
  "padding":10,
  "marginBottom":250,
  "autoHeight":false,
  "scale":1,
  "bgImage":"'.$bgImage.'",
  "content":[
    {
      "type":"list",
      "color":"##array(0,0,0)##",
      "fontSize":10,
      "colNum":7,
      "marginTop":20,
      "textAlign":"center",
      "content":%s
    }
  ]
}';
$dataList = array();
$today=date("Y-m-d",time());
foreach ($calendar as $item){
    $week = -1;
    $day = -1;
    $str=null;
    $height = 0;
    $borderTop = null;
    if(is_string($item)){
        $str = $item;
        if($item=="星期六" || $item=="星期日"){
            $week = 6;
        }
        $height = 25;
        $borderTop = "##array(85,170,255)##";
    }else {
        $timestamp = date_timestamp_get($item);
        $day = date("Y-m-d", $timestamp);
        $str = date("d", $timestamp);
        $week = date("w", $timestamp);
        $borderTop = "##array(200,202,204)##";
    }
    $str = sprintf($format,$str);
    $str = json_decode($str,true);
    if($week==6 || $week == 0){
        $str = array_merge($str,array("color"=>"##array(255,0,0)##"));
    }
    if($today==$day){
        $str = array_merge($str,array("bgColor"=>"##array(255,0,0,100)##","color"=>"##array(255,255,255)##"));
    }
    if($height){
        $str = array_merge($str,array("height"=>$height));//#C8CACC
    }
    if($borderTop){
        $str = array_merge($str,array("borderTop"=>$borderTop));//#C8CACC
    }
    array_push($dataList,$str);
}
$result = sprintf($data,json_encode($dataList,JSON_UNESCAPED_SLASHES));
echo $result;
Enter::createImageByJson(375,700,"./result.png",$result,null);
