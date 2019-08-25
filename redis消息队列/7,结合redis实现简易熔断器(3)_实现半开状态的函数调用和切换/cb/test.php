<?php

require "CircuitBreaker.php";

class myclass{
    public function test($str){
      return file_get_contents("aaaa");
        //    return "aaa";

    }
}
$c=new myclass();

echo (new CircuitBreaker())->invoke($c,"test",['bcd'],function(){ return "fallback";});


