<?php
class HashOperator
{
    private   $redis=false;
    function __construct(Redis $redis)
    {
        $this->redis=$redis;
    }
    function  set(String $key,$field,$value=null)
    {
        if(is_array($field))//数组型参数  key=>value
        {
            return $this->redis->hMset($key,$field);
        }

        if(is_string($field) && is_string($value)) //字符串型 ,好比 hset key field value
        {
            return $this->redis->hSet($key,$field,$value);
        }
        return false;
    }
    function get(String $key,String $field=null)
    {
        if($field)
        {
            return $this->redis->hGet($key,$field);
        }
        else
        return $this->redis->hGetAll($key);
    }
    function incr(String $key,String $field)//给指定字段加1
    {
        return $this->redis->hIncrBy($key,$field,1);
    }

}