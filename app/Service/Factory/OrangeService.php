<?php

namespace App\Service\Factory;

use App\Interfaces\FruitInterface;

class OrangeService implements FruitInterface
{
    public function make(): string
    {
        return 'orange-make';
    }
    public function result(): string
    {
        return 'oranges-result';
    }
}
