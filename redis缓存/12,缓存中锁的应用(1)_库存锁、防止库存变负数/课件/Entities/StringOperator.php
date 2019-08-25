<?php
class StringOperator
{
       private   $redis=false;
        function __construct(Redis $redis)
        {
             $this->redis=$redis;
        }
        function  getFromReids(String $key)
        {
            return $this->redis->get($key);
        }
    function  setToRedis(String $key,String $value,int $expire=0)
    {
        return $this->redis->set($key,$value,$expire);
    }
    function expireCache(String $key,int $expire=5)
    {
        $getExpire=$this->redis->ttl($key);
        if($getExpire<0) //已经过期
            $getExpire=0;
        $this->redis->expire($key,$expire+$getExpire);
    }
    function incrForbidden(String $key,int $score=1)//给指定的key 加入分数
    {
       $ret= $this->redis->incrBy($key,$score);
       $this->redis->expire($key,forbiddenExpire);
       return $ret;
    }
}