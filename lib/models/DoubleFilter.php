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
 * @method DoubleCond group($groupby)
 * @method DoubleCond having($having)
 * @method DoubleCond limit($len, $start=0)
 * @method DoubleCond order($orderby)
 * @method DoubleCond addition($additions)
 */
class DoubleFilter extends Filter
{
    /**
     * @var DoubleDAO
     */
    protected $DAO;
    protected $conds = [];

    /**
     * and 操作
     * @param array $cond
     * @param string $link
     * @return DoubleFilter
     */
    public function filter($cond=[], $link='and')
    {
        $link = (strtolower($link) == 'or') ? '__or__' : '__and__';
        return $cond ? new self($this->DAO, $cond, "__and__", $this->conds[0], $link) : $this;
    }

    /**
     * or 操作
     * @param $cond
     * @param string $link
     * @return DoubleFilter
     */
    public function merge($cond, $link='or')
    {
        $link = (strtolower($link) == 'and') ? '__and__' : '__or__';
        return $cond ? new self($this->DAO, $cond, "__or__", $this->conds[0], $link) : $this;
    }

    /**
     * 查询条件
     * @param $method
     * @param $args
     * @return DoubleCond
     * @throws BinyException
     */
    public function __call($method, $args)
    {
        if (in_array($method, $this->joins)){
            return call_user_func_array([$this->DAO, $method], $args)->setCond($this->conds);
        }
        if (in_array($method, $this->methods) || in_array($method, $this->calcs)){
            $cond = new DoubleCond($this->DAO);
            $cond->setWhere($this->buildWhere($this->conds));
            return call_user_func_array([$cond, $method], $args);
        } else {
            throw new BinyException(3009, [$method, __CLASS__]);
        }
    }
}