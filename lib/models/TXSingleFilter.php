<?php
/**
 * Tencent is pleased to support the open source community by making Biny available.
 * Copyright (C) 2017 THL A29 Limited, a Tencent company. All rights reserved.
 * Licensed under the BSD 3-Clause License (the "License"); you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * https://opensource.org/licenses/BSD-3-Clause
 * Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
 */

namespace biny\lib;

/**
 * Created by PhpStorm.
 * User: billge
 * Date: 15-8-3
 * Time: 上午11:50
 * @method TXSingleCond group($groupby)
 * @method TXSingleCond having($having)
 * @method TXSingleCond limit($len, $start=0)
 * @method TXSingleCond order($orderby)
 * @method TXSingleCond addition($additions)
 */
class TXSingleFilter extends TXFilter
{
    /**
     * @var TXSingleDAO
     */
    protected $DAO;
    protected $conds = [];

    /**
     * and 操作
     * @param $cond
     * @return TXSingleFilter
     */
    public function filter($cond=[])
    {
        return $cond ? new self($this->DAO, $cond, "__and__", $this->conds[0]) : $this;
    }

    /**
     * or 操作
     * @param $cond
     * @return TXSingleFilter
     */
    public function merge($cond)
    {
        return $cond ? new self($this->DAO, $cond, "__or__", $this->conds[0]) : $this;
    }

    /**
     * 删除数据
     * @return bool
     */
    public function delete()
    {
        $cond = new TXSingleCond($this->DAO);
        $cond->setWhere($this->buildWhere($this->conds));
        return $cond->delete();
    }

    /**
     * 获取转换后的conds
     * @return array
     */
    private function getDoubleCond($conds)
    {
        $cond = [];
        foreach ($conds as $key => $value){
            if ($key === self::valueKey) {
                $cond[$key] = [$value];
            } else if (is_array($value)) {
                $cond[$key] = $this->getDoubleCond($value);
            } else {
                $cond[$key] = $value;
            }
        }
        return $cond;
    }

    /**
     * 查询条件
     * @param $method
     * @param $args
     * @return TXSingleCond
     * @throws TXException
     */
    public function __call($method, $args)
    {
        if (in_array($method, $this->joins)){
            $cond = $this->getDoubleCond($this->conds);
            return call_user_func_array([$this->DAO, $method], $args)->setCond($cond);
        }
        if (in_array($method, $this->methods) || in_array($method, $this->calcs)){
            $cond = new TXSingleCond($this->DAO);
            $cond->setWhere($this->buildWhere($this->conds));
            return call_user_func_array([$cond, $method], $args);
        } else {
            throw new TXException(3009, [$method, __CLASS__]);
        }
    }

}