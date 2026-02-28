<?php
/**
 * Tencent is pleased to support the open source community by making Biny available.
 * Copyright (C) 2017 THL A29 Limited, a Tencent company. All rights reserved.
 * Licensed under the BSD 3-Clause License (the "License"); you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * https://opensource.org/licenses/BSD-3-Clause
 * Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
 * Created by PhpStorm.
 */

namespace biny\lib;

/**
 * User: billge
 * Date: 16-4-12
 * Time: 下午12:11
 * @method int sum($field)
 * @method int max($field)
 * @method int min($field)
 * @method int avg($field)
 * @method array distinct($field)
 * @method array find($field='')
 * @method array count($field='')
 * @method array update($sets)
 * @method array addCount($sets)
 */
class Cond
{
    protected $DAO;
    protected $where;
    protected $limit=[];
    protected $orderby=[];
    protected $additions=[];
    protected $groupby=[];
    protected $having=[];

    protected $methods = ['distinct', 'find', 'count', 'update', 'addCount'];
    protected $calcs = ['max', 'min', 'sum', 'avg', 'count'];

    /**
     * 构造函数
     * @param DAO $DAO
     */
    public function __construct($DAO)
    {
        $this->DAO = $DAO;
    }

    /**
     * 设置wheres
     * @param $where
     */
    public function setWhere($where)
    {
        $this->where = $where;
    }

    /**
     * 获取字段
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->$key;
    }

    /**
     * 查询条件
     * @param $method
     * @param $args
     * @return mixed
     * @throws BinyException
     */
    public function __call($method, $args)
    {
        if (in_array($method, $this->methods) || in_array($method, $this->calcs)){
            $args = $args ? $args : [''];
            $args[] = $this;
            return call_user_func_array([$this->DAO, $method], $args);
        } else {
            throw new BinyException(3009, [$method, __CLASS__]);
        }
    }

    /**
     * query
     * @param string $field
     * @param null $key
     * @return array
     */
    public function query($field='', $key=null)
    {
        return $this->DAO->query($field, $key, $this);
    }

    /**
     * paginate
     * @param $size
     * @param null $page
     * @param string $fields
     * @return array
     */
    public function paginate($size, $page=null, $fields='')
    {
        return $this->DAO->paginate($size, $page, $fields, $this);
    }

    /**
     * pluck
     * @param string $field
     * @return array
     */
    public function pluck($field)
    {
        return $this->DAO->pluck($field, $this);
    }

    /**
     * cursor
     * @param string $field
     * @param bool|mixed $instance
     * @return array
     */
    public function cursor($field='', $instance=true)
    {
        return $this->DAO->cursor($field, $instance, $this);
    }

    /**
     * select
     * @param $sql
     * @param array $querys
     * @param int $mode
     * @return array
     */
    public function select($sql, $querys=[], $mode=Database::FETCH_TYPE_ALL)
    {
        return $this->DAO->select($sql, $querys, $mode, $this);
    }

    /**
     * command
     * @param $sql
     * @param $querys
     * @return array
     */
    public function command($sql, $querys=[])
    {
        return $this->DAO->command($sql, $querys, $this);
    }

    /**
     * 调试专用
     */
    public function __toLogger()
    {
        return [
            'DAO' => $this->DAO->getDAO(),
            'where' => $this->where,
            'limit' => $this->limit,
            'orderby' => $this->orderby,
            'additions' => $this->additions,
            'groupby' => $this->groupby,
            'having' => $this->having,
        ];
    }
}