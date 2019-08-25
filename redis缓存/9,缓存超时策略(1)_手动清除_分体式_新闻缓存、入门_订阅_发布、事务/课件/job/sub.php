<?php
ini_set('default_socket_timeout', -1);  //不超时 ,否则监听一段时间(默认60秒) 自动退出了
require("../Entities/functions.php");


$channel="news";
$newCache_Time=20;//假设新闻缓存是20秒
$newsCache_key="newscache";//保存新闻时间戳的key
function callback($redis_cient, $chan, $msg){
    if($msg=="cc")//清除缓存
    {
        clearNewsCache();
    }
}
$redis->subscribe($channel,"callback");
function clearNewsCache() //获取过期的 id们
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
               $redis_client->zRem("newscache",$id);
           }
        });

       echo "clear news cache done~~~".PHP_EOL;
   }
   else
       echo "no expired news".PHP_EOL;
}