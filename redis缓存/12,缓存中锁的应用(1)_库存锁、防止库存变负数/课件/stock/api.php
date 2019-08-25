<?php

require("../Entities/functions.php");

$resObj=new stdClass();
header("Content-type:application/json");
if(!isset($_POST["id"])) exit("no");


$prod_id=intval($_POST["id"]);
$prodKey="prod".$prod_id; // 拼凑成 news101 这样的key

$redis->zSetOperator->setName="stock";//设置sorted list的key 名称


while($redis->client()->setnx("lock",1))
{
    $current_stock=$redis->zSetOperator->get($prodKey);
    if($current_stock<=0)
    {
        $resObj->msg="no stock";//代表没库存
        $resObj->result=$current_stock;
    }
    else{
        if(isset($_GET["delay"]) && intval($_GET['delay'])==1)  //这里开始模拟卡顿了。 假设你判断后，正好进来了很多并发  或者你卡顿了
        {
            sleep(2); //模拟卡顿2秒
        }
        $ret=$redis->zSetOperator->incr($prodKey,-1);
        $resObj->msg="OK";//代表没库存
        $resObj->result=$ret; //这里返回减去1后的库存
    }

    $redis->client()->del("lock");//释放锁
    break;
}





exit(json_encode($resObj));


