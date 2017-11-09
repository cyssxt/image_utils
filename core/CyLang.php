<?php
/**
 * Created by IntelliJ IDEA.
 * User: 520Cloud
 * Date: 2017/11/7
 * Time: 15:44
 */

class CyLang{
    private $param_reg='/\{\{([^#{}]+)\}\}/';
    private $fun_reg='/##(.*\))##/';
//    private $param_reg='/(aaa)/';
    private $json = null;

    static function hex2rgb($hexColor) {
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
    /**
     * CyLang constructor.
     * @param null $json
     */
    public function __construct($json)
    {
        $this->json = $json;
    }
    function parseAll($value){
        preg_match_all($this->fun_reg,$value,$matches,PREG_OFFSET_CAPTURE);
        $matches_1 = $matches[1];
        $tmpValue = $value;
        foreach($matches_1 as $match){
            $v = $this->parse($match[0]);
            eval("\$v=".$v.";");
//            if(is_array($v)){
//                $v = CyLang::arrayToStr($v);
//            }
            $tmpValue  = str_replace("##".$match[0]."##",json_encode($v),$tmpValue);
        }
        $tmpValue = $this->parse($tmpValue);
        return $tmpValue;
    }

    function parse($value){
        preg_match_all($this->param_reg,$value,$matches,PREG_OFFSET_CAPTURE);
        $matches_1 = $matches[1];
        $tmpValue = $value;
        foreach ($matches_1 as $match){
            $v = $this->getFieldValue($match[0],$this->json);
            $tmpValue  = str_replace("{{".$match[0]."}}",$v,$tmpValue);
        }
        return $tmpValue;
    }

    function getValueName($match){
        return str_replace(".","_",$match);
    }

    function getFieldValue($field){
        $result = preg_split("/\./",$field);
        $data = $this->json;
        foreach ($result as $r){
            $data = $data[$r];
        }
        if(is_array($data)){
            return CyLang::ArrayToStr($data);
        }
        return $data;
    }

    static function arrayToStr($data){
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

}

function test(){
    $data = json_decode(file_get_contents("../data.json"),true);
    $cy = new CyLang($data);
    echo $cy->parse("{{data.type}}['{{data.key}}']");
}

//test();
//eval("\$tmpValue=array('key'=>'huo','icon'=>'binghuo.png','name'=>'丙火型','desc'=>'壬水具有像江河、 大海般豪放不拘的性质，最具有动态、好动的特性。像江海的水会不停到处流动，四处汇聚所有的河流，融入各式各样的东西，所以最能包容各种事物，“不执着”是其最大的特色，而且因为能包容所以学习能力也最强。',)['key'];");