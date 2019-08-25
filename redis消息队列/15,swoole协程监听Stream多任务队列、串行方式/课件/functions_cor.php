<?php
function initGroup(string $groupName,Redis $redis){
    $groups=$redis->xInfo('GROUPS', "newusers");//这里得到一个数组

    $existGroup=false;
    foreach($groups as $g){
        if($g[1]===$groupName)
        {
            $existGroup=true;
            break;
        }
    }
    if(!$existGroup){
        $redis->xGroup('CREATE', xKey, $groupName, "0"); //从0开始
        echo "init Group:$groupName".PHP_EOL;
    }
}
/*
 * $next代表下一个人是谁 $next="c2"
 */
function lisStream(string $groupName,string $consumer,Redis $redis,$start=">",$next="")
{
    while(true){
        $getResult=$redis->xReadGroup($groupName,$consumer,[xKey=>$start],1,2000); //返回的是一个数组
        //得到的数据  带key ,key就是stream的name
        if($getResult && isset($getResult[xKey])){
            $sendmail=$getResult[xKey];
            foreach ($sendmail as $id=>$fieldList){
                //这里假设有一些业务代码，xxxxooooo

                if( $next!=""){ //如果有“下一位",则改变 消费者，不执行ACK

                   $redis->rawCommand("XCLAIM", xKey,$groupName,$next,0 ,$id);

                    setLog("from $consumer xclaim to  $next ".PHP_EOL);
                }
                else{
                    $redis->xAck(xKey, $groupName, [$id]);
                    setLog("Coroutine ID=".\Swoole\Coroutine::getuid().",".$groupName." userid=".$fieldList["userid"].PHP_EOL);
                }

            }
        }
        usleep(500*1000);//休眠 500毫秒
    }

}

use Swoole\Coroutine as co;
function setLog($str){

    go(function()use($str){
        $r = co::fwrite(STDOUT, $str, 0);
    });

}
