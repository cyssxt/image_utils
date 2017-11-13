<?php
/**
 * Created by IntelliJ IDEA.
 * User: 520Cloud
 * Date: 2017/11/6
 * Time: 0:07
 */
define("DEFAULT_FONT_SIZE",16);
define("DETAULT_COLOR",null);
define("FONT_SIZE","fontSize");
define("CONTENT","content");
define("COLOR","color");
define("BG_COLOR","bgColor");
define("DEFAULT_BG_COLOR",null);
define("PADDING","padding");
define("COL_NUM","colNum");
define("HEIGHT","height");
define("TEXT_ALIGN","textAlign");
define("CENTER","center");
define("BORDER_TOP","borderTop");
define("imagecolorallocate","imagecolorallocate");
class ImageGenerator
{
    private $fontPath = "";
    private $desImage = null;
    private $width=null;
    private $height = null;
    private $bgColor = null;
    public $currentY = 0;
    private $selectParams = 0;
    private $scale =1;
    private $padding = 0;
    static public $test=2;
    public $bgHeight=0;
    private $defaultSize = 16;

    /**
     * ImageGenerator constructor.
     * @param int|null $width
     * @param int|null $height
     * @param array $bgColor
     * @param string $fontPath
     * @param int $scale
     * @internal param null $font
     */
    public function __construct($width=350, $height=350,$bgColor=Array(0,0,0),$fontPath="../sources/PingFang_Bold.ttf",$scale=1,$selectParams=null)
    {
        $this->fontPath = $fontPath;
        $this->width = $width;
        $this->height = $height;
        $this->scale=$scale;
        $this->selectParams =$selectParams;
        $bgImage = $this->getProperty($this->selectParams,"bgImage",null,null);
        $this->scale = $this->getProperty($this->selectParams,"scale",1,null);
        $this->desImage = imagecreatetruecolor($this->width*$this->scale, $this->height*$this->scale);
        $this->bgColor = $bgColor;
        $paramTmp = array_merge(Array($this->desImage), $bgColor);
        $bgColor = call_user_func_array("imagecolorallocate", $paramTmp);
        imagefill($this->desImage,0,0,$bgColor);
        if($bgImage){
            list($xxx,$this->bgHeight) = $this->drawImage($bgImage,0,0,0,0,$this->width,null,false,true);
        }
        $this->padding = \CommonUtils\getProperty($selectParams,PADDING,0);
        $this->currentY = $this->currentY+$this->padding;
    }
    function setFontPath($path){
        $this->fontPath = $path;
    }

    /**
     * 保存图片
     * @param $fileName
     * @internal param $fileName（图片路径）
     */
    function save($fileName){
        $marginBottom = $this->getProperty($this->selectParams,"marginBottom",50,null);
        $height = $this->currentY+$marginBottom;
        $autoHeight = $this->getProperty($this->selectParams,"autoHeight",false,null);
        $newImage = $this->desImage;
        if($autoHeight){
            $newImage = imagecreatetruecolor($this->width*$this->scale,$height);
            $realHeight = $this->bgHeight ? $this->bgHeight:$height;
            if($realHeight<$height){
                $realHeight = $height;
            }
            imagecopy($newImage,$this->desImage,0,0,0,0,$this->width*$this->scale,$realHeight);
            imagedestroy($this->desImage);
        }
        imagepng($newImage,$fileName);
        imagedestroy($newImage);
    }

    function drawImageByDetail($detail){
        $content=\CommonUtils\getProperty($detail,"content");
        $width=\CommonUtils\getProperty($detail,"width");
        $height=\CommonUtils\getProperty($detail,"height");
        $marginTop=\CommonUtils\getProperty($detail,"marginTop");
        $this->currentY = $this->currentY+$marginTop;
        $this->drawImage($content,0,$this->currentY,0,0,$width,$height,true);
    }
    function imagecreatefrom($picname){
        $info=getimagesize($picname);
        $ename=explode('/',$info['mime']);
        $ext=$ename[1];
        switch($ext){
            case "png":
                $image=imagecreatefrompng($picname);
                break;
            case "jpeg":

                $image=imagecreatefromjpeg($picname);
                break;
            case "jpg":

                $image=imagecreatefromjpeg($picname);
                break;
            case "gif":

                $image=imagecreatefromgif($picname);
                break;
        }
        return array($info[0],$info[1],$image);
    }

