<?php
require("StringOperator.php");
require("ListOperator.php");
require("HashOperator.php");
require("zSetOperator.php");
class RedisObject
{
    protected $redis_client;
    public $StringOperator;
    public $ListOperator;
    public $HashOperator;
    public $zSetOperator;
    function __construct()
    {
        $this->redis_client=new Redis();
        $this->redis_client->connect("192.168.222.137",6379);
        $this->StringOperator=new   StringOperator($this->redis_client);
        $this->ListOperator=new   ListOperator($this->redis_client);
        $this->HashOperator=new HashOperator($this->redis_client);
        $this->zSetOperator=new ZSetOperator($this->redis_client);
    }
    function setExpireTime($key,int $seconds)//设置过期时间，单位 秒
    {
        $this->redis_client->expire($key,$seconds);
    }
    function pipeExec(callable $callbak)
    {
        //管道执行
        $this->redis_client->multi(Redis::PIPELINE);
        $callbak($this->redis_client);
    }


}