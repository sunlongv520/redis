<?php
const xKey="newusers";
require("functions_cor.php");

//初始化组
$initRedis=new Redis();
$initRedis->connect("192.168.222.139",6379);
initGroup("sendmail",$initRedis);
initGroup("sendscore",$initRedis);
$initRedis->close();// 上面两个执行完成后 连接关闭

Swoole\Runtime::enableCoroutine();
go(function(){ //发送邮件
   $redis=new Redis();
   $redis->connect("192.168.222.139",6379);
    lisStream("sendmail","c1",$redis,">","c2");
});

go(function(){ //发送积分
    $redis=new Redis();
    $redis->connect("192.168.222.139",6379);
    lisStream("sendmail","c2",$redis,"0","c3");
});

go(function(){ //第三步过程
    $redis=new Redis();
    $redis->connect("192.168.222.139",6379);
    lisStream("sendmail","c3",$redis,"0","");
});