    function drawImage($src,$dx,$dy,$sw,$sy,$width,$height,$flag=true,$filter=false){
        $fullWidth = $width;
        $fullHeight = $height;
        if(is_string($src)){
            list($fullWidth,$fullHeight,$src) = $this->imagecreatefrom($src);
        }
        $desWidth=$width*$this->scale;
        if($height){
            $desHeight = $height*$this->scale;
        }else{
            $desHeight = $desWidth*$fullHeight/$fullWidth;
        }
        if($flag){
            $imageWidth = $this->width*$this->scale;
            $dx = ($imageWidth-$desWidth)/2;
        }
        imagecopyresampled($this->desImage,$src,$dx,$dy,$sw,$sy,$desWidth,$desHeight,$fullWidth,$fullHeight);
        if(!$filter) {
            $this->currentY = $dy + $desHeight;
        }
        return Array($desWidth,$desHeight);
    }

    function drawCircle($dx,$dy,$width,$height,$color){
        $params = array_merge(Array($this->desImage),$color);
        $color = call_user_func_array("imagecolorallocate",$params);
        imagefilledarc($this->desImage,$dx,$dy,$width,$height,0,360,$color,IMG_ARC_EDGED);
    }

    function drawTextByDetail($detail){
        $content = \CommonUtils\getProperty($detail,"content");
        $fontSize = \CommonUtils\getProperty($detail,"fontSize");
        $color = \CommonUtils\getProperty($detail,"color");
        $marginTop = \CommonUtils\getProperty($detail,"marginTop");
        $textWidth = \CommonUtils\getProperty($detail,"width",$this->width);
        $lineHeight = \CommonUtils\getProperty($detail,"lineHeight");
        $textAlign = \CommonUtils\getProperty($detail,TEXT_ALIGN,false);
        $verticalAlign = \CommonUtils\getProperty($detail,"vertialAlign",false);
        $marginLeft = \CommonUtils\getProperty($detail,"marginLeft",0)*$this->scale;
        $prefix = \CommonUtils\getProperty($detail,"prefix");
        $bgColor = \CommonUtils\getProperty($detail,"bgColor");
        $this->currentY = $this->currentY+$marginTop*$this->scale;
        $lineHeight = $lineHeight*$this->scale;
        $fontSize = $fontSize*$this->scale;
        $dx = $marginLeft;
        $prefixWidth=0;
        $center = $textAlign=="center";
        if($prefix){
            $prefixMarginLeft = \CommonUtils\getProperty($prefix,"marginLeft")*$this->scale;
            $prefixWidth = \CommonUtils\getProperty($prefix,"width")*$this->scale;
            $prefixHeight = \CommonUtils\getProperty($prefix,"height")*$this->scale;
            $prefixColor = \CommonUtils\getProperty($prefix,"color");
            if($textAlign=="center"){
                $dx = ($this->width-$textWidth)/2;
            }
            $dx = $dx+$prefixMarginLeft+$prefixWidth;
            $textWidth = $textWidth*$this->scale - $prefixWidth*2-$prefixMarginLeft;
            $center = false;
        }
        $dy = $this->currentY;
        if($bgColor){
            $this->drawRectByDetail($detail);
        }
        list($width,$height) = $this->drawText($content,$fontSize,$color,$dx,$dy,$textWidth,null,$lineHeight,$center,$bgColor?true:false);
        if($prefix){
            $dy = $dy+$height/2;
            $this->drawCircle($dx-$prefixMarginLeft-$prefixWidth,$dy,$prefixWidth,$prefixHeight,$prefixColor);
        }
    }

