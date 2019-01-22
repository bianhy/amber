<?php
namespace Amber\System\Event;

interface ListenerInterface
{
    public function handle($params);
}