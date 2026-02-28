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
 * @method int sum($field)
 * @method int max($field)
 * @method int min($field)
 * @method int avg($field)
 * @method array distinct($field)
 * @method array find($field='')
 * @method array pluck($field)
 * @method array paginate($size, $page=null, $fields='')
 * @method array query($field='', $key=null)
 * @method array cursor($field='', $instance=true)
 * @method array select($sql, $querys=array())
 * @method array command($sql, $querys=array())
 * @method array count($field='')
 * @method array update($sets)
 * @method array addCount($sets)
 * @method DoubleFilter join($dao, $relate)
 * @method DoubleFilter leftJoin($dao, $relate)
 * @method DoubleFilter rightJoin($dao, $relate)
 */
class Filter
{
    const valueKey = "__values__";

    /**
     * @var SingleDAO|DoubleDAO
     */
    protected $DAO;
    protected $conds = [];
    protected $methods = ['distinct', 'find', 'cursor', 'query', 'pluck', 'paginate', 'count', 'group',
        'limit', 'order', 'addition', 'having', 'select', 'command', 'update', 'addCount'];
    protected $calcs = ['max', 'min', 'sum', 'avg', 'count'];
    protected $joins = ['join', 'leftJoin', 'rightJoin'];

    /**
     * 静态创建
     * @param $DAO
     * @param $filter
     * @param string $type
     * @param null $cond
     * @return DoubleFilter|SingleFilter
     * @throws BinyException
     */
    public static function create($DAO, $filter, $type="__and__", $cond=null)
    {
        if ($DAO instanceof SingleDAO){
            return new SingleFilter($DAO, $filter, $type, $cond);
        } elseif ($DAO instanceof DoubleDAO) {
            return new DoubleFilter($DAO, $filter, $type, $cond);
        } else {
            throw new BinyException(3003, gettype($DAO));
        }
    }

    /**
     * 构造函数
     * @param SingleDAO|DoubleDAO $DAO
     * @param Filter $filter
     * @param string $type
     * @param null $cond
     * @throws BinyException
     */
    public function __construct($DAO, $filter, $type="__and__", $cond=null, $link="__and__")
    {
        if (!($DAO instanceof SingleDAO || $DAO instanceof DoubleDAO)){
            throw new BinyException(3003, gettype($DAO));
        }
        if (!$filter){
            if ($cond && count($cond) == 1 && array_keys($cond)[0] === 0){
                // 条件传递
                $this->conds = $cond;
            } else {
                throw new BinyException(3007);
            }
        } elseif (is_array($filter)){
            if ($cond){
                $this->conds = [[$type => [$cond, [$link=>[[self::valueKey => $filter]]]]]];
            } else {
                $this->conds = [[$type => [[self::valueKey => $filter]]]];
            }
        } elseif (null === $cond) {
           throw new BinyException(3006, gettype($filter));
        } elseif (!($filter instanceof Filter)) {
            throw new BinyException(3004, gettype($filter));
        } elseif ($filter->getDAO() !== $DAO) {
            throw new BinyException(3005);
        } elseif ($cond) {
            $this->conds = [[$type => [$cond, $filter->getConds()[0]]]];
        } else {
            $this->conds = [[$type => [$filter->getConds()[0]]]];
        }
        $this->DAO = $DAO;

    }

    /**
     * 连表Where
     * @param $conds
     * @param string $type
     * @return string
     */
    protected function buildWhere($conds, $type='and')
    {
        $wheres = [];
        foreach ($conds as $values){
            foreach ($values as $key => $cond){
                if ($key == "__and__" || $key == "__or__"){
                    $key = str_replace("_", "", $key);
                    $sCond = $this->buildWhere($cond, $key);
                    if ($sCond){
                        $wheres[] = $sCond;
                    }
                } elseif ($key === self::valueKey){
                    $sCond = $this->DAO->buildWhere($cond, $type);
                    if ($sCond){
                        $wheres[] = $sCond;
                    }
                }
            }
        }
        if (!$wheres){
            return '';
        } elseif (count($wheres) == 1){
            return $wheres[0];
        }
        return "(" . join(") {$type} (", $wheres) . ")";
    }

    public function getDAO()
    {
        return $this->DAO;
    }

    public function getConds()
    {
        return $this->conds;
    }

    /**
     *
     * @return string
     */
    public function __toLogger()
    {
        return ['DAO' => $this->DAO->getDAO(), 'conds' => $this->conds];
    }
}