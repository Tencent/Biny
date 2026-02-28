<?php
/**
 * Tencent is pleased to support the open source community by making Biny available.
 * Copyright (C) 2017 THL A29 Limited, a Tencent company. All rights reserved.
 * Licensed under the BSD 3-Clause License (the "License"); you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * https://opensource.org/licenses/BSD-3-Clause
 * Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
 * Cache
 */

namespace biny\lib;

class Cache {
    /**
     * @var array
     */
    private $cache= [];

    /**
     * 获取全局临时缓存
     * @param $key
     * @return null
     */
    public function __get($key)
    {
        return isset($this->cache[$key]) ? $this->cache[$key] : null;
    }

    /**
     * 设置全局临时缓存
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        $this->cache[$key] = $value;
    }

    /**
     * 判断是否存在
     * @param $key
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->cache[$key]);
    }

    /**
     * 删除全局临时缓存
     * @param $key
     */
    public function __unset($key)
    {
        unset($this->cache[$key]);
    }

    /**
     * 调试专用接口
     * @return array
     */
    public function __toLogger()
    {
        return $this->cache;
    }
}