<?php
require("./../core/functions.php");
class CircuitBreaker{
    private $zSetKey="circuit";

    public function invoke(object $class,string $method,array $params,callable $fallback){
        global $redis;
        try{
           return $class->$method(...$params);
        }
        catch (Throwable $ex){
            $member=get_class($class)."_".$method;
            $redis->client()->zIncrBy($this->zSetKey,1,$member);
            return $fallback();//函数降级
        }
    }
}
function cbHandler(Exception $ex){
  throw new Exception($ex->getMessage());
}

set_error_handler("cbHandler",E_ALL);
