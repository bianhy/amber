<?php
namespace Amber\System\Event;

class Event
{
    public static function emit(ListenerInterface $event, $params=[])
    {
        return call_user_func_array([$event,'handle'],[$params]);
    }
}