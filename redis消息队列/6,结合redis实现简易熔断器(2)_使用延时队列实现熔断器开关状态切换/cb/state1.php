<?php
require("../core/functions.php");

while(true)
{
    $getMembers=$redis->client()->zRangeByScore("circuit_open","-inf",time(),['limit' => [0, 10]]);

    if(count($getMembers)>0)
    {
        foreach ($getMembers as $member){
            $redis->client()->zAdd("circuit",-1,$member);
        }

        // 别忘了删掉， 否则 会循环获取
        $redis->client()->zRem("circuit_open",...$getMembers); //使用了参数解包
        echo "set ".count($getMembers)." circuit halfopen".PHP_EOL;
    }

    usleep(500*1000);//休眠500毫秒
}