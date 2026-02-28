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
use App;

class Shell
{
    protected $request;
    protected $response;

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->request = App::$base->request;
        $this->response = App::$base->response;
    }

    /**
     * 获取Service|DAO
     * @param $obj
     * @return Service | DAO
     */
    public function __get($obj)
    {
        if (substr($obj, -7) == 'Service' || substr($obj, -3) == 'DAO') {
            return Factory::create($obj);
        }
    }

    /*********************************以下api都已废弃，将在后续版本删除********************************/

    /**
     * 获取请求参数
     * @param $key
     * @param null $default
     * @return float|int|mixed|null
     */
    public function param($key, $default=null)
    {
        Logger::warn('please use $this->request->param() instead');
        return $this->request->param($key, $default);
    }

    /**
     * @param string $ret
     * @return mixed
     */
    public function correct($ret='success')
    {
        Logger::warn('please use $this->response->correct() instead');
        return $this->response->correct($ret);
    }

    /**
     * @param string $msg
     * @return string
     */
    public function error($msg="error")
    {
        Logger::warn('please use $this->response->error() instead');
        return $this->response->error($msg);
    }
}