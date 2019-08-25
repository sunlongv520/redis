<?php
require("./../core/functions.php");
const xKey="newusers";
const groupName="sendmail";

$redis=$redis->client();
function initGroup()  { //初始化组
    global $redis;
    $groups=$redis->xInfo('GROUPS', "newusers");//这里得到一个数组
   /*1) 1) "name"
   2) "sendmail"
   3) "consumers"
   4) (integer) 0
   5) "pending"
   6) (integer)
    *
    */
    $existGroup=false;
    foreach($groups as $g){
        if($g[1]===groupName)
        {
            $existGroup=true;
            break;
        }
    }
    if(!$existGroup){
        $redis->xGroup('CREATE', xKey, groupName, "0"); //从0开始
        echo "init Group:sendmail".PHP_EOL;
    }
}
initGroup();
while(true){
    $getResult=$redis->xReadGroup(groupName,"c1",[xKey=>">"],1,2000); //返回的是一个数组

   //得到的数据  带key ,key就是stream的name
    if($getResult && isset($getResult[xKey])){
        $sendmail=$getResult[xKey];
        foreach ($sendmail as $id=>$fieldList){

            //这里假设有一些业务代码，处理发送邮件
            //执行ack
            $redis->xAck(xKey, groupName, [$id]);
            echo "sendmail with userid=".$fieldList["userid"].PHP_EOL;
        }
    }
    usleep(500*1000);//休眠 500毫秒
}