<?php
require("../core/functions.php");


$redis=new RedisObject();

 if($_POST)
 {
     $db=new db();

     $order_id=$db->saveToDB(["order_time"=>date('Y-m-d h:i:s')],"orders"); //获取订单号

     $order_no=date("Ymd").$order_id; //订单号生成机制 请自行决定，本课时仅仅为了演示
     $db->execSql("update orders set order_no='".$order_no."' where order_id=$order_id");

     //以上过程假设 都成功了,往redis zset里面插值 ,key是orders score是time()+20秒，这里为了演示快捷，改成了20秒,member是订单号
     $redis->client()->zAdd("orders",time()+20,$order_no);
     echo "下单成功,订单号是".$order_no;
 }

?>
<html>
<header>

</header>
<body>
 <div>
     <form method="post">
        <input type="submit" name="inputOrder" value="下单"/>
     </form>
 </div>

</body>
</html>
