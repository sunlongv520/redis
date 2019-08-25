<?php
class ListOperator
{
    private   $redis=false;
    function __construct(Redis $redis)
    {
        $this->redis=$redis;
    }
    function  push(String $key,String $value,bool $l=true)
    {
        if($l) //代表是lpush
          return $this->redis->lPush($key,$value);
        return $this->redis->rPush($key,$value);
    }
    function len(String $key)
    {
        return $this->redis->lLen($key);
    }

}