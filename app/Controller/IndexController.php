<?php

namespace App\Controller;

use Amber\System\Libraries\Strings;
use App\Service\Factory\FruitFactory;

class IndexController extends AbstractController
{
    public function index()
    {
       $factory = FruitFactory::create('Apple');
       $make = $factory->make();
       $result = $factory->result();
       echo $make;
       echo $result;
       echo 'hello php';
    }
}
