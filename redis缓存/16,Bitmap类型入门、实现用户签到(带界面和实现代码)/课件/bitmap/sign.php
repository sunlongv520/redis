<?php
require("../Entities/functions.php");

//假设用户ID是123 ,假设我们取2018年5月份数据 , 则key 我们设置为 sign:123:1805

function strToBin($str){
    $arr = preg_split('/(?<!^)(?!$)/u', $str);
    foreach($arr as &$v){
        $temp = unpack('H*', $v);
        $v = base_convert($temp[1], 16, 2);
        //这里改下代码 $v 如果不足8位，它不会自动补
        $v=str_pad($v,8,'0',STR_PAD_LEFT);
        unset($temp);
    }
    return join('',$arr);
}

//先取值
$key="sign:123:1805";
$jsObject=[];//如果没有这个key代表还没签到，则默认是空数组
if($redis->client()->exists($key)) //不存在key则不作处理
{
    $getSign=strToBin($redis->client()->get($key));
//构建一个对象， 对象里面的字段 完全是根据JS代码所需要的内容 自己凑的
    $getSign=str_split($getSign);  //把字符串分隔成数组
    $index=1;
    foreach($getSign as $s)
    {
        //str_pad(待填补的字符串,填补后的长度，填补字符串，填补位置)
        if(intval($s)!==0)
            $jsObject[]=["signDay"=>str_pad($index,2,'0',STR_PAD_LEFT)];
        $index++;
    }

}




?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>redis-签到示例代码</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <link rel="stylesheet" type="text/css" href="css/sign2.css">

    <script type="text/javascript" src="js/jquery-1.8.1.min.js"></script>
    <script type="text/javascript" src="js/calendar2.js?a=1"></script>
    <script type="text/javascript">
        $(function(){
            //ajax获取日历json数据
            //const signList=[{"signDay":"09"},{"signDay":"11"},{"signDay":"12"},{"signDay":"13"}];
            const signList=<?php echo json_encode($jsObject) ?>;
            calUtil.init(signList);

            $("#calendar").click(function(e){
                const day=e.target.innerHTML;//获取当前点击对象的html,其实就是  天

                fetch("/bitmap/signapi.php",{
                     method:"POST"
                    ,body:"day="+day
                    ,headers:{
                         'Content-type':'application/x-www-form-urlencoded'
                    }
                  })
                    .then(function(result){
                        result.text().then(function(data){
                            //得到返回值，你爱咋处理咋处理
                        })
                    });

            })
        });
    </script>

</head>
<body>

<div style="" id="calendar"></div>



</body>
</html>