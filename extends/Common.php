<?php
use biny\lib\Logger;
/**
 * Created by PhpStorm.
 * User: billge
 * Date: 16-4-18
 * Time: 下午12:18
 * 用户私有类
 */
class Common
{
    public static function test($string)
    {
        Logger::info($string);
    }

//    /**
//     * 日志示例
//     * @param $message
//     * @param $level
//     */
//    public static function sendLog($message, $level)
//    {
//        Logger::log($message, $level);
//    }
//
//    /**
//     * 日志示例
//     * @param $message
//     * @param $level
//     */
//    public static function sendError($message, $level)
//    {
//        Logger::error($message, $level);
//    }
}