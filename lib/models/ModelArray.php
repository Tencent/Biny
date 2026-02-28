<?php
/**
 * Tencent is pleased to support the open source community by making Biny available.
 * Copyright (C) 2017 THL A29 Limited, a Tencent company. All rights reserved.
 * Licensed under the BSD 3-Clause License (the "License"); you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * https://opensource.org/licenses/BSD-3-Clause
 * Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
 * Created by PhpStorm.
 * User: billge
 * Date: 19-11-04
 * Time: 下午5:26
 */

namespace biny\lib;
use App;
use app\dao\baseDAO;

class ModelArray extends \ArrayObject
{
    /**
     * @var baseDAO
     */
    protected $DAO = null;
    protected $_data;
    protected $_save;
    protected $_dirty = false;
    protected $_cache = array();
    protected $_relates = array();

    /**
     * 构造函数
     * requirement constructor.
     * @param $id
     * @param null $data
     */
    public function __construct($id, $data = null)
    {
        if (is_string($this->DAO) && substr($this->DAO, -3) == 'DAO'){
            $this->DAO = Factory::create($this->DAO);
        }
        if ($data) {
            $this->_data = $data;
        } else {
            $this->_data = $this->DAO->getByPk($id);
        }
    }

    public function __toString()
    {
        return 'ModelArray';
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->_data);
    }

    public function get($key)
    {
        return isset($this->_data[$key]) ? $this->_data[$key] : null;
    }

    public function __get($key)
    {
        if (substr($key, -7) == 'Service' || substr($key, -3) == 'DAO') {
            return Factory::create($key);
        }
        return isset($this->_data[$key]) ? $this->_data[$key] : null;
    }

    public function __set($key, $value)
    {
        if (array_key_exists($key, $this->_data)){
            if ($this->_data[$key] == $value){
                return;
            }
            $this->_save[$key] = $value;
            if (is_array($this->DAO->getPk()) && in_array($key, $this->DAO->getPk()) || $this->DAO->getPk() == $key){
                // 键值不可修改
                $this->_dirty = true;
            }
        }
        $this->_data[$key] = $value;
        unset($this->_cache[$key]);
    }

    public function __isset($key)
    {
        return isset($this->_data[$key]);
    }

    public function __unset($key)
    {
        unset($this->_data[$key]);
        unset($this->_cache[$key]);
    }

    public function offsetGet($key)
    {
        return isset($this->_data[$key]) ? $this->_data[$key] : null;
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
        return count($this->_data);
    }

    public function serialize()
    {
        return serialize($this->_data);
    }

    public function __invoke($key=null)
    {
        if ($key !== null){
            return !empty($this->_data[$key]);
        } else {
            return !empty($this->_data);
        }
    }

    public function keys()
    {
        return array_keys($this->_data);
    }

    /**
     * 自动映射实例
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if ($relate = $this->_relates[$name]){
            $key = $relate[1];
            if (is_array($key)){
                // 反向关联
                $k = isset($key[2]) ? $key[2] : $this->DAO->getPk();
                $DAO = Factory::create($key[0]);
                $data = $DAO->filter([$key[1]=>$this->$k])->find();
                array_unshift($arguments, $data);
            } else {
                array_unshift($arguments, $this->$key);
            }
            return call_user_func_array(array(App::$model, $relate[0]), $arguments);
        }
        $arguments[] = &$this->_data;
        return call_user_func_array($name, $arguments);
    }

    /**
     * 保存数据
     * @return array|bool|int
     */
    public function save()
    {
        if (!($this->DAO instanceof baseDAO) || !$this->DAO->getPk() || $this->_dirty){
            // 无pk不支持保存
            return false;
        }
        if (!$this->_save){
            // 无修改
            return null;
        }
        $pkey = $this->DAO->getPk();
        if (is_array($pkey)) {
            $pk = [];
            foreach ($pkey as $key) {
                $pk[] = $this->$key;
            }
        } else {
            $pk = $this->$pkey;
        }
        $ret = $this->DAO->updateByPk($pk, $this->_save);
        if ($ret > 0) {
            $this->_save = array();
        }
        return $ret;
    }

    /**
     * 是否已修改
     * @return bool
     */
    public function isChange()
    {
        return !$this->_dirty && !empty($this->_save);
    }

    /**
     * 返回数组
     * @param $key
     * @param string $split
     * @return array
     */
    protected function plist($key, $split=';')
    {
        $data = isset($this->_data[$key]) ? trim($this->_data[$key], $split) : null;
        return $data ? explode($split, $data) : array();
    }

    /**
     * 返回JSON
     * @param $key
     * @param bool $default
     * @return array|mixed|null
     */
    protected function pJson($key, $default=false)
    {
        if ($data = isset($this->_data[$key]) ? $this->_data[$key] : null){
            $json = json_decode($data, true);
            return $json ? $json : ($default ? $data : array());
        }
        return array();
    }

    /**
     * 是否存在
     * @return bool
     */
    public function exist()
    {
        return !empty($this->_data);
    }

    /**
     * 日志输出用
     * @return mixed
     */
    public function __toLogger()
    {
        return $this->_data;
    }

    /**
     * 返回数组
     * @return mixed
     */
    public function values()
    {
        return $this->_data;
    }
}

