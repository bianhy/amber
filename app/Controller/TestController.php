<?php

namespace App\Controller;

class TestController extends AbstractController
{
    public function time()
    {
        echo time();
    }

    public  function twoSum() {

        $nums   = [3,3];
        $target = 6;
        foreach ($nums as $key => $value) {
            $param = $target - $value;
            unset($nums[$key]);
            $k =  array_search($param, $nums);
            if($k !== false){
             return [$k,$key];
            }
        }
    }


}
