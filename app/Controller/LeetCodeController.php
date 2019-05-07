<?php

namespace App\Controller;

class LeetCodeController extends AbstractController
{
    public function test()
    {
        $strs = ["a","ab"];
        var_dump($this->longestCommonPrefix($strs));
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

    public function reverse() {
        $x = 0;
        $abs = abs($x);
        $strrev = strrev($abs);
        $q = $x >= 0 ? '' : '-';

        $ret = $q.(int)($strrev);
        if ($ret < -2147483648 || $ret > 2147483647){
            return 0;
        }
        return  $ret;
    }

    public function isPalindrome()
    {
        $x = 11;
        if ($x < 0) {
            return false;
        }
        $strrev = strrev($x);
        return $strrev == $x;
    }

    function longestCommonPrefix($strs) {

        $strs = ["flower","flow","flight"];
        $flag   = array_shift($strs); // 取出第一个
        $length = strlen($flag);      // 字符串长度
        $str    = '';                 // 输出字符串

        for ($i = 0; $i < $length; $i++) {
            foreach ($strs as $s) {
                if ($s[$i] != $flag[$i]) {
                    goto no;
                }
            }
            $str .= $flag[$i];
        }

        no:
        return $str;
    }
}
