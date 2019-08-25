<?php
require("core/functions.php");
$redis=new RedisObject();

 if($_POST)
 {
     $no=$_POST["orders_no"];
     if(trim($no)=="") exit("无效订单号");
     $order_no=str_pad($no,3,'0',STR_PAD_LEFT);//不满3位 补足3位 加0
     $redis->client()->lPush("orders","pn".$order_no);
     echo "下单成功".date("Y/m/d H:i:s");
 }

?>
<html>
<header>

</header>
<body>
 <div>
     <form method="post">
      输入订单号(数字即可,会自动加pn前缀):<input type="text" name="orders_no"/> <input type="submit" value="下单"/>
     </form>
 </div>

</body>
</html>
