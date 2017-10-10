<?php
/**
 * Tencent is pleased to support the open source community by making Biny available.
 * Copyright (C) 2017 THL A29 Limited, a Tencent company. All rights reserved.
 * Licensed under the BSD 3-Clause License (the "License"); you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * https://opensource.org/licenses/BSD-3-Clause
 * Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
 * Core Exception
 */

namespace biny\lib;
use TXApp;

class TXException extends \ErrorException
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
        $this->config = TXApp::$base->config->get('exception');
        $message = self::fmt_code($code, $params);
        TXEvent::trigger(onException, [$code, [$message, $this->getTraceAsString()]]);
        if (class_exists('biny\lib\TXDatabase')){
            TXDatabase::rollback();
        }
        try{
            if (RUN_SHELL){
                echo "<b>Fatal error</b>:  $message in <b>{$this->getFile()}</b>:<b>{$this->getLine()}</b>\nStack trace:\n{$this->getTraceAsString()}";
                exit;
            }
            if ($httpCode = TXApp::$base->config->get($html, 'http')){
                header($httpCode);
            }
            if (SYS_DEBUG){
                echo "<pre>";
                echo "<b>Fatal error</b>:  $message in <b>{$this->getFile()}</b>:<b>{$this->getLine()}</b>\nStack trace:\n{$this->getTraceAsString()}";

            } else {
                if (TXApp::$base->request->isShowTpl() || !TXApp::$base->request->isAjax()){
                    echo new TXResponse($this->config["exceptionTpl"], ['msg'=>$this->config['messages'][$html] ?: "系统数据异常：$html"], $params);
                } else {
                    $data = ["flag" => false, "error" => $this->config['messages'][$html] ?: "系统数据异常：$html"];
                    echo new TXJSONResponse($data);
                }
            }
            die();
        } catch (TXException $ex) {
            //防止异常的死循环
            echo "system Error";
            exit;
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
            $msgtpl = TXApp::$base->config->get($code, 'exception');
        } catch (TXException $ex) { //防止异常的死循环
            $msgtpl = $ex->getMessage();
        }
        return vsprintf($msgtpl, $params);
    }
}