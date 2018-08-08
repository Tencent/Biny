<?php
/**
 * Tencent is pleased to support the open source community by making Biny available.
 * Copyright (C) 2017 THL A29 Limited, a Tencent company. All rights reserved.
 * Licensed under the BSD 3-Clause License (the "License"); you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * https://opensource.org/licenses/BSD-3-Clause
 * Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
 */

# 基本加载
include __DIR__.'/TXAutoload.php';
include __DIR__.'/config/TXConfig.php';
include __DIR__.'/business/TXEvent.php';
include __DIR__.'/logger/TXLogger.php';
include __DIR__.'/exception/TXException.php';
include __DIR__.'/models/TXModel.php';

use biny\lib\TXConfig;
use biny\lib\TXRequest;
use biny\lib\TXSession;
use biny\lib\TXRouter;
use biny\lib\TXCache;
use biny\lib\TXRedis;
use biny\lib\TXMemcache;
use biny\lib\TXSocket;
use biny\lib\TXController;
use biny\lib\TXException;
use biny\lib\TXLogger;
use biny\lib\TXEvent;
use biny\lib\TXFactory;
use biny\lib\TXAutoload;
use biny\lib\TXModel;

/**
 * Framework App核心
 * @property TXConfig $config
 * @property TXConfig $app_config
 * @property TXRequest $request
 * @property TXSession $session
 * @property TXRouter $router
 * @property TXCache $cache
 * @property TXRedis|\Redis $redis
 * @property TXMemcache $memcache
 * @property TXSocket $socket
 */
class TXApp
{
    /**
     * @var TXApp
     */
    public static $base;
    /**
     * @var TXModel
     */
    public static $model;

    public static $base_root;
    public static $app_root;
    public static $view_root;
    public static $log_root;
    public static $extends_root;

    /**
     * @var TXController
     */
    private static $controller;

    /**
     * App注册运行
     * @param $apppath
     * @throws TXException
     */
    public static function registry($apppath)
    {
        self::define();
        self::$base = new self();
        self::$model = new TXModel();
        self::$base_root = dirname(__DIR__);
        self::$extends_root = self::$base_root.DS."extends";
        self::$log_root = self::$base_root.DS."logs";
        if (RUN_SHELL){
            self::$log_root .= '/shell';
        }

        if (is_readable($apppath)) {
            self::$app_root = $apppath;
        } else {
            throw new TXException(1001, [$apppath]);
        }
        self::$view_root = self::$app_root.DS."template";
        if (!is_writable(self::$log_root) && !mkdir(self::$log_root)){
            throw new TXException(1007, [self::$log_root]);
        }

        self::init();
    }

    /**
     * 初始化定义
     */
    private static function define()
    {
        defined('DS') or define('DS', DIRECTORY_SEPARATOR);
        //定义保护
        defined('RUN_SHELL') or define('RUN_SHELL', false);
        defined('SYS_DEBUG') or define('SYS_DEBUG', false);
        defined('SYS_CONSOLE') or define('SYS_CONSOLE', false);
        defined('isMaintenance') or define('isMaintenance', false);

        defined('ENV_DEV') or define('ENV_DEV', SYS_ENV === 'dev');
        defined('ENV_PRE') or define('ENV_PRE', SYS_ENV === 'pre');
        defined('ENV_PUB') or define('ENV_PUB', SYS_ENV === 'pub');

        defined('ERROR') or define('ERROR', 1);
        defined('WARNING') or define('WARNING', 2);
        defined('NOTICE') or define('NOTICE', 8);
        defined('DEBUG') or define('DEBUG', 9);
        defined('INFO') or define('INFO', 10);

        //TXEvent 默认事件
        defined('beforeAction') or define('beforeAction', 1);
        defined('afterAction') or define('afterAction', 2);
        defined('onException') or define('onException', 3);
        defined('onError') or define('onError', 4);
        defined('onRequest') or define('onRequest', 5);
        defined('onSql') or define('onSql', 'onSql');
    }

    /**
     * 异常捕获类
     * @param $code
     * @param $message
     * @param $file
     * @param $line
     * @throws TXException
     */
    public static function handleError($code, $message, $file, $line)
    {
        if ($code === E_WARNING || $code === E_NOTICE){
            $message = sprintf("%s\n#1 %s(%s)", $message, $file, $line);
            TXLogger::addError($message, 'WARNING', $code);
        } elseif (error_reporting() & $code) {
            throw new TXException(1000, $message);
        }
        return;
    }

    /**
     * 异常结束捕获
     */
    public static function handleFatalError()
    {
        $error = error_get_last();

        if (isset($error['type']) && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING])) {
            throw new TXException(1000, $error['message']);
        }
    }

    /**
     * 核心初始化
     */
    private static function init()
    {
        TXAutoload::init();
        set_error_handler(['TXApp', 'handleError']);
        register_shutdown_function(['TXApp', 'handleFatalError']);
        TXEvent::init();
        self::$controller = TXFactory::create('TXController');
    }

    /**
     * application to run
     */
    public static function run()
    {
        self::$controller->dispatcher();
    }

    /**
     * shell 执行
     */
    public static function shell()
    {
        self::$controller->shellStart();
    }

    /**
     * 获取单例全局量
     * @param $name
     * @return mixed
     * @throws TXException
     */
    public function __get($name)
    {
        switch ($name){
            case 'config':
            case 'app_config':
                return TXConfig::instance($name);
            case 'request':
                return TXRequest::getInstance();
            case 'redis':
                return TXRedis::instance();
            case 'memcache':
                return TXMemcache::instance();
            case 'socket':
                return TXSocket::instance();
            case 'session':
                return TXSession::instance();
            case 'router':
            case 'cache':
                $module = 'TX'.ucfirst($name);
                return TXFactory::create($module);

            default:
                throw new TXException(1006, $name);
        }
    }

}