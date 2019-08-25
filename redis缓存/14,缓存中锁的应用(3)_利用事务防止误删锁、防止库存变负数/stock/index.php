<?php
require("../Entities/functions.php");
$redis->zSetOperator->setName="stock";//设置sorted list的key 名称
$current_stock=$redis->zSetOperator->get("prod101");


?>
<html>

<head>
  <script>
   function vote(pID)
   {
    let isDelay=document.getElementById('cbDelay').checked;

	let delay=isDelay?"?delay=1":"?delay=0";
	 
   fetch("/stock/api.php"+delay,{method:"POST"
       ,body:"id="+pID.toString()
       ,headers:{
           'Content-Type': 'application/x-www-form-urlencoded'
       }
   })
	       .then((response)=>{
               response.json().then((json)=>{
                  document.getElementById("span101").innerHTML=json.result.toString()
                   document.getElementById("spanmsg").innerHTML=json.msg.toString()
               })
           })
   }
    
	
    
  </script>
</head>
<body>

<div>
 <div>
   模拟卡顿5秒<input type='checkbox' id='cbDelay'/>

   <hr/>
 </div>
  <div>
    <a href='#'>这是id=101的商品</a> <input type='button' value='下单' onclick="vote(101)"/>
      当前库存:<span id="span101"><?php echo $current_stock ?></span> <span id="spanmsg"></span>

  </div>

</div>
</body>

</html>