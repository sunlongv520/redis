<?php
require("core/functions.php");

$client=$redis->client();

function start(redis $redis_client)
{
    while(true) {
        $res = $redis_client->brPop(["orders"],10);
        if($res && $res[0])
        {
            if($res[1]==="pn002") sleep(5);
            echo "order_no=".$res[1]." done".PHP_EOL;
            usleep(500*1000); //休眠500毫秒
            echo "restart ".PHP_EOL;
        }
        else
            continue;


    }

}
start($client);
