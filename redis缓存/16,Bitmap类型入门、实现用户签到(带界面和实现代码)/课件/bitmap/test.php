<?php
require("../Entities/functions.php");

function strToBin($str){  //这段是网上的代码 修改的
    $arr = preg_split('/(?<!^)(?!$)/u', $str);
    foreach($arr as &$v){
        $temp = unpack('H*', $v);
        $v = base_convert($temp[1], 16, 2);
        //这里改下代码 $v 如果不足8位，它不会自动补
        $v=str_pad($v,8,'0',STR_PAD_LEFT);  //这里就是修改部分
        unset($temp);
    }
    return join('',$arr);
}
echo strToBin($redis->client()->get("sign:123:1805"));


