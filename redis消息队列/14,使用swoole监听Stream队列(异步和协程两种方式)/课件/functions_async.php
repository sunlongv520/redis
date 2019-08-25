<?php
function KVArray(array $result){
    //把类似 [a,1,b,2] 这样的数组变成 [a=>1,b=>2] 这样的数组
    if(count($result)%2!=0) return false; //长度必须是整除2
    $chunk=array_chunk($result,2,true);//把数组每两个切割
    /*
     如[a,1,b,2] 会变成
     array(
         [0] => Array
          (
            [0] => a
            [1] => 1
          )

          [1] => Array
          (
            [0] => b
            [1] => 2
           )
    )
     */
    $ret=[];
    foreach($chunk as $arr){
        $ret[$arr[0]]=$arr[1];
    }
    return $ret;
}
function initGroup(string $groupName){
    global $redis;
    $redis->xinfo('GROUPS', "newusers",function(swoole_redis $redis, array $groups) use ($groupName){
        $existGroup=false;
        foreach($groups as $g){
            if($g[1]===$groupName)
            {
                $existGroup=true;
                break;
            }
        }
        if(!$existGroup){
            $redis->xGroup('CREATE', xKey, $groupName, "0",function (){
                echo "init Group:sendmail".PHP_EOL;
            });

        }
    });
}
function lisStream(string $groupName,string $consumer ){
    global $redis;
    $redis->xreadgroup("group",$groupName,$consumer,"count",1,"block",2000,"streams",xKey,">",function ($redis,$result) use($groupName,$consumer){
        if(!$result){
            usleep(500000);
            lisStream($groupName,$consumer);
            return;
        }
        //注意这里的改动，结果集和使用PHP  Redis 结构不同
       // var_dump($result);//大家可以在课后 看下内容
        //var_export($result[0][1][0]); //这里是所需要的array,包含两行【因为上面我只读取一条数据】，第一行是id,第二行是 具体的值(也是数组)
        /**
        array (
        0 => '1544346379386-0',
        1 =>
        array (
          0 => 'userid',
          1 => '66',
        ),
        )
         */
        $getResult=$result[0][1][0];
        $id=$getResult[0];
        $fieldList=KVArray($getResult[1]);
        if($result){
                //这里假设有一些业务代码，处理发送邮件
                $redis->xAck(xKey, $groupName, $id,function () use($groupName,$fieldList,$consumer){
                    echo "sendmail userid=".$fieldList["userid"].PHP_EOL;
                    usleep(500000);
                    lisStream($groupName,$consumer);
                });
            }
    });

}