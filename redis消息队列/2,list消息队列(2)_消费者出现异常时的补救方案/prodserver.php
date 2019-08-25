<?php
require("core/functions.php");
require("core/MyException.php");

if($argc!=2) //譬如php abc.php  那么argc就是1（自己算一个参数)   php abc.php aaa ,那么argc就是2    用argv[1] 可以取到 参数值
    exit("please set server id~~~");
$myid=$argv[1];//这是启动时自己设置的 唯一id

$client=$redis->client();

function start(redis $redis_client)
{
    global $myid;
    //先处理 上次没有处理的 key
    echo "find baklist".PHP_EOL;
    while(true) // 应该使用swoole 等 开一个异步任务或者进程，专门对自己的 备份list做长期监听
    {
        $bak_res = $redis_client->brPop(["orders".$myid],1); //从自己的备份队列中获取 ,过期时间设置短一些
        if($bak_res && $bak_res[0])
        {
            doJob($bak_res[1],$redis_client,true);
            usleep(500*1000); //休眠500毫秒
        }
        else //如果没取到值 说明 备份队列中 木有 了，则跳出循环
            break;
    }
    echo "baklist done".PHP_EOL;
    echo "begin orders ".PHP_EOL;
    //接下来开始继续
    while(true) {
        $res = $redis_client->brpoplpush("orders","orders".$myid,10); //注意，这个函数直接返回的是值
        if($res)
        {
            doJob($res,$redis_client);
            usleep(500*1000); //休眠500毫秒

        }
        else
            continue;


    }

}
function doJob($orderNo,redis $redis_client,$isBak=false) //isBak决定了 处理过程是否是 备份队列处理 ，如果是 有些步骤不需要执行
{
    global $myid;
    try{
        if($orderNo=="pn023") throw new MyException("error");
        sleep(3); //假装 干的很耗时，很辛苦
        if($isBak) //如果是 备份队列处理，显示不一样的字符，仅此而已
            echo "backjob order_no=";
        else
        echo "order_no=";

        echo $orderNo." done".PHP_EOL;
        if(!$isBak)
            $redis_client->lPop("orders".$myid);   //注意这里，处理完后，要删掉 备份列表左边的第一个元素
    }
    catch (MyException $myException)
    {
        if(!$isBak){
            //这里要判断 是什么类型的exception ，譬如超时等才能塞回去,这个机制是自己定的
            $redis_client->rPush("orders",$orderNo);//塞回原队列
            $redis_client->lPop("orders".$myid);
            echo "push back ".PHP_EOL;
            sleep(3);//休眠3秒，让其他 死循环程序来获取
        }

    }
    catch (Exception $ex)
    {

        echo "err".$ex->getMessage().PHP_EOL;



    }

}
start($client);