    function drawText($text,$fontSize,$color,$x,$y,$width=null,$fontPath=null,$lineHeight=null,$center=false,$filter=false,$pWidth=null,$angle=0){
        $fontPath = $fontPath?$fontPath:$this->fontPath;
        list($left_bottom_x,$left_bottom_y,$right_bottom_x,$right_bottom_y,$right_top_x,$right_top_y,$left_top_x,$left_top_y) = imagettfbbox($fontSize,$angle,$fontPath,$text);
        $textWidth = $right_bottom_x-$left_bottom_x;
        $width = $width?$width*$this->scale:$this->width*$this->scale;
        $textHeight = $right_bottom_y-$right_top_y;
        $tmpY = 0;
        if($center){
            if($textWidth>$width){
                $pWidth = $pWidth?$pWidth:$this->width;
                $x = $x + ($pWidth-$width)/2;
            }else{
                $x = $x+($width-$textWidth)/2;
            }
        }
        if($center&&$lineHeight>0){
            $textHeight = $right_bottom_y-$right_top_y;
            $tmpY = ($lineHeight-$textHeight)/2;
        }
        $y = $tmpY+$y+$textHeight;
        $this->drawTextAutoSpace($text,$fontSize,$color,$x,$y,$width,null,$lineHeight,$filter);
        return Array($textWidth,$textHeight);
    }

    function drawTextAutoSpace($text,$fontSize,$color,$x,$y,$width,$fontPath=null,$lineHeight=null,$filter=false,$angle=0){
        if(empty($text)){
            return;
        }
        $fontPath = $fontPath?$fontPath:$this->fontPath;
        $tmpStr="";
        $tmpWidth = 0;
        $lineHeight = $lineHeight?$lineHeight:1.5;
        $height = $lineHeight;
        $tempParams = array_merge(Array($this->desImage),$color);
        $fontColor = call_user_func_array("imagecolorallocate",$tempParams);
        $len = mb_strlen($text);
        for($i=0;$i<$len;$i++){
            $char = mb_substr($text, $i, 1);
            if(!empty($tmpStr)){
//                echo $tmpStr."====";
                list($tmp_left_bottom_x,$tmp_left_bottom_y,$tmp_right_bottom_x,$tmp_right_bottom_y,$tmp_right_top_x,$tmp_right_top_y) = imagettfbbox($fontSize, 0, $fontPath, $tmpStr);
                $tmpWidth = $tmp_right_bottom_x-$tmp_left_bottom_x;
                $height = $tmp_right_bottom_y-$tmp_right_top_y;
            }
            list($left_bottom_x,$left_bottom_y,$right_bottom_x) = imagettfbbox($fontSize, 0, $fontPath, $char);
            $charWidth = $right_bottom_x-$left_bottom_x;
            if($tmpWidth+$charWidth<$width||$i==$len-1){
                $tmpStr = $tmpStr.$char;
                if($i==$len-1){
                    if(!$filter){
                        $this->currentY = $y;//$lineHeight;
                    }
                    imagettftext($this->desImage,$fontSize,$angle,$x,$y,$fontColor,$fontPath,$tmpStr);
                }
            }else{
                imagettftext($this->desImage,$fontSize,$angle,$x,$y,$fontColor,$fontPath,$tmpStr);
                $subStr = mb_substr($text,$i);
                if(!$filter){
                    $this->currentY = $this->currentY+$lineHeight*$height;//+$lineHeight*$height;
                }
                $this->drawTextAutoSpace($subStr,$fontSize,$color,$x,$y+$height*$lineHeight,$width,$fontPath,$lineHeight);
                break;
            }
        }
    }

    function drawRect($x,$y,$width,$height,$color,$radius=0,$filter=false){
        $params = array_merge(Array($this->desImage),$color);
        $fun = "imagecolorallocate";
        if(count($params)==5){
            $fun = "imagecolorexactalpha";
        }
        $fill = call_user_func_array($fun,$params);
        ImageFilledRectangle($this->desImage,$x,$y+$radius,$width+$x,$height-$radius+$y,$fill);
        if($radius>0){
            ImageFilledRectangle($this->desImage,$x+$radius,$y,$x+$width-$radius,$y+$radius,$fill);
            imagefilledarc($this->desImage,$x+$radius,$y+$radius,$radius*2,$radius*2,180,270,$fill,IMG_ARC_PIE);
            imagefilledarc($this->desImage,$x+$width-$radius,$y+$radius,$radius*2,$radius*2,270,360,$fill,IMG_ARC_PIE);
            imagefilledarc($this->desImage,$x+$radius,$height+$y-$radius,$radius*2,$radius*2,90,180,$fill,IMG_ARC_PIE);
            ImageFilledRectangle($this->desImage,$x+$radius,$height+$y-$radius,$x+$width-$radius,$height+$y,$fill);
            imagefilledarc($this->desImage,$x+$width-$radius,$height+$y-$radius,$radius*2,$radius*2,0,90,$fill,IMG_ARC_PIE);
        }
        if(!$filter){
            $this->currentY = $y+$height;
        }
    }

