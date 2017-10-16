<?php
/**
 * Tencent is pleased to support the open source community by making Biny available.
 * Copyright (C) 2017 THL A29 Limited, a Tencent company. All rights reserved.
 * Licensed under the BSD 3-Clause License (the "License"); you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * https://opensource.org/licenses/BSD-3-Clause
 * Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
 * Created by PhpStorm.
 * User: billge
 * Date: 15-7-29
 * Time: 上午11:18
 */

namespace biny\lib;
use app\model\person;

/**
 * Class TXModel
 * @package biny\lib
 * @property person $person
 * @method person person($id)
 */
class TXModel
{
    /**
     * 获取单例模型
     * @param $name
     * @return mixed
     * @throws TXException
     */
    public function __get($name)
    {
        $class = 'app\\model\\'.$name;
        return $this->create($class);
    }

    /**
     * 获取单例模型
     * @param $name
     * @return mixed
     * @throws TXException
     */
    public function __call($name, $params)
    {
        $class = 'app\\model\\'.$name;
        return $this->create($class, $params);
    }

    private function create($class, $params=[])
    {
        if (is_callable([$class, 'init'])){
            return call_user_func_array([$class, 'init'], $params);
        } else {
            throw new TXException(7000, $class);
        }
    }

}