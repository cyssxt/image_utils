<?php
/**
 * Created by IntelliJ IDEA.
 * User: 520Cloud
 * Date: 2017/11/6
 * Time: 0:07
 */

class ImageUtils
{
    private $fontPath = "";
    private $font = null;
    private $desImage = null;
    private $width=null;
    private $height = null;
    private $bgColor = null;
    public $currentY = 0;
    private $scale =1;


    static function getProperty($detail,$key,$default=null){
        return isset($detail[$key])?$detail[$key]:$default;
    }

    /**
     * ImageUtils constructor.
     * @param int|null $width
     * @param int|null $height
     * @param array $bgColor
     * @param string $fontPath
     * @param int $scale
     * @internal param null $font
     */
    public function __construct($width=350, $height=350,$bgColor=Array(0,0,0),$fontPath="../sources/font/PingFang_Bold.ttf",$scale=1)
    {
        $this->fontPath = $fontPath;
        $this->width = $width;
        $this->height = $height;
        $this->desImage = imagecreatetruecolor($this->width, $this->height);
        $this->bgColor = $bgColor;
        $this->scale=$scale;
        $paramTmp = array_merge(Array($this->desImage), $bgColor);
        $bgColor = call_user_func_array("imagecolorallocate", $paramTmp);
        imagefill($this->desImage,0,0,$bgColor);
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
        $height = $this->currentY+50;
        $newImage = imagecreatetruecolor($this->width,$height);
        imagecopy($newImage,$this->desImage,0,0,0,0,$this->width,$height);
        imagepng($newImage,$fileName);
        imagedestroy($this->desImage);
        imagedestroy($newImage);
    }

    function drawImageByDetail($detail){
        $content=ImageUtils::getProperty($detail,"content");
        $width=ImageUtils::getProperty($detail,"width");
        $height=ImageUtils::getProperty($detail,"height");
        $marginTop=ImageUtils::getProperty($detail,"marginTop");
        $this->currentY = $this->currentY+$marginTop;
        $this->drawImage($content,0,$this->currentY,0,0,$width,$height,true);
    }



    function drawImage($src,$dx,$dy,$sw,$sy,$width,$height,$flag=true,$pdc=100){
        $fullWidth = $width;
        $fullHeight = $height;
        if(is_string($src)){
            list($fullWidth,$fullHeight)=getimagesize($src);
            $src=imagecreatefrompng($src);
        }
        $desWidth=$width*$this->scale;
        if($height){
            $desHeight = $height*$this->scale;
        }else{
            $desHeight = $desWidth*$fullHeight/$fullWidth;
        }
        if($flag){
            $imageWidth = $this->width;
            $dx = ($imageWidth-$desWidth)/2;
        }
        imagecopyresampled($this->desImage,$src,$dx,$dy,$sw,$sy,$desWidth,$desHeight,$fullWidth,$fullHeight);
        $this->currentY =$dy+$desHeight;
        return Array($desWidth,$desHeight);
    }

    function drawCircle($dx,$dy,$width,$height,$color){
        $params = array_merge(Array($this->desImage),$color);
        $color = call_user_func_array("imagecolorallocate",$params);
        imagefilledarc($this->desImage,$dx,$dy,$width,$height,0,360,$color,IMG_ARC_EDGED);
    }

    function drawTextByDetail($detail){
        $content = ImageUtils::getProperty($detail,"content");
        $fontSize = ImageUtils::getProperty($detail,"fontSize");
        $color = ImageUtils::getProperty($detail,"color");
        $marginTop = ImageUtils::getProperty($detail,"marginTop");
        $textWidth = ImageUtils::getProperty($detail,"width");
        $lineHeight = ImageUtils::getProperty($detail,"lineHeight");
        $marginLeft = ImageUtils::getProperty($detail,"marginLeft",0)*$this->scale;
        $prefix = ImageUtils::getProperty($detail,"prefix");
        $this->currentY = $this->currentY+$marginTop*$this->scale;
        $lineHeight = $lineHeight*$this->scale;
        $fontSize = $fontSize*$this->scale;
        $dx = 0;
        $prefixWidth=0;
        if($prefix){
            $prefixMarginLeft = ImageUtils::getProperty($prefix,"marginLeft")*$this->scale;
            $prefixWidth = ImageUtils::getProperty($prefix,"width")*$this->scale;
            $prefixHeight = ImageUtils::getProperty($prefix,"height")*$this->scale;
            $prefixColor = ImageUtils::getProperty($prefix,"color");
            $dx = $prefixMarginLeft+$prefixWidth/2;
        }
        list($width,$height) = $this->drawText($content,$fontSize,$color,null,$dx==0?0:$dx+$marginLeft+$prefixWidth/2,$this->currentY,$textWidth,null,$lineHeight);
        if($prefix){
            $this->drawCircle($dx,$this->currentY-$height/2,$prefixWidth,$prefixHeight,$prefixColor);
        }
    }

