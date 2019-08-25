<?php
require("./../core/functions.php");
const xKey="newusers";
const groupName="sendmail";

$start="0";
while(true){

    $getResult=$redis->client()->xReadGroup(groupName,"c1",[xKey=>$start],2,2000);
    if($getResult && isset($getResult[xKey])){
        $sendmail=$getResult[xKey];
        if(count($sendmail)>0) //确保有数据，开始循环，并把当前$start设置为最后一个ID
        {
            foreach ($sendmail as $id=>$fieldList){
                //再次发送邮件,有一定几率依然会失败
                if(rand(1,100)%2==0)  {
                    $redis->client()->xAck(xKey, groupName, [$id]);
                    echo "sendmail with userid=".$fieldList["userid"].PHP_EOL;
                }
                else{
                    //表示发生了异常 ,【此时不能ACK】
                    echo "error(resend) with userid=".$fieldList["userid"].PHP_EOL;  //报错
                }
            }
            //数组是 类似 array ( '1541947498188-0' => array ( 'userid' => '25', ) 这种形式
            $keys= array_keys($sendmail);
            $start= end($keys); //  用来获取key
            echo "last-id is:".$start.PHP_EOL;
        }
        else
        {
            $start=0;//重新归零，继续重试
            echo "reset ".PHP_EOL;
        }


    }
    sleep(3);//休眠3秒
}

