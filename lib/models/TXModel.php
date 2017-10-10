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

class TXModel
{
    protected $_data;
    private $_cache = [];
    protected $_dirty = false;
    /**
     * @var \app\dao\baseDAO
     */
    protected $DAO = null;
    protected $_pk;

    public function __get($key)
    {
        if (substr($key, -7) == 'Service' || substr($key, -3) == 'DAO') {
            return TXFactory::create($key);
        }
        $data = array_merge($this->_data, $this->_cache);
        return isset($data[$key]) ? TXString::encode($data[$key]) : null;
    }

    public function _get($key)
    {
        $data = array_merge($this->_data, $this->_cache);
        return isset($data[$key]) ? $data[$key] : null;
    }

    public function __set($key, $value)
    {
        if (array_key_exists($key, $this->_data)){
            $this->_data[$key] = $value;
            $this->_dirty = true;
        } else {
            $this->_cache[$key] = $value;
        }
    }

    public function __isset($key)
    {
        return isset($this->_data[$key]) || isset($this->_cache[$key]);
    }

    public function save()
    {
        if ($this->_dirty && $this->_data && $this->DAO){
            $this->DAO->updateByPK($this->_pk, $this->_data);
            $this->_dirty = false;
        }
    }



    public function __toLogger()
    {
        return $this->_data;
    }

}