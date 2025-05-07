<?php

namespace App\Service\Factory;

class FruitFactory
{
    //设计模式--工厂模式 + 适配器模式
    public static function create($type)
    {
        switch ($type) {
            case 'Apple':
                return new AppleService();
            case 'Banana':
                return new BannerService();
            case 'Orange':
                return new OrangeService();
            default:
                return new UnknownService(new UnnamedService());
        }
    }
}
