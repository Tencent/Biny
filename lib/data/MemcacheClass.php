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

/**
 * @method bool delete($key)
 * @method mixed get($key)
 */
class MemcacheClass
{
    private static $_instance = [];

    /**
     * @param string $name
     * @return MemcacheClass
     */
    public static function instance($name='memcache')
    {
        if (!isset(self::$_instance[$name])){
            $config = App::$base->app_config->get($name, 'dns');
            self::$_instance[$name] = new self($config);
        }
        return self::$_instance[$name];
    }


    /**
     * @var \Memcache
     */
    private $handler;
    private $connect;

    public function __construct($config)
    {
        $this->connect = $config;
    }

    /**
     * 选择库
     * @param $name
     * @return MemcacheClass
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
        $this->handler = new \Memcache();
        if (isset($config['keep-alive']) && $config['keep-alive']){
            $fd = $this->handler->pconnect($config['host'], $config['port'], 60);
        } else {
            $fd = $this->handler->connect($config['host'], $config['port']);
        }
        if (!$fd){
            throw new BinyException(4004, [$config['host'], $config['port']]);
        }
    }

    public function set($key, $value, $expire=0)
    {
        if (!$this->handler){
            $this->connect();
        }
        return $this->handler->set($key, $value, MEMCACHE_COMPRESSED, $expire);
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