    /**
     * @param $d 绘制柱状图
     */
    public function drawBar($d)
    {
        $contents = \CommonUtils\getProperty($d,"content");
        $barTotalHeight = \CommonUtils\getProperty($d,"barTotalHeight");
        $titleColor = \CommonUtils\getProperty($d,"titleColor");
        $barMarginTop = \CommonUtils\getProperty($d,"barMarginTop");
        $contentSize = \CommonUtils\getProperty($d,"contentSize",0)*$this->scale;
        $titleSize = \CommonUtils\getProperty($d,"titleSize",0)*$this->scale;
        $marginTop = \CommonUtils\getProperty($d,"marginTop",0)*$this->scale;
        $totalWidth = 0;
        $maxPercent = 0;
        foreach($contents as $content){
            $width = \CommonUtils\getProperty($content,"width");
            $marginLeft = \CommonUtils\getProperty($content,"marginLeft",0);
            $percent = \CommonUtils\getProperty($content,"percent");
//            $marginTop = \CommonUtils\getProperty($content,"marginTop");
            $totalWidth = $width*$this->scale+$totalWidth+$marginLeft*$this->scale;
            if($percent>$maxPercent){
                $maxPercent = $percent;
            }
        }
        $x = ($this->width-$totalWidth)/2;
        $y = $marginTop+$barMarginTop*$this->scale+$this->currentY;
        $nextX = $x;
        for ($i=0;$i<count($contents);$i++) {
            $content = $contents[$i];
            $percent = \CommonUtils\getProperty($content,"percent");
            $text = \CommonUtils\getProperty($content,"text");
            $title = \CommonUtils\getProperty($content,"title");
            $color = \CommonUtils\getProperty($content,"color");
            $width = \CommonUtils\getProperty($content,"width")*$this->scale;
            $marginLeft = \CommonUtils\getProperty($content,"marginLeft")*$this->scale;
            $marginBottom = \CommonUtils\getProperty($content,"marginBottom")*$this->scale;
            $contentMarginTop = \CommonUtils\getProperty($content,"marginTop")*$this->scale;
            $textAlign = CommonUtils\getProperty($content,"textAlign");
            $height = $barTotalHeight*$this->scale * $percent/$maxPercent;
//            $height = 50;
            $tmpY = $barTotalHeight-$height+$y;
            $nextX = $nextX + $marginLeft;
            $this->drawRect($nextX,$tmpY,$width,$height,$color,$width/2);
            $this->drawText($text,$contentSize,$color,$nextX,$tmpY-$contentMarginTop,$width,null,null,$textAlign=="center",true,$width);
            $this->drawText($title,$titleSize,$titleColor,$nextX,$tmpY+$height+$marginBottom,$width,null,null,$textAlign=="center",true,$width);
            $nextX = $nextX + $width ;
        }
        $this->currentY = $this->currentY+$marginTop+$barTotalHeight*$this->scale;
    }

