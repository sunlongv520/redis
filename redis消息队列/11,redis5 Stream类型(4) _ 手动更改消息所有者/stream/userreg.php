<?php
ini_set("display_errors","On");
error_reporting(E_ALL);
const xKey="newusers";
require("./../core/functions.php");

if(isset($_POST["user_name"]))
{
    //先入库
    $db=new db();
    $user_id=$db->saveToDB([
                       "user_name"=>$_POST["user_name"],
                       "user_pass"=>$_POST["user_pass"],
                        "user_addtime"=>date("Y-m-d h:i:s")
                    ],"users"); //获取自增用户ID

    $redis->client()->xAdd(xKey,"*",["userid"=>$user_id]);

    echo "注册成功".date("Y-m-d h:i:s");
}
 ?>
<html>
<header>

</header>
<body>
<div>
    <form method="post">
        <div>用户名:<label>
                <input type="text" name="user_name" autocomplete="false"/>
            </label></div>
        <div>密  码:<label>
                <input type="text" name="user_pass" autocomplete="false"/>
            </label></div>
        <div><input type="submit" name="cmdreg" value="注册"/></div>
    </form>
</div>

</body>
</html>

