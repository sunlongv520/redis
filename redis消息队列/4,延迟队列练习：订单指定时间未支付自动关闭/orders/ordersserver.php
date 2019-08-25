<?php
require("../core/functions.php");

while(true)
{
    //这里为了演示 快捷，改成了20秒
  $getOrderNoList=$redis->client()->zRangeByScore("orders","-inf",time(),['limit' => [0, 10]]);
  $ids="";
  foreach ($getOrderNoList as $id){
      if($ids!="")
          $ids.=",";
      $ids.="'".$id."'";
  }
  //以上 拼凑了一堆ID值
    if($ids!=""){
        $sql="update orders set order_state=2 where order_no in (".$ids.") and order_state=1";
        echo $sql;
        $db=new db();
        $ret=$db->execSql($sql);

        // 别忘了删掉， 否则 会循环获取
        $redis->client()->zRem("orders",...$getOrderNoList); //使用了参数解包
        echo "set ".$ret." orders cancelled".PHP_EOL;

    }

  usleep(500*1000);//休眠500毫秒
}