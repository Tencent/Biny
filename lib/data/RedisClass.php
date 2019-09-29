<?php
/**
 * Tencent is pleased to support the open source community by making Biny available.
 * Copyright (C) 2017 THL A29 Limited, a Tencent company. All rights reserved.
 * Licensed under the BSD 3-Clause License (the "License"); you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * https://opensource.org/licenses/BSD-3-Clause
 * Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
 */

namespace biny\lib;
use App;
use Redis;

/**
 * RedisClass class
 */
class RedisClass
{
    /**
     * @var Redis|Client
     */
    private $handler;
    private $config;
    private $connect;

    private static $_instance = [];

    /**
     * @param string $name
     * @return RedisClass
     */
    public static function instance($name='redis')
    {
        if (!isset(self::$_instance[$name])){
            $config = App::$base->app_config->get($name, 'dns');
            self::$_instance[$name] = new self($config);
        }
        return self::$_instance[$name];
    }

    /**
     * @param $config
     * @throws BinyException
     */
    private function __construct($config)
    {
        $this->config = App::$base->config->get('cache');
        $this->connect = $config;
    }

    /**
     * 选择 Redis 实例
     * @param $name
     * @return RedisClass
     */
    public function choose($name)
    {
        return self::instance($name);
    }

    /**
     * 创建handler
     * @throws BinyException
     */
    private function connect()
    {
        $config = $this->connect;
        $this->handler = new Redis();
        if (isset($config['keep-alive']) && $config['keep-alive']) {
            $fd = $this->handler->pconnect($config['host'], $config['port'], 1800);
        } else {
            $fd = $this->handler->connect($config['host'], $config['port']);
        }
        if ($config["password"]) {
            $this->handler->auth($config["password"]);
        }
        if (!$fd) {
            throw new BinyException(4005, [$config['host'], $config['port']]);
        }
    }

    public function get($key, $serialize=null)
    {
        if (!$this->handler){
            $this->connect();
        }
        if ($serialize === null){
            $serialize = $this->config['serialize'];
        }
        return $serialize ? unserialize($this->handler->get($key)) : $this->handler->get($key);
    }

    public function set($key, $value, $timeout=0, $serialize=null)
    {
        if (!$this->handler){
            $this->connect();
        }
        if ($serialize === null){
            $serialize = $this->config['serialize'];
        }
        $value = $serialize ? serialize($value) : $value;
        return $timeout ? $this->handler->set($key, $value, $timeout) : $this->handler->set($key, $value);
    }

    public function hget($key, $hash, $serialize=null)
    {
        if (!$this->handler){
            $this->connect();
        }
        if ($serialize === null){
            $serialize = $this->config['serialize'];
        }
        return $serialize ? unserialize($this->handler->hGet($key, $hash)) : $this->handler->hGet($key, $hash);
    }

    public function hset($key, $hash, $value, $serialize=null)
    {
        if (!$this->handler){
            $this->connect();
        }
        if ($serialize === null){
            $serialize = $this->config['serialize'];
        }
        $value = $serialize ? serialize($value) : $value;
        return $this->handler->hSet($key, $hash, $value);
    }

    /**
     * 调用redis
     * @param $method
     * @param $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if (!$this->handler){
            $this->connect();
        }
        return call_user_func_array([$this->handler, $method], $arguments);
    }
}