<?php
function lisStream(string $groupName,string $consumer,Redis $redis )
{

    while(true){
        $getResult=$redis->xReadGroup($groupName,$consumer,[xKey=>">"],1,2000); //返回的是一个数组
        //得到的数据  带key ,key就是stream的name
        if($getResult && isset($getResult[xKey])){
            $sendmail=$getResult[xKey];
            foreach ($sendmail as $id=>$fieldList){

                //这里假设有一些业务代码，处理发送邮件
                $redis->xAck(xKey, $groupName, [$id]);
                echo "Coroutine ID=".\Swoole\Coroutine::getuid().",sendmail userid=".$fieldList["userid"].PHP_EOL;
            }
        }
        usleep(500*1000);//休眠 500毫秒
    }

}