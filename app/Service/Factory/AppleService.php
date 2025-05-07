<?php

namespace App\Service\Factory;

use App\Interfaces\FruitInterface;

class AppleService implements FruitInterface
{
    public function make(): string
    {
        return 'apple-make';
    }
    public function result(): string
    {
        return 'apple-result';
    }
}
