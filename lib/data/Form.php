<?php
/**
 * Tencent is pleased to support the open source community by making Biny available.
 * Copyright (C) 2017 THL A29 Limited, a Tencent company. All rights reserved.
 * Licensed under the BSD 3-Clause License (the "License"); you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * https://opensource.org/licenses/BSD-3-Clause
 * Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
 * Created by PhpStorm.
 * User: billge
 * Date: 15-11-4
 * Time: 下午3:05
 */

namespace biny\lib;

class Form
{
    const typeInt = 1;
    const typeBool = 2;
    const typeArray = 3;
    const typeObject = 4;
    const typeDate = 5;
    const typeDatetime = 6;
    const typeNonEmpty = 7;
    const typeRequired = 8;

    protected $_params = [];
    protected $_rules = [];
    protected $_method;
    protected $_dateFormats = ["Y-m-d", "Y/m/d"];
    protected $_datetimeFormats = ["Y-m-d H:i", "Y/m/d H:i", "Y-m-d H:i:s", "Y/m/d H:i:s"];

    protected $_data = [];

    private $_errorMsg = [];
    private $checkMethods = [];

    /**
     * 构造form
     * @param array $params
     * @param null $method
     */
    public function init($params=[], $method=null)
    {
        $this->_params = array_merge($params, Router::$ARGS);
        $this->_method = $method;
        if ($method && method_exists($this, $method)){
            $this->$method();
        }
        foreach ($this->_rules as $key => $default){
            $this->_data[$key] = isset($this->_params[$key]) ? $this->_params[$key] : (isset($default[1]) ? $default[1] : null);
        }
    }

    /**
     * 获取form
     * @return array
     */
    public function values()
    {
        return $this->_data;
    }

    /**
     * 获取form字段
     * @param $name
     * @return mixed
     * @throws BinyException
     */
    public function __get($name)
    {
        if (substr($name, -7) == 'Service' || substr($name, -3) == 'DAO') {
            return Factory::create($name);
        }
        if (!array_key_exists($name, $this->_data)){
            throw new BinyException(5001, [$name, get_class($this)]);
        }
        return $this->_data[$name];
    }

    /**
     * 返回正确
     * @return bool
     */
    protected function correct()
    {
        return true;
    }

    /**
     * 返回错误
     * @param $arr
     * @return bool
     */
    protected function error($arr=[])
    {
        $this->_errorMsg = $arr;
        return false;
    }

    /**
     * 获取错误信息
     * @return bool
     */
    public function getError()
    {
        return $this->_errorMsg ?: false;
    }

    /**
     * 检测form合法性
     * @return bool
     * @throws BinyException
     */
    public function check()
    {
        foreach ($this->_rules as $key => $value){
            if (!isset($value[0]) || !$value[0]){
                continue;
            }
            switch ($value[0]){
                case self::typeInt:
                    if (!is_numeric($this->__get($key))){
                        return $this->error([$key=>sprintf("type Error [%s] given", $this->__get($key))]);
                    }
                    break;

                case self::typeBool:
                    if ($this->__get($key) !== "true" && $this->__get($key) !== "false"){
                        return $this->error([$key=>sprintf("type Error [%s] given", $this->__get($key))]);
                    }
                    break;

                case self::typeArray:
                    if (!is_array($this->__get($key))){
                        return $this->error([$key=>sprintf("type Error [%s] given", $this->__get($key))]);
                    }
                    break;

                case self::typeObject:
                    if (!is_object($this->__get($key))){
                        return $this->error([$key=>sprintf("type Error [%s] given", $this->__get($key))]);
                    }
                    break;

                case self::typeDate:
                    $str = $this->__get($key);
                    $time = strtotime($this->__get($key));
                    if (!$time){
                        return $this->error([$key=>sprintf("type Error [%s] given", $this->__get($key))]);
                    }
                    $match = false;
                    foreach ($this->_dateFormats as $format){
                        if (date($format, $time) == $str){
                            $match = true;
                        }
                    }
                    if (!$match){
                        return $this->error([$key=>sprintf("type Error [%s] given", $this->__get($key))]);
                    }
                    break;

                case self::typeDatetime:
                    $str = $this->__get($key);
                    $time = strtotime($this->__get($key));
                    if (!$time){
                        return $this->error([$key=>sprintf("type Error [%s] given", $this->__get($key))]);
                    }
                    $match = false;
                    foreach ($this->_datetimeFormats as $format){
                        if (date($format, $time) !== $str){
                            $match = true;
                        }
                    }
                    if (!$match){
                        return $this->error([$key=>sprintf("type Error [%s] given", $this->__get($key))]);
                    }
                    break;

                case self::typeNonEmpty:
                    $value = $this->__get($key);
                    if ($value === NULL || (is_string($value) && trim($value) === "")){
                        return $this->error([$key=>sprintf("type Error [%s] given", $value)]);
                    }
                    break;

                case self::typeRequired:
                    if (!isset($this->_params[$key])){
                        return $this->error([$key=>"type Error [NULL] given"]);
                    }
                    break;

                default:
                    $value = 'valid_'.$value[0];
                    if (!isset($this->checkMethods[$value])){
                        if (!method_exists($this, $value)){
                            throw new BinyException(5002, [$value, get_class($this)]);
                        }
                        $this->checkMethods[$value] = $this->$value();
                    }
                    return $this->checkMethods[$value];
            }
        }
        return true;
    }
}