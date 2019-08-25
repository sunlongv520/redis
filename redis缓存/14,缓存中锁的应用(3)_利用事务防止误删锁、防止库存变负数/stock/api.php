<?php

require("../Entities/functions.php");

$resObj=new stdClass();
header("Content-type:application/json");
if(!isset($_POST["id"])) exit("no");


$prod_id=intval($_POST["id"]);
$prodKey="prod".$prod_id; // 拼凑成 news101 这样的key

$redis->zSetOperator->setName="stock";//设置sorted list的key 名称

$myid=session_create_id();
while(!$redis->client()->set("lock",$myid,["NX","EX"=>2]))
{
    usleep(100000) ;//1 秒==1000毫秒 1毫秒=1000微妙
}
$redis->client()->watch("lock");

    $current_stock=$redis->zSetOperator->get($prodKey);
    if($current_stock<=0)
    {
        $resObj->msg="no stock";//代表没库存
        $resObj->result=$current_stock;

        $redis->client()->multi(Redis::MULTI)
            ->del("lock")
            ->exec();
    }
    else{
        if(isset($_GET["delay"]) && intval($_GET['delay'])==1)  //这里开始模拟卡顿了。 假设你判断后，正好进来了很多并发  或者你卡顿了
        {
            sleep(5); //模拟卡顿5秒
        }
        $ret=$redis->client()->multi(Redis::MULTI)
            ->zIncrBy("stock",-1,$prodKey)   //member 是prod101
            ->del("lock")
            ->exec();

         if($ret)
         {
             $resObj->msg="OK";//代表OK
             $resObj->result=$ret[0]; //这里返回减去1后的库存
         }
         else{
             $resObj->msg="canceled";//代表没有执行
             $resObj->result=$redis->zSetOperator->get($prodKey); //这里返回减去1后的库存
         }

    }




exit(json_encode($resObj));


