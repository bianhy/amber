<?php

namespace App\Service\Factory;

use App\Interfaces\FruitInterface;

class UnnamedService implements FruitInterface
{
    public function make(): string
    {
        return 'unnamed';
    }
    public function result(): string
    {
        return 'unnamed-result';
    }
}
