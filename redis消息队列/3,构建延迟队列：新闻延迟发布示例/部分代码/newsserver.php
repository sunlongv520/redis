<?php
require("core/functions.php");

while(true)
{
  $getNewsID=$redis->client()->zRangeByScore("newspub","-inf",time(),['limit' => [0, 10]]);
  $ids="";
  foreach ($getNewsID as $id){
      if($ids!="")
          $ids.=",";
      $ids.=$id;
  }
  //以上 拼凑了一堆ID值
    if($ids!=""){
        $sql="update news set news_ispub=1 where news_id in (".$ids.")";
        echo $sql;
        $db=new db();
        $ret=$db->execSql($sql);

        // 别忘了删掉， 否则 会循环获取
        $redis->client()->zRem("newspub",...$getNewsID); //使用了参数解包
        echo "set ".$ret." news published".PHP_EOL;

    }

  usleep(500*1000);//休眠500毫秒
}