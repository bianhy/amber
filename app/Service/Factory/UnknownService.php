<?php

namespace App\Service\Factory;

use App\Interfaces\FruitInterface;

class UnknownService implements FruitInterface
{

    //适配器模式
    private UnnamedService $oldSystem;

    public function __construct(UnnamedService  $oldSystem)
    {
        $this->oldSystem = $oldSystem;
    }
    public function make(): string
    {

        return $this->oldSystem->make();
    }
    public function result(): string
    {
        return $this->oldSystem->result();
    }
}
