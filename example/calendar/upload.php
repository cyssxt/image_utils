<?php
/**
 * Created by IntelliJ IDEA.
 * User: 520Cloud
 * Date: 2017/11/11
 * Time: 23:01
 */
if ((($_FILES["file"]["type"] == "image/gif")
        || ($_FILES["file"]["type"] == "image/jpeg")
        || ($_FILES["file"]["type"] == "image/pjpeg"))
    && ($_FILES["file"]["size"] < 5000000))
{
    if ($_FILES["file"]["error"] > 0)
    {
        echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
    }
    else
    {
//        echo "Upload: " . $_FILES["file"]["name"] . "<br />";
//        echo "Type: " . $_FILES["file"]["type"] . "<br />";
//        echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
//        echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br />";
        move_uploaded_file($_FILES["file"]["tmp_name"],
            "/cloud/php_doc/upload/" . $_FILES["file"]["name"]);
        $url = "/cloud/php_doc/upload/".$_FILES["file"]["name"];
        $resultImage = "/cloud/php_doc/upload/result_".$_FILES["file"]["name"];
        include  "./main.php";
//        echo "/upload".$_FILES["file"]["name"];
        echo "<html>

    <script>
    window.location.href='/upload/result_".$_FILES["file"]["name"]."';
    
</script>
</html>";
//        if (file_exists("upload/" . $_FILES["file"]["name"]))
//        {
//            echo $_FILES["file"]["name"] . " already exists. ";
//        }
//        else
//        {
//            move_uploaded_file($_FILES["file"]["tmp_name"],
//                "/cloud/php_doc/upload/" . $_FILES["file"]["name"]);
//
//        }
    }
}
else
{
    echo "Invalid file";
    echo $_FILES["file"]["type"]."\r\n";
    echo $_FILES["file"]["size"]."\r\n";
}
?>