<?php
require("./../core/functions.php");
define("BreakerStateOpen",1); //开
define("BreakerStateClose",2); //关，这是默认值
define("BreakerStateHalfOpen",3); //半开
class CircuitBreaker{
    private $zSetKey="circuit"; //记录错误次数的key
    private $zSetKey_open="circuit_open"; //熔断器从开到半开的状态key
    public $failCount=3;//表示失败次数>=该值 则不再访问原函数

    public $openTime=20; //20秒后自动进入半开状态
    public function invoke(object $class,string $method,array $params,callable $fallback){
        global $redis;
        $member=get_class($class)."_".$method;
        try{
            if($this->getState($member)==BreakerStateOpen)
                return $fallback()."开状态";
            if($this->getState($member)==BreakerStateHalfOpen)
                return $fallback()."半开状态";

            return $class->$method(...$params);
        }
        catch (Throwable $ex){
         $score=$redis->client()->zIncrBy($this->zSetKey,1,$member);
         if($score>=$this->failCount){//进入了开状态，设置一个定时器，进入半开状态
             $redis->client()->zAdd($this->zSetKey_open,time()+$this->openTime,$member);
         }
         return $fallback();//函数降级
        }
    }
    private function getState($member) //判断是否失败，从此不再访问原函数
    {
        global $redis;
        $getScore=$redis->client()->zScore($this->zSetKey,$member);
        if($getScore>=$this->failCount) return BreakerStateOpen;//开状态
        if($getScore==-1) return BreakerStateHalfOpen;//如果值是-1 则代表是半开状态
        return BreakerStateClose;
    }



}
function cbHandler(Exception $ex){
  throw new Exception($ex->getMessage());
}

set_error_handler("cbHandler",E_ALL);
