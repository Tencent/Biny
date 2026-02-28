<?php
/**
 * Tencent is pleased to support the open source community by making Biny available.
 * Copyright (C) 2017 THL A29 Limited, a Tencent company. All rights reserved.
 * Licensed under the BSD 3-Clause License (the "License"); you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * https://opensource.org/licenses/BSD-3-Clause
 * Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
 */

# 基本加载
include __DIR__.'/Autoload.php';
include __DIR__.'/config/Config.php';
include __DIR__.'/business/Event.php';
include __DIR__.'/logger/Logger.php';
include __DIR__.'/exception/BinyException.php';
include __DIR__.'/models/Model.php';

use biny\lib\Config;
use biny\lib\Language;
use biny\lib\Request;
use biny\lib\Response;
use biny\lib\Session;
use biny\lib\Router;
use biny\lib\Cache;
use biny\lib\RedisClass;
use biny\lib\MemcacheClass;
use biny\lib\Socket;
use biny\lib\Controller;
use biny\lib\BinyException;
use biny\lib\Logger;
use biny\lib\Event;
use biny\lib\Factory;
use biny\lib\Autoload;
use biny\lib\Model;

/**
 * Framework App核心
 * @property Config $config
 * @property Config $app_config
 * @property Request $request
 * @property Response $response
 * @property Session $session
 * @property Router $router
 * @property Cache $cache
 * @property RedisClass|\Redis $redis
 * @property MemcacheClass|\Memcache $memcache
 * @property Socket $socket
 */
class App
{
    /**
     * @var App
     */
    public static $base;
    /**
     * @var Model
     */
    public static $model;

    public static $base_root;
    public static $app_root;
    public static $view_root;
    public static $log_root;
    public static $extends_root;

    /**
     * @var Controller
     */
    private static $controller;

    /**
     * App注册运行
     * @param $appPath
     * @throws BinyException
     */
    public static function registry($appPath)
    {
        self::define();
        self::$base = new self();
        self::$model = new Model();
        self::$base_root = dirname(__DIR__);
        self::$extends_root = self::$base_root.DS."extends";
        self::$log_root = self::$base_root.DS."logs";

        if (is_readable($appPath)) {
            self::$app_root = $appPath;
        } else {
            throw new BinyException(1001, [$appPath]);
        }
        self::$view_root = self::$app_root.DS."template";
        if (!is_writable(self::$log_root) && !mkdir(self::$log_root)){
            throw new BinyException(1007, [self::$log_root]);
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

        //Event 默认事件
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
     * @throws BinyException
     */
    public static function handleError($code, $message, $file, $line)
    {
        if ($code === E_WARNING || $code === E_NOTICE){
            $message = sprintf("%s\n#1 %s(%s)", $message, $file, $line);
            Logger::addError($message, 'WARNING', $code);
        } elseif (error_reporting() & $code) {
            throw new BinyException(1000, $message);
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
            throw new BinyException(1000, $error['message']);
        }
    }

    /**
     * 核心初始化
     */
    private static function init()
    {
        Autoload::init();
        set_error_handler(['App', 'handleError']);
        register_shutdown_function(['App', 'handleFatalError']);
        Event::init();
        self::$controller = Factory::create('Controller');
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
     * @throws BinyException
     */
    public function __get($name)
    {
        // todo namescpce
        switch ($name){
            case 'config':
            case 'app_config':
                return Config::instance($name);
            case 'request':
                return Request::getInstance();
            case 'response':
                return Response::getInstance();
            case 'redis':
                return RedisClass::instance();
            case 'memcache':
                return MemcacheClass::instance();
            case 'socket':
                return Socket::instance();
            case 'session':
                return Session::instance();
            case 'router':
            case 'cache':
                $module = ucfirst($name);
                return Factory::create($module);

            default:
                throw new BinyException(1006, $name);
        }
    }

}

/**
 * 获取多语言
 * @param $content
 * @return mixed
 */
function _L($content){
    return Language::getLanguage() ? Language::getContent($content) : $content;
}