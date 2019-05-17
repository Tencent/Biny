<?php
/**
 * Tencent is pleased to support the open source community by making Biny available.
 * Copyright (C) 2017 THL A29 Limited, a Tencent company. All rights reserved.
 * Licensed under the BSD 3-Clause License (the "License"); you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * https://opensource.org/licenses/BSD-3-Clause
 * Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
 * 配置读取类
 */

namespace biny\lib;
use App;

class Config
{
    private static $instance = [];
    private $name;
    private $cfgCaches = [];
    private $appcfgCaches = [];
    private $alias = [];

    /**
     * 单例
     * @return Config
     */
    public static function instance($name)
    {
        if (!isset(self::$instance[$name])){
            self::$instance[$name] = new self($name);
        }
        return self::$instance[$name];
    }

    /**
     * 构造
     * Config constructor.
     * @param $name
     */
    private function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Load config file
     * @param string $module
     * @throws BinyException
     */
    private function loadConfig($module)
    {
        if (!isset($this->cfgCaches[$module])) {
            $path = App::$base_root . DS . 'config' . DS . $module . '.php';

            $n_module = $module. (ENV_DEV ? '_dev' : (ENV_PRE ? '_pre' : (ENV_PUB ? '_pub' : '')));
            $n_path = App::$base_root . DS . 'config' . DS . $n_module . '.php';
            if (is_readable($path) || is_readable($n_path)) {
                $config = is_readable($path) ? require($path) : [];
                $config = is_readable($n_path) ? array_merge($config, require($n_path)) : $config;
                $this->cfgCaches[$module] = $config;
            } else {
                throw new BinyException(1002, [$path]);
            }
        }

        return $this->cfgCaches[$module];
    }

    /**
     * @param string $module
     * @return mixed
     * @throws BinyException
     */
    private function loadAppConfig($module)
    {
        if (!isset($this->appcfgCaches[$module])) {
            $path = App::$app_root . DS . 'config' . DS . $module . '.php';

            $n_module = $module. (ENV_DEV ? '_dev' : (ENV_PRE ? '_pre' : (ENV_PUB ? '_pub' : '')));
            $n_path = App::$app_root . DS . 'config' . DS . $n_module . '.php';
            if (is_readable($path) || is_readable($n_path)) {
                $config = is_readable($path) ? require($path) : [];
                $config = is_readable($n_path) ? array_merge($config, require($n_path)) : $config;
                $this->appcfgCaches[$module] = $config;
            } else {
                throw new BinyException(1002, [$path]);
            }
        }

        return $this->appcfgCaches[$module];
    }

    /**
     * get core config
     * @param $key
     * @param string $module
     * @param bool $alias
     * @return mixed|null
     */
    public function get($key, $module='config', $alias=true)
    {
        $config = $this->name === "config" ? $this->loadConfig($module) : $this->loadAppConfig($module);
        if (isset($config[$key])) {
            return $alias ? $this->getAlias($config[$key]) : $config[$key];
        } else {
            return null;
        }
    }

    /**
     * 设置别名
     * @param $key
     * @param $value
     */
    public function setAlias($key, $value)
    {
        $this->alias["@{$key}@"] = $value;
    }

    /**
     * 获取别名转义
     * @param $value
     * @return mixed
     */
    private function getAlias($value)
    {
        if ($this->alias && is_string($value)){
            $value = str_replace(array_keys($this->alias), array_values($this->alias), $value);
            return $value;
        } else {
            return $value;
        }
    }
}