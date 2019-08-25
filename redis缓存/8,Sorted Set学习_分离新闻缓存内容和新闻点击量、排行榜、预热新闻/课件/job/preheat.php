<?php

require("../Entities/functions.php");
require("../Entities/NewsInfo.php");

$cmd='init';
if($argc==2)
{
    $cmd=$argv[1];
}
if($cmd=="init")
{
    pre_init();
}

function pre_init()
{
    global $redis;
    $redis->pipeExec(function(Redis $redis_client){
        //先从db取出 点击量数据，为了演示就简单点了  select * from news order by views desc limit 0,3  假设就取 3条，
        //仅仅是为了演示 简单，不要纠结、不要纠结、不要纠结
        $newsPreData=getDataBySQL("select * from news order by views desc limit 0,3 ");
        foreach($newsPreData as $row)
        {
            $key="hnews".$row["news_id"]; //拼接key
            $redis_client->hMset($key,[
                "news_id"=>$row["news_id"],
                "news_title"=>$row["news_title"],
//                "views"=>$row["views"]
            ]);
            //这好比 zadd newsclick xx(点击量) news101
            //集合名 我们定位 newsclick 。代表新闻点击量集合。 因为万一后面 还有 新闻评论数、点赞数呢？
            $redis_client->zAdd("newsclick",$row["views"],"news".$row["news_id"]);
            $redis_client->expire($key,200);
        }
        $redis_client->exec();
        echo "done~~~";

    });
}


