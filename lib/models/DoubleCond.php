<?php
/**
 * Tencent is pleased to support the open source community by making Biny available.
 * Copyright (C) 2017 THL A29 Limited, a Tencent company. All rights reserved.
 * Licensed under the BSD 3-Clause License (the "License"); you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * https://opensource.org/licenses/BSD-3-Clause
 * Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
 * Created by PhpStorm.
 * User: billge
 * Date: 16-4-12
 * Time: 下午12:15
 */

namespace biny\lib;

class DoubleCond extends Cond
{
    /**
     * @var DoubleDAO
     */
    protected $DAO;

    /**
     * 构建limit
     * @param $len
     * @param int $start
     * @return $this
     */
    public function limit($len, $start=0)
    {
        $this->limit = is_array($len) ? $len : [intval($start), intval($len)];
        return $this;
    }

    /**
     * 构建order
     * @param $orderby
     * @return $this
     */
    public function order($orderby)
    {
        $this->orderby[] = $orderby;
        return $this;
    }

    /**
     * 构建group
     * @param $groupby
     * @return $this
     */
    public function group($groupby)
    {
        foreach ($groupby as $key => $val){
            if (is_array($val)){
                if (!isset($this->groupby[$key])){
                    $this->groupby[$key] = [];
                }
                foreach ($val as $k => $v){
                    $this->groupby[$key][$k] = $v;
                }
            } else {
                $this->groupby[$key] = $val;
            }
        }
        return $this;
    }

    /**
     * 构建having
     * @param $having
     * @return $this
     */
    public function having($having)
    {
        foreach ($having as $key => $val){
            foreach ($val as $k => $v){
                $this->having[$key][$k] = $v;
            }
        }
        return $this;
    }

    /**
     * 构建additions
     * @param $additions
     * @return $this
     */
    public function addition($additions)
    {
        foreach ($additions as $key => $val){
            if (is_array($val)){
                if (!isset($this->additions[$key])){
                    $this->additions[$key] = [];
                }
                foreach ($val as $k => $v){
                    $this->additions[$key][$k] = $v;
                }
            } else {
                $this->additions[$key] = $val;
            }
        }
        return $this;
    }
}