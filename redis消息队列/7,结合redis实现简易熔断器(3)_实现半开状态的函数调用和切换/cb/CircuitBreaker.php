<?php
require("./../core/functions.php");
define("BreakerStateOpen",1); //开
define("BreakerStateClose",2); //关，这是默认值
define("BreakerStateHalfOpen",3); //半开
class CircuitBreaker{
    private $zSetKey="circuit"; //记录错误次数的key
    private $zSetKey_open="circuit_open"; //熔断器从开到半开的状态key
    public $failCount=3;//表示失败次数>=该值 则不再访问原函数

    public $openTime=15; //20秒后自动进入半开状态
    public function invoke(object $class,string $method,array $params,callable $fallback){
        global $redis;
        $member=get_class($class)."_".$method;
        $currentState=$this->getState($member);//获取当前状态，保存到变量中
        try{
            if($currentState==BreakerStateOpen)  //开状态直接调用降级函数
                return $fallback()."开状态";
            if($currentState==BreakerStateHalfOpen) //半开状态下 随机调用
            {
                if(rand(0,100)%2==0)
                    return $fallback()."半开状态";  //这里依然调用降级函数
                else
                {//下面调用了 真实函数
                    $result=$class->$method(...$params);
                    $redis->client()->zIncrBy($this->zSetKey,1,$member); //半开状态下依然要 计数器+1，目的是让它归零
                    return $result;
                }
            }

            $ret= $class->$method(...$params);
            return $ret;
        }
        catch (Throwable $ex){
            if($currentState==BreakerStateClose) //如果是关 状态
            {
                $score=$redis->client()->zIncrBy($this->zSetKey,1,$member);
                if($score>=$this->failCount){//进入了开状态，设置一个定时器，一段时间后进入半开状态
                    echo "<div>切换为开</div>";
                    $redis->client()->zAdd($this->zSetKey_open,time()+$this->openTime,$member);
                }
            }
            if($currentState==BreakerStateHalfOpen) //如果是半开 状态 下出现异常，则依然要设置定时器
            {
                $redis->client()->zAdd($this->zSetKey,$this->failCount,$member);//把计数器 设置为 failCount
                $redis->client()->zAdd($this->zSetKey_open,time()+$this->openTime,$member);
                echo "<div>从半开切换为开</div>";
            }
            return $fallback();
        }

    }
    private function getState($member) //判断是否失败，从此不再访问原函数
    {
        global $redis;
        $getScore=$redis->client()->zScore($this->zSetKey,$member);
        if($getScore>=$this->failCount) return BreakerStateOpen;//开状态
        if($getScore<0) return BreakerStateHalfOpen;//如果值小于0 则代表是半开状态

        //其他状态 或==0 则 是关闭状态
        return BreakerStateClose;
    }
}
function cbHandler(Exception $ex){
  throw new Exception($ex->getMessage());
}

set_error_handler("cbHandler",E_ALL);
