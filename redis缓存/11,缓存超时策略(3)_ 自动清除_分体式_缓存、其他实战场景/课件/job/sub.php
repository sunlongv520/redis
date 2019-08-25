<?php
ini_set('default_socket_timeout', -1);  //不超时 ,否则监听一段时间(默认60秒) 自动退出了
require("../Entities/functions.php");


$channel="__keyevent@*__:expired"; //注意这里，频道不是news
$newCache_Time=20;//假设新闻缓存是20秒
$newsCache_key="newscache";//保存新闻时间戳的key
/*function callback($redis_cient, $chan, $msg){
    if($msg=="cc")//清除缓存
    {
        clearNewsCache();
    }
}*/
function callback($redis, $pattern, $chan, $msg){

    // $chan 的形式如__keyevent@0__:expired  、__keyevent@1__:expired
    //所以这里要用正则来进行匹配
    if(preg_match('/^__keyevent@(\d+)__:expired$/i',$chan,$matchs))
    {
        $db=$matchs[1];// 取出DB   不同的DB 需要进行select
        //$msg的形式 是 news101
        if(preg_match('/^news(\d+)$/i',$msg,$matchID))
        {
            $newsID=$matchID[1];
            clearNewsCacheByID($db,$newsID);
        }

    }
}

$redis->psubscribe($channel,"callback");

function clearNewsCacheByID($db=0,$newsID) //获取过期的 id们  ,这是前面课程的清楚缓存函数
{
    global   $redis ;// 这个是我们自己封装的Redis Object
    $redis->selectDB($db);//选择数据库 ，重要
        $redis->multiExec(function(Redis $redis_client) use($newsID){
                $redis_client->del("hnews".$newsID);//删除 hash 类型的新闻数据
                $redis_client->zRem("newsclick","news".$newsID);
        });

        echo "clear news cache,newsID=$newsID done~~~".PHP_EOL;

}







function clearNewsCache() //获取过期的 id们  ,这是前面课程的清楚缓存函数
{
    global $newsCache_key,$newCache_Time;
    global   $redis ;// 这个是我们自己封装的Redis Object

   $ids=$redis->client()->zRangeByScore($newsCache_key,0,time()-$newCache_Time); //找到过期的key
  //[101,102,103]
   if($ids && count($ids)>0)
   {
       $ids_str=implode(" ",$ids);
       echo "del ids:".$ids_str.PHP_EOL; //显示即将删除哪些数据

       $redis->multiExec(function(Redis $redis_client) use($ids){
           foreach($ids as $id)
           {
               $redis_client->del("hnews".$id);//删除 hash 类型的新闻数据
               $redis_client->zRem("newsclick","news".$id);
               $redis_client->zRem("newscache",$id);// 这句不需要了。由于使用了keyspace notifications
           }
        });

       echo "clear news cache done~~~".PHP_EOL;
   }
   else
       echo "no expired news".PHP_EOL;
}