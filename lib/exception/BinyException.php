<?php
/**
 * Tencent is pleased to support the open source community by making Biny available.
 * Copyright (C) 2017 THL A29 Limited, a Tencent company. All rights reserved.
 * Licensed under the BSD 3-Clause License (the "License"); you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * https://opensource.org/licenses/BSD-3-Clause
 * Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
 * Core BinyException
 */

namespace biny\lib;
use App;

class BinyException extends \ErrorException
{
    private $config;

    /**
     * 构造函数
     * @param string $code
     * @param array $params
     * @param string $html
     */
    public function __construct($code, $params=[], $html="500")
    {
        try{
            $this->config = App::$base->config->get('exception');
            $message = self::fmt_code($code, $params);
            Event::trigger(onException, [$code, [$message, $this->getTraceAsString()]]);
            if (class_exists('biny\lib\Database')){
                Database::rollback();
            }
            if (RUN_SHELL){
                echo "<b>Fatal error</b>:  $message in <b>{$this->getFile()}</b>:<b>{$this->getLine()}</b>\nStack trace:\n{$this->getTraceAsString()}";
                exit;
            }
            if ($httpCode = App::$base->config->get($html, 'http')){
                header($httpCode);
            }
            if (SYS_DEBUG){
                echo "<pre>";
                echo "<b>Fatal error</b>:  $message in <b>{$this->getFile()}</b>:<b>{$this->getLine()}</b>\nStack trace:\n{$this->getTraceAsString()}";

            } else {
                if (App::$base->request->isShowTpl() || !App::$base->request->isAjax()){
                    $params['webRoot'] = App::$base->router->rootPath;
                    App::$base->response->display($this->config["exceptionTpl"], ['msg'=>$this->config['messages'][$html] ?: "系统数据异常：$html"], $params, false);
                } else {
                    echo App::$base->response->error($this->config['messages'][$html] ?: "系统数据异常：$html");
                }
            }
            die();
        } catch (\Exception $ex) {
            //防止异常的死循环
            if (SYS_DEBUG) {
                echo "<b>Fatal error</b>: Endless loop <b>{$this->getFile()}</b>:<b>{$this->getLine()}</b>\nStack trace:\n{$this->getTraceAsString()}";
            } else {
                echo "SYSTEM ERROR";
            }
            die();
        }
    }

    /**
     * 格式化代码为字符串
     * @param int $code
     * @param array $params
     * @return string
     */
    public static function fmt_code($code, $params=[])
    {
        try {
            $msgtpl = App::$base->config->get($code, 'exception');
        } catch (\Exception $ex) { //防止异常的死循环
            $msgtpl = $ex->getMessage();
        }
        return vsprintf($msgtpl, $params);
    }
}
