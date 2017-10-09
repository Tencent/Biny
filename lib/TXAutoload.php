<?php
/**
 * Tencent is pleased to support the open source community by making Biny available.
 * Copyright (C) 2017 THL A29 Limited, a Tencent company. All rights reserved.
 * Licensed under the BSD 3-Clause License (the "License"); you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * https://opensource.org/licenses/BSD-3-Clause
 * Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
 * Class TXAutoload
 */

class TXAutoload
{
    private static $loaders;
    private static $autoPath;
    private static $isReload = false;
    private static $config;
    /**
     * Autoload init
     */
    public static function init()
    {
        self::$config = TXApp::$base->config->get('autoload');
        self::$autoPath = TXApp::$base_root.DS.self::$config['autoPath'];
        if (is_readable(self::$autoPath)){
            self::$loaders = require(self::$autoPath);
        } else {
            self::$isReload = true;
            self::loading();
        }

        if (false === spl_autoload_register(['TXAutoload', 'load'])) {
            throw new TXException(1004);
        }
    }

    /**
     * 加载
     */
    private static function loading()
    {
        $lastTime = is_readable(self::$autoPath) ? filemtime(self::$autoPath) : false;
        // 5秒缓存不更新
        if (!self::$loaders || !$lastTime || time()-$lastTime > self::$config['autoSkipLoad']){
            self::$loaders = [];
            self::getLoads(__DIR__);
            self::getLoads(TXApp::$extends_root);
            self::getLoads(TXApp::$app_root. DS . "controller");
            self::getLoads(TXApp::$app_root. DS . "shell");
            self::getLoads(TXApp::$app_root. DS . "service");
            self::getLoads(TXApp::$app_root. DS . "dao");
            self::getLoads(TXApp::$app_root. DS . "form");
            self::getLoads(TXApp::$app_root. DS . "event");
            self::getLoads(TXApp::$app_root. DS . "model");
            //写入文件
            if (is_writeable(self::$autoPath)) {
                file_put_contents(self::$autoPath, "<?php\nreturn " . var_export(self::$loaders, true) . ';', LOCK_EX);
            } else {
                throw new TXException(1005, [self::$autoPath]);
            }
        }
    }

    /**
     * 获取所有类文件
     * @param $path
     * @return array
     */
    private static function getLoads($path)
    {
        foreach (glob($path . DS.'*') as $file) {
            if (is_dir($file)) {
                self::getLoads($file);
            } else {
                $name = explode(DS, $file);
                $class = str_replace('.php', '', end($name));
                self::$loaders[$class] = $file;
            }
        }
    }

    /**
     * AutoLoad
     * @param $class
     * @throws TXException
     */
    public static function load($class)
    {
        if ((!isset(self::$loaders[$class]) || !is_readable(self::$loaders[$class])) && !self::$isReload){
            self::loading();
        }

        if (isset(self::$loaders[$class])) {
            $path = self::$loaders[$class];
            if (is_readable($path)) {
                include $path;
            } else {
                throw new TXException(1003, [$class]);
            }
        } else if (substr($class, -6) == 'Action') {
            throw new TXException(1003, [$class], 404);
        } else if (self::$config['autoThrow']){
            throw new TXException(1003, [$class]);
        }
    }
}