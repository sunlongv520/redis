<?php

require "CircuitBreaker.php";

class myclass{
    public function test($str){
        return file_get_contents("aaaa");
    }
}
$c=new myclass();
//echo $c->test("abc");

echo (new CircuitBreaker())->invoke($c,"test",['bcd'],function(){ return "fallback";});
