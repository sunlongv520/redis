<?php

require("../Entities/functions.php");

var_export($redis->client()->set("name",1,["NX","EX"=>2]));

    
