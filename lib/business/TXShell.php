<?php
/**
 * Tencent is pleased to support the open source community by making Biny available.
 * Copyright (C) 2017 THL A29 Limited, a Tencent company. All rights reserved.
 * Licensed under the BSD 3-Clause License (the "License"); you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * https://opensource.org/licenses/BSD-3-Clause
 * Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
 * Shell class
 */

namespace biny\lib;
use TXApp;

class TXShell
{
    /**
     * 请求参数
     * @var array
     */
    private $params;

    /**
     * 顺序参数
     * @var array
     */
    private $args;

    /**
     * 构造函数
     */
    public function __construct()
    {
        $params = TXApp::$base->router->getArgs();
        $this->args = $params['args'];
        $this->params = $params['params'];
    }

    /**
     * 获取Service|DAO
     * @param $obj
     * @return TXService | TXDAO
     */
    public function __get($obj)
    {
        if (substr($obj, -7) == 'Service' || substr($obj, -3) == 'DAO') {
            return TXFactory::create($obj);
        }
    }

    /**
     * 获取请求参数
     * @param $key
     * @param null $default
     * @return float|int|mixed|null
     */
    public function param($key, $default=null)
    {
        if (is_int($key)){
            return isset($this->args[$key]) ? $this->args[$key] : $default;
        } else {
            return isset($this->params[$key]) ? $this->params[$key] : $default;
        }
    }

    /**
     * @param string $ret
     * @return mixed
     */
    public function correct($ret='success')
    {
        TXLogger::addLog($ret);
        return $ret;
    }

    /**
     * @param string $msg
     * @return string
     */
    public function error($msg="error")
    {
        TXEvent::trigger(onError, [$msg]);
        TXLogger::addError($msg);
        return $msg;
    }
}