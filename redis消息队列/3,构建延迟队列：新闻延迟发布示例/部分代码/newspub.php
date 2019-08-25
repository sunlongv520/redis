<?php
ini_set('date.timezone','Asia/Shanghai'); //PHP内置服务器 需要设置时区
require("core/functions.php");

$db=new db();
if($_POST)
{
    $news_title=$_POST["news_title"];
    $news_pubtime=$_POST["news_pubtime"];


    $ispub=strtotime($news_pubtime)>time()?0:1 ;//如果发布时间超过当前时间则为0，否则视为已经发布

   $newsid=$db->saveToDB([
        "news_title"=>$news_title,
        "news_pubtime"=>$news_pubtime,
        "news_ispub"=>  $ispub
       ],"news");

    echo "新闻入库成功，新闻ID是:".$newsid;

     if(!$ispub)
     {
         //如果没有发布，则添加到 Sorted Set中
         $redis->client()->zAdd("newspub",strtotime($news_pubtime),$newsid);
     }

}
//接下来获取最新发布的10条新闻
$sql="select * from news where news_ispub=1 order by news_pubtime desc limit 0,10";
$newsset=$db->getDataBySql($sql);

?>
<html>
<head>
 <script src="js/jquery-1.8.1.min.js"></script>
    <link type="text/css" rel="stylesheet" href="js/skin/jedate.css">

    <script src="js/jdate.js" type="text/javascript"></script>
    <script>
        $(document).ready(function(){
            jeDate("#news_pubtime",{
                festival:true,
                minDate:"1900-01-01",              //最小日期
                maxDate:"2099-12-31",              //最大日期
                method:{
                    choose:function (params) {

                    }
                },
                format: "YYYY-MM-DD hh:mm:ss"
            });
        })
    </script>
</head>
<body>
<div>
    <div>
    <form method="post">
       <div>输入新闻标题<input type="text" name="news_title"/> </div>
        <div>发布时间:<input type="text" class="jeinput" id="news_pubtime" name="news_pubtime" placeholder="YYYY-MM-DD hh:mm:ss"/></div>
        <input type="submit" value="提交新闻"/>
    </form>
    </div>
    <hr/>
    <div>

        <div>
            <dl>
                <dt>已经发布的最新10条新闻</dt>
              <?php $i=1; foreach($newsset as $news):?>
                <dd> <?php echo $i?>、<?php echo $news["news_title"]?></dd>
              <?php endforeach;?>
            </dl>
        </div>
    </div>
</div>

</body>
</html>

