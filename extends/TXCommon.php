<?php
use biny\lib\TXLogger;
/**
 * Created by PhpStorm.
 * User: billge
 * Date: 16-4-18
 * Time: 下午12:18
 * 用户私有类
 */
class TXCommon
{
    public static function test($string)
    {
        TXLogger::info($string);
    }

//    /**
//     * 日志示例
//     * @param $message
//     * @param $level
//     */
//    public static function sendLog($message, $level)
//    {
//        TXLogger::log($message, $level);
//    }
//
//    /**
//     * 日志示例
//     * @param $message
//     * @param $level
//     */
//    public static function sendError($message, $level)
//    {
//        TXLogger::error($message, $level);
//    }
}