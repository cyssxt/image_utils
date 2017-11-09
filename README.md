# image_generator
generator image by json format data 


##CyLang用法##

<pre>
<code>
$cy = new CyLang($json);
//$json为Array,通过长可以通过json_decode($aa,true)生成
$result = $cy->parseAll($detail);
</code>
</pre>

我们们可以通过此方式，解析json中存在变量的文件
<pre>
<code>
{{aaa}}//a表示变量
{{a.b}}
//假定我们有一个Array,其对应的json为:
{
   "a":{"b":"123"} 
}
echo $cy->parseAll("{{a.b}}");
//123
</code>
</pre>

当然我们可以使用某些内部函数,如：
<pre>
<code>
$cy = new CyLang(Array("a"=>Array("bb"=>"cccccc"),"b"=>"c"));
//需要加:号的原因，主要是因为此工具主要json中的变量使用，在##包围的变量中，代码会将其作为eval去解析
//{"a":##strlen('{{a.bb}}')##}
echo $cy->parseAll("##strlen('{{a.bb}}')##");
//6

##CyLang::hex2rgb('#000000')##
此为CyLang内置函数，将#000000,转化为Array(0,0,0)
</code>
</pre>


## image generatory 使用 ##
首先生成配置json文件如下
<pre>
<code>
[
  {
    "type":"image",
    "content":"{{data.type.icon}}",
    "width":32.2,
    "marginTop":49.6,
  },
  {
    "type":"text",
    "content":"aaa",
    "width":32.2,
    "marginTop":49.6;//上边距
    "marginLeft":14,//左边距
    "fontSize":14,//字号大小
    "color":##CyLang::hex2rgb({{config.colors}}['{{data.type.key}}'])##
  }
]
</code>
</pre>
初始化imageUtils
<pre><code>
$image = new ImageUtils(375*SCALE,10000,Array(255,255,255),"./sources/font/PingFang_Bold.ttf",2);
//2为缩放倍数
//Array(255,255,255)为背景色
//"./sources/font/PingFang_Bold.ttf"为字体文件
</code></pre>
