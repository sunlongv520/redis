<?php
class zSetOperator
{
    private   $redis=false;
    public $setName='newsclick';//默认是点击量
    function __construct(Redis $redis)
    {
        $this->redis=$redis;
    }
    function  set(int $score,String $member) //zadd的封装
    {
        return $this->redis->zAdd($this->setName,$score,$member);
    }
    function get(String $member):int //zscore的封装
    {
        $getScore= $this->redis->zScore($this->setName,$member);
        if(!$getScore) //没取到
            $getScore=0;
         return $getScore;
    }
    function incr(String $member)//给指定字段加1
    {
        return $this->redis->zIncrBy($this->setName,1,$member);
    }

}