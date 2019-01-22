<?php

namespace App\Controller;

class TestController extends AbstractController
{
    public function time()
    {
        echo time();
    }

    public function hello()
    {
        echo 'hello php';
    }
}