    function drawText($text,$fontSize,$color,$bgcolor,$x,$y,$width=null,$fontPath=null,$lineHeight=null,$angle=0,$filter=false,$pWidth=null){
        $fontPath = $fontPath?$fontPath:$this->fontPath;
        //设置背景颜色
        if($bgcolor){
            $bgParams = array_merge(Array($this->desImage),$bgcolor);
            $bgColor = call_user_func_array("imagecolorallocate",$bgParams);
            imagefilledrectangle($this->desImage,0,0,0,0,$bgColor);
        }
        list($left_bottom_x,$left_bottom_y,$right_bottom_x,$right_bottom_y,$right_top_x,$right_top_y,$left_top_x,$left_top_y) = imagettfbbox($fontSize,$angle,$fontPath,$text);
        if(!$width){
            $width = $right_bottom_x-$left_bottom_x;
        }else{
            $width = $width*2;
        }
        $height = $right_bottom_y-$right_top_y;
        if(!$x){
            $x = ($this->width-$width)/2;
        }else{
            if($pWidth){
                $x = $x+($pWidth-$width)/2;
            }
        }
        if(!$filter){
            $this->currentY = $y+$height;
        }
//        imagettftext($this->desImage,$fontSize,$angle,$x,$y+$height,$fontColor,$fontPath?$fontPath:$this->fontPath,$text);
        $this->drawTextAutoSpace($text,$fontSize,$color,$x,$y+$height,$width,null,$lineHeight);
        return Array($width,$height);
    }

    function drawTextAutoSpace($text,$fontSize,$color,$x,$y,$width,$fontPath=null,$lineHeight=null,$angle=0){
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
                    $this->currentY = $this->currentY+$lineHeight;
                    imagettftext($this->desImage,$fontSize,$angle,$x,$y,$fontColor,$fontPath,$tmpStr);
                }
            }else{
                imagettftext($this->desImage,$fontSize,$angle,$x,$y,$fontColor,$fontPath,$tmpStr);
                $subStr = mb_substr($text,$i+1);
                $this->currentY = $this->currentY+$lineHeight*$height;
                $this->drawTextAutoSpace($subStr,$fontSize,$color,$x,$y+$height*$lineHeight,$width,$fontPath,$lineHeight);
                break;
            }
        }
    }

    function drawRect($x,$y,$width,$height,$color,$radius=0){
        $radius=$radius!=0?$radius:$width/2;
        $params = array_merge(Array($this->desImage),$color);
        $fill = call_user_func_array("imagecolorallocate",$params);
        imagefilledarc($this->desImage,$x+$radius,$y+$radius,$radius*2,$radius*2,180,270,$fill,IMG_ARC_EDGED);
        if($radius==$width/2)
            ImageFilledRectangle($this->desImage,$x+$radius,$y,$x+$width-$radius,$y+$radius,$fill);
        imagefilledarc($this->desImage,$x+$width-$radius,$y+$radius,$radius*2,$radius*2,270,360,$fill,IMG_ARC_EDGED);
        ImageFilledRectangle($this->desImage,$x,$y+$radius,$width+$x,$height-$radius+$y,$fill);
        imagefilledarc($this->desImage,$x+$radius,$height+$y-$radius,$radius*2,$radius*2,90,180,$fill,IMG_ARC_EDGED);
        if($radius==$width/2)
            ImageFilledRectangle($this->desImage,$x+$radius,$height+$y-$radius,$x+$width-$radius,$height+$y,$fill);
        imagefilledarc($this->desImage,$x+$width-$radius,$height+$y-$radius,$radius*2,$radius*2,0,90,$fill,IMG_ARC_EDGED);
    }

    /**
     * @param $d 绘制柱状图
     */
    public function drawBar($d)
    {
        $contents = ImageUtils::getProperty($d,"content");
        $barTotalHeight = ImageUtils::getProperty($d,"barTotalHeight");
        $titleColor = ImageUtils::getProperty($d,"titleColor");
        $barMarginTop = ImageUtils::getProperty($d,"barMarginTop");
        $contentSize = ImageUtils::getProperty($d,"contentSize",0)*$this->scale;
        $titleSize = ImageUtils::getProperty($d,"titleSize",0)*$this->scale;
        $marginTop = ImageUtils::getProperty($d,"marginTop",0)*$this->scale;
        $totalWidth = 0;
        $maxPercent = 0;
        foreach($contents as $content){
            $width = ImageUtils::getProperty($content,"width");
            $marginLeft = ImageUtils::getProperty($content,"marginLeft",0);
            $percent = ImageUtils::getProperty($content,"percent");
//            $marginTop = ImageUtils::getProperty($content,"marginTop");
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
            $percent = ImageUtils::getProperty($content,"percent");
            $text = ImageUtils::getProperty($content,"text");
            $title = ImageUtils::getProperty($content,"title");
            $color = ImageUtils::getProperty($content,"color");
            $width = ImageUtils::getProperty($content,"width")*$this->scale;
            $marginLeft = ImageUtils::getProperty($content,"marginLeft")*$this->scale;
            $marginBottom = ImageUtils::getProperty($content,"marginBottom")*$this->scale;
            $contentMarginTop = ImageUtils::getProperty($content,"marginTop")*$this->scale;
            $height = $barTotalHeight*$this->scale * $percent/$maxPercent;
//            $height = 50;
            $tmpY = $barTotalHeight-$height+$y;
            $nextX = $nextX + $marginLeft;
            $this->drawRect($nextX,$tmpY,$width,$height,$color);
            $this->drawText($text,$contentSize,$color,null,$nextX,$tmpY-$contentMarginTop,null,null,null,null,true,$width);
            $this->drawText($title,$contentSize,$color,null,$nextX,$tmpY+$height+$marginBottom,null,null,null,null,true,$width);
            $nextX = $nextX + $width ;
        }
        $this->currentY = $this->currentY+$marginTop+$barTotalHeight*$this->scale;
    }
}
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