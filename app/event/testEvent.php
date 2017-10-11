<?php

namespace app\event;
use biny\lib\TXLogger;

/**
 * Created by PhpStorm.
 * User: billge
 * Date: 15-11-5
 * Time: 下午5:18
 */

class testEvent
{
    public function test($event)
    {
        TXLogger::info("tigger in event".$event);
    }
}