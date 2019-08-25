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
    function client()//外部调用获取client实例
    {
        return $this->redis_client;
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
    //事务执行，临时写下。后面再完善
    function multiExec(callable $callbak)
    {
        //管道执行
        $this->redis_client->multi(Redis::MULTI);
        $callbak($this->redis_client);
        $this->redis_client->exec();
    }
    function subscribe(String $channel,String $callback)
    {
        //开一个新连接
        $sub_client=new Redis();
        $sub_client->connect("192.168.222.137",6379);
        $sub_client->subscribe([$channel],$callback);
    }
    function psubscribe(String $channel_pattern,String $callback)
    {
        //开一个新连接
        $sub_client=new Redis();
        $sub_client->connect("192.168.222.137",6379);
        $sub_client->psubscribe([$channel_pattern],$callback);
    }
    function selectDB(int $db=0)
    {
        $this->redis_client->select($db);
    }


}