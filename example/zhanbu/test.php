<?php
/**
 * Created by IntelliJ IDEA.
 * User: 520Cloud
 * Date: 2017/11/9
 * Time: 14:18
 */


include("../../core/Enter.php");
/**
 * create image by json_file
 */
Enter::createImageByDefault(350,
    5000,
    dirname(__FILE__)."/./sources/detail.json",
    array(dirname(__FILE__)."/./sources/config.json",dirname(__FILE__)."/./sources/data.json"),
    dirname(__FILE__)."/./result.png",array("scale"=>2));
//
Enter::createImageByDefault(350,
    5000,
    dirname(__FILE__)."/./sources/detail.json",
    array(dirname(__FILE__)."/./sources/config.json",dirname(__FILE__)."/./sources/data.json"),
    dirname(__FILE__)."/./result1.png",array("scale"=>1));
/**
 * create image by json_file
 */
Enter::createImageByJson(350, 5000,"./json.png", '{"content":[{"type":"text","content":"{{data.content}}","color":"##array(0,0,0)##","marginTop":10,"fontSize":20}]}', '{"data":{"type":"text","content":"aaaaa"}}');