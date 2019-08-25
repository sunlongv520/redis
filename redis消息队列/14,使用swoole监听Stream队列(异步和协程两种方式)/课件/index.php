<?php
const xKey="newusers";
require("functions_cor.php");
//require("functions_async.php");
//$redis=new swoole_redis();
//$redis->connect("192.168.222.139",6379,function(swoole_redis $redis, bool $result){
//    if(!$result){
//        echo "连接失败";
//    }
//    else{
//        initGroup("sendmail");
//        lisStream("sendmail","c1");//c1消费者
//    }
//});
go(function(){
   $redis=new Redis();
   $redis->connect("192.168.222.139",6379);
    lisStream("sendmail","c1",$redis);
});



