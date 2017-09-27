<?php
/**
 * Created by PhpStorm.
 * User: billge
 * Date: 15-11-5
 * Time: 下午5:18
 */

class testEvent
{
    public function before($event)
    {
        TXLogger::info("tigger in event".$event);
    }

    public function another($event, $params=array())
    {
        TXLogger::info("tigger in anther".$event);
        TXLogger::info($params);
    }
}