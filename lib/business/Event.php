<?php
/**
 * Tencent is pleased to support the open source community by making Biny available.
 * Copyright (C) 2017 THL A29 Limited, a Tencent company. All rights reserved.
 * Licensed under the BSD 3-Clause License (the "License"); you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * https://opensource.org/licenses/BSD-3-Clause
 * Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
 * Created by PhpStorm.
 * User: billge
 * Date: 15-11-2
 * Time: 下午6:04
 */

namespace biny\lib;

class Event
{
    /**
     * 事件句柄
     * @var int
     */
    private static $fh = 0;

    /**
     * 事件观察者
     * @var array
     */
    private static $monitors = [];

    /**
     * 获取form字段
     * @param $name
     * @return mixed
     * @throws BinyException
     */
    public function __get($name)
    {
        if (substr($name, -7) == 'Service' || substr($name, -3) == 'DAO') {
            return Factory::create($name);
        }
    }

    /**
     * 绑定事件
     * @param callable $method
     * @param $event
     * @param null $times
     * @return int
     * @throws BinyException
     */
    public static function bind($method, $event, $times=null)
    {
        if (!is_callable($method)){
            throw new BinyException(5003, isset($method[1]) ? $method[1] : 'null');
        }
        $fh = ++self::$fh;
        self::$monitors[$event][$fh] = ['m'=>$method, 't'=>$times];
        return $fh;
    }

    /**
     * 绑定永久事件
     * @param callable $method
     * @param $event
     * @return int
     */
    public static function on($event, $method=null)
    {
        $method = $method ?: [Logger::instance(), 'event'];
        return self::bind($method, $event);
    }

    /**
     * 绑定一次事件
     * @param callable $method
     * @param $event
     * @return int
     */
    public static function one($event, $method=null)
    {
        $method = $method ?: [Logger::instance(), 'event'];
        return self::bind($method, $event, 1);
    }

    /**
     * 解绑事件
     * @param $event
     * @param $fh
     * @return bool
     */
    public static function off($event, $fh=null)
    {
        if ($fh){
            if (isset(self::$monitors[$event][$fh])){
                unset(self::$monitors[$event][$fh]);
                return true;
            } else {
                return false;
            }
        } else {
            unset(self::$monitors[$event]);
            return true;
        }
    }

    /**
     * 触发事件
     * @param $event
     * @param array $params
     * @return bool
     */
    public static function trigger($event, $params=[])
    {
        if (!isset(self::$monitors[$event])){
            return false;
        }
        array_unshift($params, $event);
        foreach (self::$monitors[$event] as $fh => &$value){
            $method = $value['m'];
            call_user_func_array($method, $params);
            if (isset($value['t']) && --$value['t'] <= 0){
                unset(self::$monitors[$event][$fh]);
            }
        }
        unset($value);
        return true;
    }

    /**
     * 启动类
     */
    public static function init()
    {
        Event::on(onException, ['biny\lib\Event', 'onException']);
        Event::on(onRequest, ['biny\lib\Event', 'onRequest']);
    }

    /**
     * 异常错误
     * @param $event
     * @param $code
     * @param $params
     */
    public static function onException($event, $code, $params)
    {
        Logger::addError("ERROR CODE: $code\n".join("\n", $params));
    }

    /**
     * 请求入口
     * @param $event
     * @param $request Request
     */
    public static function onRequest($event, $request)
    {
        Logger::addLog('request: '.$request->getHostInfo().$request->getUrl());
    }
}