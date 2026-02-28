<?php
/**
 * Tencent is pleased to support the open source community by making Biny available.
 * Copyright (C) 2017 THL A29 Limited, a Tencent company. All rights reserved.
 * Licensed under the BSD 3-Clause License (the "License"); you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * https://opensource.org/licenses/BSD-3-Clause
 * Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
 * Logger config class
 */

namespace biny\lib;
use App;

class Logger
{
    private static $_instance = null;
    private static $config = [];

    private static $LEVELS = [
        INFO => 'INFO',
        DEBUG => 'DEBUG',
        NOTICE => 'NOTICE',
        WARNING => 'WARNING',
        ERROR => 'ERROR',
    ];

    public static function instance()
    {
        if (null === self::$_instance) {
            self::$config = App::$base->config->get('logger');
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public static $ConsoleOut = [];

    /**
     * 计算内存消耗
     * @param $size
     * @return string
     */
    private static function convert($size)
    {
        $unit=['b','kb','mb','gb','tb','pb'];
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }

    /**
     * 事件触发写sql
     * @param $e
     * @param $sql
     */
    public function event($e, $sql)
    {
        $this->addLog($sql);
        $this->logger($sql, $e, "info");
    }

    /**
     * @param $message
     * @param $key
     * @param string $level
     */
    protected function logger($message, $key, $level="info")
    {
        self::$ConsoleOut[] = ['value' => $message, 'key' => $key, 'type' => $level];
        if (RUN_SHELL){
            self::showLogs();
        }
    }

    public static function log($message, $key="phpLogs")
    {
        self::instance()->logger($message, $key, "log");
    }

    public static function memory($key="memory")
    {
        self::instance()->logger(self::convert(memory_get_usage()), $key, "warn");
    }

    public static function time($key="time")
    {
        self::instance()->logger(microtime(true), $key, "warn");
    }

    public static function info($message, $key="phpLogs")
    {
        self::instance()->logger($message, $key, "info");
    }

    public static function warn($message, $key="phpLogs")
    {
        self::instance()->logger($message, $key, "warn");
    }

    public static function error($message, $key="phpLogs")
    {
        self::instance()->logger($message, $key, "error");
    }

    public static function display($message)
    {
        echo '<pre>';
        $message = (is_object($message) && method_exists($message, '__toLogger')) ? $message->__toLogger() : $message;
        print_r($message !== NULL && $message !== '' ? $message : "NULL");
        echo '</pre>';
    }

    /**
     * 获取实例
     * @param $obj
     * @return array
     */
    private static function object_to_array($obj)
    {
        $arr = [];
        $class = new \ReflectionClass($obj);
        $properties = $class->getProperties();
        foreach ($properties as $propertie){
            $value = $propertie->isPrivate() ? ":private" :
                ($propertie->isProtected() ? ":protected" :
                    ($propertie->isPublic() ? ":public" : ""));
            $arr[$propertie->getName()] = $value;
        }
        return [$class->getName() => $arr];
    }

    /**
     * 格式化输出项
     */
    public static function format()
    {
        foreach (self::$ConsoleOut as &$Out){
            $value = $Out['value'];
            if (is_object($value)){
                if (method_exists($value, '__toLogger')){
                    $value = $value->__toLogger();
                } else {
                    $value = self::object_to_array($value);
                }
            } elseif ($value === null){
                $value = 'NULL';
            } else if (is_bool($value)){
                $value = $value ? "true" : "false";
            }
            $Out['value'] = $value;
        }
        unset($Out);
    }


    /**
     * 返回所有日志
     */
    public static function showLogs(){
        if (self::$ConsoleOut){
            self::format();
            if (RUN_SHELL){
                foreach (self::$ConsoleOut as $Out){
                    $value = $Out['value'];
                    $key = $Out['key'];
                    $type = $Out['type'];
                    if (is_array($value)){
                        $value = var_export($value, true);
                    }
                    echo "[$type] $key => $value\n";
                }
            } elseif (SYS_CONSOLE){
                echo "\n<script type=\"text/javascript\">\n";
                foreach (self::$ConsoleOut as $Out){
                    $value = $Out['value'];
                    $key = $Out['key'];
                    $type = $Out['type'];
                    if (is_array($value)){
                        $value = json_encode($value);
                        $message = sprintf('console.%s("%s => ", %s);', $type, $key, $value ?: "false");
                    } else {
                        $message = sprintf('console.%s("%s => ", "%s");', $type, $key, addslashes(str_replace(["\r\n", "\r", "\n"], "", $value)));
                    }
                    echo $message."\n";
                }
                echo "</script>";
            }
            self::$ConsoleOut = [];
        }
    }

    /**
     * 析构函数
     */
    public function __destruct(){
        if (App::$base->request && (App::$base->request->isShowTpl() || !App::$base->request->isAjax())){
            self::showLogs();
        }
    }

    /**
     * 记录错误日志
     * @param $message
     * @param $key
     * @param int $level
     * @throws BinyException
     */
    public static function addError($message, $key='', $level=ERROR){
        self::$config = App::$base->config->get('logger');
        $errorLevel = self::$config['errorLevel'];
        if ($errorLevel < $level){
            return;
        }
        // 自定义日志方法
        if (isset(self::$config['sendError']) && is_callable(self::$config['sendError'])){
            call_user_func_array(self::$config['sendError'], [$message, $key, isset(self::$LEVELS[$level]) ? self::$LEVELS[$level] : 'ERROR']);
        }
        // 记录文件日志
        if (self::$config['files']){
            if (is_array($message) || is_object($message)){
                $message = var_export($message, true);
            }
            $header = sprintf("[%s]%s:%s[%s] %s\n%s", isset(self::$LEVELS[$level]) ? self::$LEVELS[$level] : 'ERROR',
                date('Y-m-d H:i:s'), substr(microtime(), 2, 3), RUN_SHELL ? 'localhost' : App::$base->request->getUserIp(),
                App::$base->request->getUrl(false), $key ? "$key => " : '');
            $message = "$header$message\n";
            if (self::$config['reorganize']) {
                $filename = sprintf("%s/error.log", App::$log_root);
                self::moveLog('error');
            } else {
                $filename = sprintf("%s/error_%s.log", App::$log_root, date('Y-m-d'));
            }
            file_put_contents($filename, $message, FILE_APPEND | LOCK_EX);
        }
    }

    /**
     * 记录日志
     * @param $message
     * @param $key
     * @param int $level
     */
    public static function addLog($message, $key='', $level=INFO){
        self::$config = App::$base->config->get('logger');
        // 自定义日志方法
        if (isset(self::$config['sendLog']) && is_callable(self::$config['sendLog'])){
            call_user_func_array(self::$config['sendLog'], [$message, $key, isset(self::$LEVELS[$level]) ? self::$LEVELS[$level] : 'INFO']);
        }
        // 记录文件日志
        if (self::$config['files']){
            if (is_array($message) || is_object($message)) {
                $message = var_export($message, true);
            }
            $header = sprintf("[%s]%s:%s [%s] %s", isset(self::$LEVELS[$level]) ? self::$LEVELS[$level] : 'INFO',
                date('Y-m-d H:i:s'), substr(microtime(), 2, 3), RUN_SHELL ? App::$base->request->getBaseUrl() : App::$base->request->getUserIp(),
                $key ? "$key => " : '');
            $message = "$header$message\n";
            $name = RUN_SHELL ? 'shell' : 'log';
            if (self::$config['reorganize']) {
                $filename = sprintf("%s/%s.log", App::$log_root, $name);
                self::moveLog($name);
            } else {
                $filename = sprintf("%s/%s_%s.log", App::$log_root, $name, date('Y-m-d'));
            }
            file_put_contents($filename, $message, FILE_APPEND | LOCK_EX);
        }
    }

    /**
     * 日志文件归档
     */
    private static function moveLog($name)
    {
        $file = App::$log_root.DS."$name.log";
        if (file_exists($file)) {
            $ctime = filectime($file);
            if (date('Y-m-d', $ctime) === date('Y-m-d')) {
                return;
            }
            if (filesize($file) > 0) {
                $dir = App::$log_root.DS.date('Ym', $ctime);
                if (!is_dir($dir)) {
                    mkdir($dir);
                }
                $dict = $dir."/".$name."-".date('Y-m-d', $ctime).".log";
                if (!file_exists($dict)) {
                    rename($file, $dict);
                }
            } else {
                unlink($file);
                touch($file);
                chmod($file, 0777);
            }
        } else {
            touch($file);
            chmod($file, 0777);
        }
    }
}