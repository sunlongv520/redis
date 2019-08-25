<?php
require("../Entities/functions.php");

//为了演示简单我们把key写死
//假设用户ID是123 ,假设我们取2018年5月份数据 , 则key 我们设置为 sign:123:1805

$day=intval($_POST["day"]);
$key="sign:123:1805";
$redis->client()->setBit($key,$day-1,1);

exit("OK");
