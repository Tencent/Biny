<?php
/**
 * Tencent is pleased to support the open source community by making Biny available.
 * Copyright (C) 2017 THL A29 Limited, a Tencent company. All rights reserved.
 * Licensed under the BSD 3-Clause License (the "License"); you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * https://opensource.org/licenses/BSD-3-Clause
 * Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
 * object factory
 */

namespace biny\lib;
use App;

class Factory {
    /**
     * 对象列表
     *
     * @var array
     */
    private static $objects = [];

    /**
     * dynamic create object
     * @param string $class
     * @param string $alias
     * @return SingleDAO | mixed
     */
    public static function create($class, $alias=null)
    {
        if (null === $alias) {
            $alias = $class;
        }
        if (!isset(self::$objects[$alias])) {
            //可以不写DAO文件自动建立对象
            if (substr($class, -3) == 'DAO') {
                $key = substr($class, 0, -3);
                $dbConfig = App::$base->config->get('dbConfig', 'database');
                if (isset($dbConfig[$key])){
                    $dao = new SingleDAO($dbConfig[$key], $key);
                    self::$objects[$alias] = $dao;
                } else {
                    self::loadClass($class, $alias);
                }
            } else {
                self::loadClass($class, $alias);
            }
        }

        return self::$objects[$alias];
    }

    /**
     * 加载类
     * @param $class
     * @param $alias
     */
    private static function loadClass($class, $alias)
    {
        if (strpos($class, '\\')){
            self::$objects[$alias] = new $class();
        }
        $autoConfig = App::$base->config->get('namespace', 'autoload');
        if (!isset($autoConfig[$class])){
            $config = Autoload::loading();
            $autoConfig = $config['namespace'];
        }
        $class = isset($autoConfig[$class]) ? $autoConfig[$class] : $class;
        self::$objects[$alias] = new $class();
    }
}