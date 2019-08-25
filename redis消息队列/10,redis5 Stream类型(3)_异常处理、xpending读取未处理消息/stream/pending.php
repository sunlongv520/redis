<?php
ini_set("display_errors","On");
error_reporting(E_ALL);
const xKey="newusers";
require("./../core/functions.php");

//
$plist=$redis->client()->xPending(xKey,"sendmail",0,"+",10,"c1");
$detail=[];
$queryID="";
if(isset($_GET["id"]))
{
    $queryID=$_GET["id"];
    $detail=$redis->client()->xRange(xKey,$queryID,$queryID);

}
?>
<html>
<header>
  <style>
      .tb{width:80%;margin: 0 auto;}
      .td th,td{line-height: 21pt;border-bottom: solid 1px gray}
  </style>
    <script src="jquery-1.8.1.min.js"></script>
    <script>
        function showDetail(dataID) {
            self.location="pending.php?id="+dataID;
        }

    </script>
</header>
<body>
<div>
    未确认消息列表
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
          <td><input type="submit" name="showdetail" onclick="showDetail('<?php echo $row[0]?>')" value="显示详细"/></td>
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

