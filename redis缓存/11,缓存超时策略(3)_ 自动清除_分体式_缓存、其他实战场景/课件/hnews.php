<?php
require("Entities/functions.php");
require("Entities/NewsInfo.php");


$newsid=getParameter("id");
if(!$newsid) die("错误的参数");
$newsKey_prefix="hnews";//新闻key的前缀
$newsClickSet="newsclick" ;//保存新闻 点击量集合的集合名称
$newsClickKey="news".$newsid;//保存新闻点击量 的成员名
$newsKey=$newsKey_prefix.$newsid;  //拼凑成 一个完整的新闻key

$newsCacheKey="newscache";//用来保存 新闻缓存时间戳的
$news=new NewsInfo();

//$getNews=$redis->HashOperator->get($newsKey);  //从redis取 ,注意此时 是hash类型
$getNews=$redis->StringOperator->getFromReids("news".$newsid);//使用 keyspace notifications 后。存入的是String 类型,key=news101
$redis->zSetOperator->setName=$newsClickSet; //设置本次取值 集合名称
if(!$getNews) //如果木有取到
{
    $getNews=getFromDB($newsid);//就从数据库取 ,由于为了演示简单，统一用的是select *  .所以下面要做处理
    $getNews=$getNews[0]; //注意这里。 各个框架或自己写的代码取值 格式不同，  要以['key'=>'value'] 这种形式的数组

    if(!$getNews) //数据库里也没取到
    {
        //这里加入  防穿透 代码，为了代码 演示 清晰，这里暂时不加了,假设 都能取到数据
    }
    $getViews=$getNews["views"];//保存数据库中的点击量
    unset($getNews["views"]) ;//去除点击量 字段，因为它不存入hash类型

    //注意这个 部分  要用事务来完成。现在先简单些
    $redis->StringOperator->setToRedis("news".$newsid,$newsid,5);//假设存200秒
    $redis->HashOperator->set($newsKey,$getNews);//塞入缓存,塞的是新闻数据
    $redis->zSetOperator->set($getViews,$newsClickKey);  //塞入缓存 ,塞得是 点击量
  //  $redis->zSetOperator->set(time(),$newsid,$newsCacheKey);  //也是插入时间戳 /这句话不需要了

}
else
{
    echo "from cache";
    $getNews=$redis->HashOperator->get($newsKey);
}

//假设上面OK， 则我们要增加新闻点击量
//$redis->HashOperator->incr($newsKey,"views");
//这里要使用 zSet来增加了  而不是Hash
$getNews["views"]=$redis->zSetOperator->incr($newsClickKey);  //由于增加后 会返回 加完后的值，因此 赋值给$getNews作为结果
echo "-------------------------------------以下是内容-------- </br>";
echo json_encode($getNews);