    /**
     * @param $detail 列表
     */
    public function drawList($detail)
    {
        $contents = CommonUtils\getProperty($detail,CONTENT);
        $colNum = CommonUtils\getProperty($detail,COL_NUM);
        $marginTop = CommonUtils\getProperty($detail,"marginTop");
        $y = $this->currentY;
        $x = 0+$this->padding;
        $rectY = $y+$marginTop*$this->scale;
        $width = $colNum!=0?($this->width*$this->scale-$this->padding*$this->scale*2)/$colNum:0;
        $bgColor = $this->getProperty($detail,BG_COLOR,null,null);
        $preHeight=0;
        $paddingBottom = 0;
        if($bgColor){
            $height = $this->getProperty($detail,HEIGHT,null,null)*$this->scale;
            $fullWidth = $this->getProperty($detail,"width",null,null)*$this->scale;
            if(!$height && $colNum){
                $childHeight = $this->getProperty($detail,"childHeight",null,null)*$this->scale;
                $paddingBottom = $this->getProperty($detail,"paddingBottom",null,null)*$this->scale;
                $height  = ceil(count($contents)/$colNum)*$childHeight;
            }
            $this->drawRect($x-$this->padding,$y,$fullWidth,$height+$paddingBottom,$bgColor);
        }
        $textX = 0;
        for ($i=0;$i<count($contents);$i++) {
            $content = $contents[$i];
            $height = $this->getProperty($content,HEIGHT,null,$detail)*$this->scale;
            $width = $width==0?$this->getProperty($content,"width",null,null)*$this->scale:$width;
            $text = CommonUtils\getProperty($content,CONTENT);
            $fontSizes = $this->getProperty($content,FONT_SIZE,DEFAULT_FONT_SIZE,$detail);
            $colors = $this->getProperty($content,COLOR,DETAULT_COLOR,$detail);
            $bgColor = $this->getProperty($content,BG_COLOR,null,null);
            $textAlign = $this->getProperty($content,TEXT_ALIGN,null,$detail);
            $borderTop = $this->getProperty($content,BORDER_TOP,null,$detail);
            $radius = $this->getProperty($content,"radius",null,$detail);
            $marginLeft = $this->getProperty($content,"marginLeft",0,null);
            $texts = preg_split("/,/",$text);
            if($width==0){
                throw new ErrorException("show config a width content[".$text."]");
            }
            $tmpX = $colNum!=0?($x + ($i%$colNum)*$width):0;
            if(!$height){
                $height = $width;
            }
            if(!$preHeight){
                $preHeight = $height;
            }
            if($colNum!=0&&$i%$colNum==0&&$i!=0){
                $rectY = $rectY + $preHeight;
                $preHeight = $height;
            }
            $rectX = $tmpX;
            if($bgColor){
                $this->drawRect($rectX,$rectY,$width,$height,$bgColor,$radius,true);
            }
            for ($j=0;$j<count($texts);$j++){
                $text = $texts[$j];
                $font = is_array($fontSizes)?$fontSizes[$j]:$fontSizes;
                $font = $font*$this->scale;
                $color = count($colors)==2?$colors[$j]:$colors;
                list($left_bottom_x,$left_bottom_y,$right_bottom_x,$right_bottom_y,$right_top_x,$right_top_y,$left_top_x,$left_top_y) = imagettfbbox($font,0,$this->fontPath,$text);
                $textWidth = $right_bottom_x-$left_bottom_x;
                $textHeight = $right_bottom_y-$right_top_y;
                $tmpY =$rectY+($height-$textHeight)/2;
                if(count($texts)>1){
                    $tmpY =$tmpY-($j%2==0?1:-1)*$height/5;
                }
                if($textAlign && $textAlign=="center"){
                    $resultTextX = $tmpX + ($width-$textWidth)/2;
                }else{
                    $resultTextX = $textX+$marginLeft;
                }
                if($borderTop){
                    $params = array_merge(array($this->desImage),$borderTop);//$borderTop
                    $bgColor = call_user_func_array("imagecolorallocate",$params);
                    imagerectangle($this->desImage,$rectX-1,$rectY-1,$rectX+$width+2,$rectY,$bgColor);
                }
                list($textWidth) = $this->drawText($text,$font,$color,$resultTextX,$tmpY,$width,null,0,0,true);
//                $this->currentY = $tmpY;
            }
            if(!$textAlign || $textAlign!="center"){
                $textX = $resultTextX+$textWidth;
            }
        }
    }

    public function  getProperty($detail,$key,$default,$parent){
            $value = CommonUtils\getProperty($detail,$key);
            if(!$value && $parent){
                $value = CommonUtils\getProperty($parent,$key);
        }
        return $value?$value:$default;
    }

    /**
     * @param $d 画矩形
     */
    public function drawRectByDetail($d)
    {
        $color = $this->getProperty($d,BG_COLOR,null,null);
        $width = $this->getProperty($d,"width",null,null)*$this->scale;
        $height = $this->getProperty($d,"height",null,null)*$this->scale;
        $dx = $this->getProperty($d,"marginLeft",0,null)*$this->scale;
        $dy = $this->getProperty($d,"marginRight",0,null)*$this->scale;
        $this->drawRect($dx,$this->currentY+$dy,$width,$height,$color,true,false);
    }

}