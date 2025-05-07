<?php

namespace App\Service\Factory;

use App\Interfaces\FruitInterface;

class BannerService implements FruitInterface
{
    public function make(): string
    {
        return 'banner-make';
    }
    public function result(): string
    {
        return 'banner-result';
    }
}
