<?php
/**
 * Tencent is pleased to support the open source community by making Biny available.
 * Copyright (C) 2017 THL A29 Limited, a Tencent company. All rights reserved.
 * Licensed under the BSD 3-Clause License (the "License"); you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * https://opensource.org/licenses/BSD-3-Clause
 * Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
 * Created by PhpStorm.
 * User: billge
 * Date: 15-12-1
 * Time: 上午11:12
 */

namespace biny\lib;

/**
 * @method bool in_array($key)
 * @method bool array_key_exists($key)
 */
class BinyArray extends \ArrayObject
{
    private $storage = [];
    private $encodes = [];

    public function __construct($storage=[])
    {
        $this->storage = $storage;
    }

    public function __toString()
    {
        return 'BinyArray';
    }

    public function getIterator()
    {
        foreach ($this->storage as $key => $value){
            $key = $this->encode($key);
            if (!isset($this->encodes[$key])){
                $this->encodes[$key] = $this->encode($value);
            }
        }
        return new \ArrayIterator($this->encodes);
    }

    public function __get($k)
    {
        return isset($this->storage[$k]) ? $this->storage[$k] : null;
    }

    public function get($key)
    {
        return $this->__get($key);
    }

    public function __set($k, $value)
    {
        $this->storage[$k] = $value;
        $this->encodes[$k] = $this->encode($value);
    }

    public function __isset($k)
    {
        return isset($this->storage[$k]);
    }

    public function __unset($k)
    {
        unset($this->storage[$k]);
        $k = $this->encode($k);
        unset($this->encodes[$k]);
    }

    public function offsetGet($k)
    {
        if (isset($this->storage[$k])){
            $key = $this->encode($k);
            if (!isset($this->encodes[$key])){
                $this->encodes[$key] = $this->encode($this->storage[$k]);
            }
            return $this->encodes[$key];
        }
        return null;
    }

    public function offsetExists($k)
    {
        return $this->__isset($k);
    }

    public function offsetUnset($k)
    {
        $this->__unset($k);
    }

    public function offsetSet($k, $value)
    {
        $this->__set($k, $value);
    }

    public function count()
    {
        return count($this->storage);
    }

    public function __toLogger()
    {
        return $this->storage;
    }

    private function encode($value)
    {
        if (is_string($value)){
            $value = BinyString::encode($value);
        } elseif (is_array($value)){
            $value = new self($value);
        }
        return $value;
    }

    public function __call($method, $args)
    {
        $args[] = &$this->storage;
        return call_user_func_array($method, $args);
    }

    public function serialize()
    {
        return serialize($this->storage);
    }

    public function __invoke($key=null)
    {
        if ($key !== null){
            return !empty($this->storage[$key]);
        } else {
            return !empty($this->storage);
        }
    }

    public function keys()
    {
        return array_keys($this->values(false));
    }

    /**
     * 完全转义
     * @param bool $inner
     * @return array
     */
    public function values($inner=true)
    {
        $values = [];
        foreach ($this->storage as $key => $value){
            $key = $this->encode($key);
            if (!isset($this->encodes[$key])){
                $this->encodes[$key] = $this->encode($value);
            }
            if ($this->encodes[$key] instanceof BinyArray){
                $values[$key] = $inner ? $this->encodes[$key]->values() : $this->encodes[$key];
            } else {
                $values[$key] = $this->encodes[$key];
            }
        }
        return $values;
    }

    public function json_encode($encode=true)
    {
        return $encode ? json_encode($this->values(), JSON_UNESCAPED_UNICODE) : json_encode($this->storage, JSON_UNESCAPED_UNICODE);
    }
}