<?php
/**
 * Tencent is pleased to support the open source community by making Biny available.
 * Copyright (C) 2017 THL A29 Limited, a Tencent company. All rights reserved.
 * Licensed under the BSD 3-Clause License (the "License"); you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * https://opensource.org/licenses/BSD-3-Clause
 * Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
 * Session service
 * @author billge
 */

namespace biny\lib;
use TXApp;

class TXSession
{
    private static $instance = null;
    private $_data = null;
    private $config;

    /**
     * 初始化Session
     * @return null|TXSession
     */
    public static function instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->config = TXApp::$base->config->get('cache')['session'];
    }

    /**
     * session 已连接
     * @return bool
     */
    private function isActive()
    {
        return session_status() == PHP_SESSION_ACTIVE;
    }

    private function start(){
        if ($cf = TXApp::$base->app_config->get($this->config['save_handler'], 'dns')){
            ini_set("session.save_handler", $this->config['save_handler']);
            ini_set("session.save_path", 'tcp://' . $cf['host'] . ':' . $cf['port']);
        }
        if ($lifetime = $this->config['maxlifetime']){
            ini_set("session.gc_maxlifetime", $lifetime);
        }
        @session_start();
        $this->_data = $_SESSION;
    }

    //解决session死锁问题
    public function close()
    {
        if ($this->isActive()){
            @session_write_close();
            $this->_data = null;
        }
    }

    /**
     * 获取key
     * @param $key
     * @return null
     */
    public function __get($key)
    {
        if (!$this->isActive()){
            $this->start();
        }
        return isset($this->_data[$key]) ? $this->_data[$key] : null;
    }

    /**
     * 设置key
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        if (!$this->isActive()){
            $this->start();
        }
        $_SESSION[$key] = $this->_data[$key] = $value;
    }

    /**
     * 删除sessionKey
     * @param $key
     */
    public function __unset($key)
    {
        if (!$this->isActive()){
            $this->start();
        }
        unset($this->_data[$key]);
        unset($_SESSION[$key]);
    }

    /**
     * @param $key
     * @return bool
     */
    public function __isset($key)
    {
        if (!$this->isActive()){
            $this->start();
        }
        return isset($this->_data[$key]);
    }

    /**
     * 清空
     */
    public function clear()
    {
        if (!$this->isActive()){
            $this->start();
        }
        $this->_data = $_SESSION = [];
    }
}