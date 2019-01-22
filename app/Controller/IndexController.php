<?php

namespace App\Controller;

use Amber\System\Libraries\Strings;

class IndexController extends AbstractController
{
    public function index()
    {
        header('Content-Type：application/json;charset=utf-8');
        $str = 'sSHJA01ceshixuesheng368';
        $reg = '/^[a-zA-Z]{1}[0-9a-z_]{5,50}/';
        var_dump(preg_match($reg,$str));exit;
        echo 'hello php';
    }
}
