<?php
/**
 * Created by IntelliJ IDEA.
 * User: 520Cloud
 * Date: 2017/11/9
 * Time: 15:12
 */

namespace DateUtils;


function getFirstDayOfMonth($time=null){
    if(!$time){
        $time = date_format(date_create(),"Y-m-d");
    }
    $longTime = strtotime("$time");
    $time = date_create(date("Y-m-1",$longTime));
    return $time;
}

function getLastDayOfMonth($time = null){
    if(!$time){
        $time = date_format(date_create(),"Y-m-d");
    }
    $nextMonth = date("Y-m-1",strtotime("$time 1 month"));
    $longTime =strtotime("$nextMonth -1 day");
    $time = date_create(date("Y-m-d",$longTime));
    return $time;
}

function parseDate($time){
    if(!$time){
        $time = date_format(date_create(),"Y-m-d");
    }
    return date_create("Y-m-d",strtotime("Monday -1 week",date_timestamp_get($time)));
}


function test(){
    $firstDay = getFirstDayOfMonth();
    $endDay = getLastDayOfMonth();
    $currentDay = $firstDay;
    while($currentDay){
        $dValue = date_timestamp_get($endDay) -date_timestamp_get($currentDay);
        if($dValue<0){
            break;
        }
        echo date("w",date_timestamp_get($currentDay))."\r\n";
        yield $currentDay;
        $currentDay = date_add($currentDay,date_interval_create_from_date_string("1 days"));
    }
}

function getFirstWeekDay($time=null){
    if(!$time){
        $time = date_create();
    }
    return  date_create(date("Y-m-d",strtotime("Monday -1 week",date_timestamp_get($time))));
}

function getLastWeekDay($time=null){
    if(!$time){
        $time = date_create();
    }
    return date_create(date("Y-m-d",strtotime("Sunday 0 week",date_timestamp_get($time))));
}

/**
 * @return \Generator
 * 获取当前月的第一天所在的星期一，和最后一天所在的星期日
 */
function getCurrentCalendar(){
    foreach(array("星期一","星期二","星期三","星期四","星期五","星期六","星期日") as $item){
        yield $item;
    }
    $startDay = getFirstWeekDay(getFirstDayOfMonth());
    $endDay = getLastWeekDay(getLastDayOfMonth());
    $currentDay = $startDay;
    while($currentDay){
        if(date_timestamp_get($endDay)-date_timestamp_get($currentDay)<0){
            break;
        }
        yield $currentDay;
        $currentDay = date_add($currentDay,date_interval_create_from_date_string("1 days"));
    }
}
function start(){
//    var_dump();
//    var_dump(getLastWeekDay(getLastDayOfMonth()));
    $days = getCurrentCalendar();
    foreach ($days as $day) {
        echo date("Y-m-d",date_timestamp_get($day))."\r\n";
    }
}

function getWeekeDay($time){
    $week = date("w",$time);
    $weeks = array("星期日","星期一","星期二","星期三","星期四","星期五","星期六",);
    return $weeks[$week];
}
//start();