<?php
ini_set("display_errors","On");
error_reporting(E_ALL);
const xKey="newusers";
require("./../core/functions.php");


$from=isset($_GET["c"])?trim($_GET["c"]):"c1";
$plist=$redis->client()->xPending(xKey,"sendmail",0,"+",10,$from);
$detail=[];
$queryID="";
if(isset($_GET["id"]))
{
    $queryID=$_GET["id"];
    $detail=$redis->client()->xRange(xKey,$queryID,$queryID);

}
//执行删除
if(isset($_POST["cmdclaim"])){
    $dataID=$_POST["dataID"];//获取表单中的消息ID
    $idleTime=$_POST["idleTime"];//闲置时间
    $consumer=$_POST["consumer"];//转发的消费者
    if(trim($consumer)=="") exit("消费者不能为空");

//    $redis->client()->xClaim(xKey, 'sendmail',$consumer, [$dataID],
//        [
//            'IDLE' => $idleTime,
//            'RETRYCOUNT' => 5,
//            'FORCE',
//            'JUSTID'
//        ]
//    );


    $ret= $redis->client()->rawCommand("XCLAIM", "newusers","sendmail" ,$consumer,$idleTime ,$dataID);
    header("location:pending.php?c=".$consumer);
}

?>
<html>
<header>
  <style>
      .tb{width:80%;margin: 0 auto;}
      .td th,td{line-height: 21pt;border-bottom: solid 1px gray}
      .claimdiv{display: none}
  </style>
    <script src="jquery-1.8.1.min.js"></script>
    <script>
        function showDetail(dataID) {
            self.location="pending.php?id="+dataID;
        }
        function showClaim(btn) {
            $(btn).parent().find(".claimdiv").toggle();
        }

    </script>
</header>
<body>
<div>
   消费者为<b style="color: red"><?php echo $from?></b>的 未确认消息列表
</div>
<div>

  <table class="tb">
       <tr>
           <th>消息ID</th>
           <th>消费者</th>
           <th>传递间隔</th>
           <th>传递次数</th>
           <th>操作</th>
       </tr>
      <?php foreach($plist as $row):  ?>
          <tr id="tr<?php echo $row[0]?>">
          <td><?php echo $row[0]?></td>
          <td><?php echo $row[1]?></td>
          <td><?php echo $row[2]?></td>
          <td><?php echo $row[3]?></td>
          <td style="width:20%">
              <input type="submit" name="showdetail" onclick="showDetail('<?php echo $row[0]?>')" value="显示详细"/>
             <form method="post">
                 <input type="hidden" value="<?php echo $row[0]?>" name="dataID"/>
                 <input type="hidden" value="<?php echo $row[2]?>" name="idleTime"/>
                 <div class="claimdiv">
                     填写消费者名:<input type="text" name="consumer"/>
                     <input type="button" value="取消" onclick="$(this).parent().toggle()"/>
                     <input type="submit" name="cmdclaim" value="确认" />
                 </div>
              <input type="button" name="xdel"   value="转发" onclick="showClaim(this)"/>
             </form>
          </td>
          </tr>
          <?php if($queryID==$row[0]):?>
          <tr id="tr<?php echo $row[0]?>_sub">
            <td colspan="5"  >
                <table style="width:70%;float:right">
                    <?php foreach($detail as $key=>$subtable):?>
                    <tr>
                        <td colspan="100">数据ID：<?php echo $key?></td>
                    </tr>
                        <tr>
                            <?php foreach($subtable as $field=>$value):?>
                                <td><?php echo $field;?>:<?php echo $value;?></td>
                            <?php endforeach;?>
                        </tr>
                        <td colspan="100"><input type="button" value="取消" onclick="self.location='pending.php'"/></td>
              <?php endforeach;?>
                </table>
            </td>
          </tr>
      <?php endif;?>
      <?php endforeach?>

  </table>

</div>

</body>
</html>

