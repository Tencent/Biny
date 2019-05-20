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
 * 多表数据库
 * @method DoubleCond limit($len, $start=0)
 * @method DoubleCond group($groupby)
 * @method DoubleCond having($having)
 * @method DoubleCond order($orderby)
 * @method DoubleCond addition($additions)
 */
class DoubleDAO extends DAO
{
    /**
     * 表格名称
     * @var string
     */
    protected $table = null;

    /**
     * 连表位
     */
    protected $doubles;

    /**
     * 关联表
     */
    protected $DAOs;

    /**
     * 管理结构
     */
    protected $relates;

    /**
     * 构造函数
     */
    public function __construct($DAOs, $relates, $db)
    {
        $this->dbConfig = $db;
        if (count($relates) !== count($DAOs)-1){
            throw new BinyException(3002, [json_encode($DAOs)]);
        }
        $this->DAOs = $DAOs;
        $this->doubles = array_keys($DAOs);
        $this->relates = $relates;
    }

    /**
     * 获取Log DAO
     * @return string
     */
    public function getDAO()
    {
        $tables = array_values($this->DAOs);
        return join("--", $tables);
    }

    /**
     * 获取连表
     * @return string
     */
    protected function getTable()
    {
        if (!$this->table){
            $dbtbs = [];
            $i = 0;
            foreach ($this->DAOs as $name => $table){
                if (!$dbtbs){
                    $dbtbs[] = "{$table} `$name`";
                } else {
                    $relate = $this->relates[$i++];
                    $join = $relate[0];
                    $on = $relate[1];
                    $dbtbs[] = $join;
                    $dbtbs[] = "{$table} `$name`";
                    $ons = [];
                    foreach ($on as $val){
                        $key = $val[0];
                        $value = $val[1];
                        if (is_array($value)){
                            $ons[] = "{$key} {$value[0]} {$value[1]}";
                        } else {
                            $ons[] = "{$key}={$value}";
                        }
                    }
                    $dbtbs[] = "on ".join(' and ', $ons);
                }
            }
            $this->table = join(" ", $dbtbs);
        }
        return $this->table;
    }

    /**
     * 链接表
     * @param $dao SingleDAO
     * @param $relateD
     * @param string $type
     * @return $this|DoubleDAO
     * @throws BinyException
     */
    protected function _join($dao, $relateD, $type='join')
    {
        $daoClass = $dao->getCalledClass();
        if (in_array($daoClass, $this->doubles)){
            $daoClass .= count($this->doubles);
        }
        if (!$this->checkConfig($dao)){
            throw new BinyException(3002, "DAOs must be the same Host");
        }
        $DAOs = $this->DAOs;
        $DAOs[$daoClass] = $dao->getTable();

        $relates = $this->relates;
        $join = [];
        foreach ($relateD as $k => $relate){
            if (is_string($k) && in_array($k, $this->doubles)){
                $table = $k;
            } else if (isset($this->doubles[$k])){
                $table = $this->doubles[$k];
            } else {
                continue;
            }
            foreach ($relate as $key => $value){
                $key = "`{$this->real_escape_string($table)}`.`{$this->real_escape_string($key)}`";
                if (is_array($value) && count($value)>=2 && in_array($value[0], $this->extracts)){
                    $join[] = [$key, [$value[0], "`{$this->real_escape_string($daoClass)}`.`{$this->real_escape_string($value[1])}`"]];
                } else {
                    $join[] = [$key, "`{$this->real_escape_string($daoClass)}`.`{$this->real_escape_string($value)}`"];
                }
            }
        }
        $relates[] = [$type, $join];
        return new DoubleDAO($DAOs, $relates, $this->dbConfig);
    }

    /**
     * buildWhere
     * @param $conds
     * @param string $type
     * @return string
     */
    public function buildWhere($conds, $type='and')
    {
        if (empty($conds)) {
            return '';
        }
        $doubles = $this->doubles;
        $where = [];
        foreach ($conds as $k => $cond){
            if (is_string($k) && in_array($k, $doubles)){
                $table = $k;
            } else if (isset($doubles[$k])){
                $table = $doubles[$k];
            } else {
                continue;
            }
            foreach($cond as $key => $value) {
                $key = $this->real_escape_string($key);
                if (in_array(strtolower($key), $this->extracts)){
                    foreach ($value as $arrk => $arrv){
                        $arrk = $this->real_escape_string($arrk);
                        if (is_null($arrv)){
                            $where[] = "`{$table}`.`{$arrk}`{$key} NULL";
                        } else if (is_string($arrv)){
                            $arrv = $this->real_escape_string($arrv);
                            $where[] = "`{$table}`.`{$arrk}`{$key}'{$arrv}'";
                        } else if ($arrv instanceof \stdClass){
                            $where[] = "`{$table}`.`{$arrk}`{$key}{$arrv->scalar}";
                        } else if (is_array($arrv)){
                            foreach ($arrv as $av){
                                $arrv = $this->real_escape_string($av);
                                $where[] = "`{$table}`.`{$arrk}`{$key}'{$arrv}'";
                            }
                        } else {
                            $where[] = "`{$table}`.`{$arrk}`{$key}{$arrv}";
                        }
                    }
                } elseif ($key === '__like__'){
                    foreach ($cond[$key] as $arrk => $arrv){
                        $arrk = $this->real_escape_string($arrk);
                        if (is_array($arrv)){
                            foreach ($arrv as $like){
                                $like = $this->real_like_string($like);
                                if (substr($like, 0, 1) !== '^'){
                                    $like = '%'.$like;
                                }
                                if (substr($like, -1, 1) !== '$'){
                                    $like .= '%';
                                }
                                $like = trim($like, "^$");
                                $where[] = "`{$table}`.`{$arrk}` like '{$like}'";
                            }
                        } else {
                            $arrv = $this->real_like_string($arrv);
                            if (substr($arrv, 0, 1) !== '^'){
                                $arrv = '%'.$arrv;
                            }
                            if (substr($arrv, -1, 1) !== '$'){
                                $arrv .= '%';
                            }
                            $arrv = trim($arrv, "^$");
                            $where[] = "`{$table}`.`{$arrk}` like '{$arrv}'";
                        }
                    }
                } elseif (is_null($value)){
                    $where[] = "`{$table}`.`{$key}`is NULL";
                } elseif ($value instanceof \stdClass) {
                    $where[] = "`{$table}`.`{$key}`={$value->scalar}";
                } elseif (is_string($value)) {
                    $value = $this->real_escape_string($value);
                    $where[] = "`{$table}`.`{$key}`='{$value}'";
                } elseif (is_array($value)){
                    if (!$value){
                        $where[] = 'FALSE';
                        continue;
                    }
                    foreach ($value as &$val){
                        if (is_string($val)){
                            $val = "'{$this->real_escape_string($val)}'";
                        }
                    }
                    unset($val);
                    $value = "(". join(",", $value).")";
                    $where[] = "`{$table}`.`{$key}` in {$value}";
                } else {
                    $where[] = "`{$table}`.`{$key}`={$value}";
                }
            }
        }
        return join(" {$type} ", $where);
    }

    /**
     * 获取复合表fields
     * @param $fields
     * @param array $group
     * @return string
     * @throws BinyException
     */
    protected function buildFields($fields, $group=[]){
        if (is_array($fields)){
            $temps = [];
            foreach ($fields as $k => $field){
                if (is_string($k) && in_array($k, $this->doubles)){
                    $table = $k;
                } else if (isset($this->doubles[$k])){
                    $table = $this->doubles[$k];
                } else {
                    continue;
                }
                if ($field === "*"){
                    $temps[] = "`{$table}`.*";
                } elseif(is_string($field)){
                    $temps[] = "`{$table}`.`{$field}`";
                } else {
                    foreach ($field as $key => $column){
                        if (is_string($key)){
                            $column = $this->real_escape_string($column);
                            $key = $this->real_escape_string($key);
                            $temps[] = "`{$table}`.`".$key."` AS `$column`";
                        } elseif ($column instanceof \stdClass) {
                            $temps[] = $column->scalar;
                        } else {
                            $column = $this->real_escape_string($column);
                            $temps[] = "`{$table}`.`".$column."`";
                        }
                    }
                }
            }
            $fields = join(',', $temps);
        }
        if ($group){
            if ($fields){
                $groups = [$fields];
            } else {
                $groups = [];
            }
            foreach ($group as $key => $values){
                if (is_string($key) && in_array($key, $this->doubles)){
                    $table = $key;
                } else if (isset($this->doubles[$key])){
                    $table = $this->doubles[$key];
                } else {
                    continue;
                }
                foreach ($values as $ck => $vals){
                    if (!in_array(strtolower($ck), $this->calcs)){
                        throw new BinyException(3011, [$ck]);
                    }
                    foreach ($vals as $k => $value){
                        $value = $this->real_escape_string($value);
                        if (is_string($k)){
                            $k = $this->real_escape_string($k);
                            if ($ck == 'distinct'){
                                $groups[] = "COUNT(DISTINCT `{$table}`.`{$k}`) as '{$value}'";
                            } else {
                                $groups[] = "{$ck}(`{$table}`.`{$k}`) as '{$value}'";
                            }
                        } else {
                            if ($ck == 'distinct'){
                                $groups[] = "COUNT(DISTINCT `{$table}`.`{$value}`) as '{$value}'";
                            } else {
                                $groups[] = "{$ck}(`{$table}`.`{$value}`) as '{$value}'";
                            }
                        }
                    }
                }
            }
            return join(',', $groups);
        }
        return $fields ?: '*';
    }

    /**
     * 拼装Doubleorderby
     * @param $orderByss
     * @return string
     */
    protected function buildOrderBy($orderByss){
        $orders = [];
        foreach ($orderByss as $orderBys) {
            foreach ($orderBys as $k => $orderBy){
                if (is_int($k) && strtoupper($orderBy) == 'RAND'){
                    $orders[] = 'RAND()';
                    continue;
                }
                if (is_string($k) && in_array($k, $this->doubles)){
                    $table = $k;
                } else if (isset($this->doubles[$k])){
                    $table = $this->doubles[$k];
                } else if (is_string($k)) {
                    $k = $this->real_escape_string($k);
                    //外层循环
                    if (is_array($orderBy)){
                        // order by field()
                        foreach ($orderBy as &$v){
                            $v = $this->real_escape_string($v);
                        }
                        unset($v);
                        $val = join("','", $orderBy);
                        $orders[] = "FIELD(`$k`,'$val')";
                    } else {
                        if (!in_array(strtoupper($orderBy), ['ASC', 'DESC'])){
                            Logger::error("order must be ASC/DESC, {$orderBy} given", 'sql Error');
                            continue;
                        }
                        $orders[] = '`'.$k."` ".$orderBy;
                    }
                    continue;
                } else {
                    continue;
                }
                foreach ($orderBy as $key => $val){
                    $key = $this->real_escape_string($key);
                    if (is_array($val)){
                        $field = $table.".`".$key.'`';
                        // order by field()
                        foreach ($val as &$v){
                            $v = $this->real_escape_string($v);
                        }
                        unset($v);
                        $val = join("','", $val);
                        $orders[] = "FIELD($field,'$val')";
                    } else {
                        if (!in_array(strtoupper($val), ['ASC', 'DESC'])){
                            Logger::error("order must be ASC/DESC, {$val} given", 'sql Error');
                            continue;
                        }
                        $orders[] = $table.".`".$key."` ".$val;
                    }
                }
            }
        }
        if ($orders){
            return ' ORDER BY '.join(',', $orders);
        } else {
            return '';
        }
    }

    /**
     * 组合groupBy
     * @param $groupBy
     * @param array $having
     * @return string
     */
    protected function buildGroupBy($groupBy, $having=[])
    {
        if (!$groupBy){
            return '';
        }
        if (is_array($groupBy)){
            $temps = [];
            foreach ($groupBy as $k => $group){
                if (is_string($k) && in_array($k, $this->doubles)){
                    $table = $k;
                } else if (isset($this->doubles[$k])){
                    $table = $this->doubles[$k];
                } else {
                    continue;
                }
                if (is_array($group)){
                    foreach ($group as $column){
                        if ($column instanceof \stdClass){
                            $temps[] = $column->scalar;
                        } else {
                            $column = $this->real_escape_string($column);
                            $temps[] = $table.".`".$column."`";
                        }
                    }
                } elseif ($group instanceof \stdClass) {
                    $temps[] = $group->scalar;
                } else {
                    $column = $this->real_escape_string($group);
                    $temps[] = $table.".`".$column."`";
                }
            }
            $groupBy = join(',', $temps);
        }
        if ($having){
            $havings = [];
            foreach ($having as $ys => $value){
                if (!in_array(strtolower($ys), $this->extracts)){
                    continue;
                }
                foreach ($value as $arrk => $arrv){
                    $arrk = $this->real_escape_string($arrk);
                    if (is_null($arrv)){
                        $havings[] = "`{$arrk}`{$ys} NULL";
                    }elseif (is_string($arrv)){
                        $arrv = $this->real_escape_string($arrv);
                        $havings[] = "`{$arrk}`{$ys}'{$arrv}'";
                    } else {
                        $havings[] = "`{$arrk}`{$ys}{$arrv}";
                    }
                }
            }
            if ($havings){
                $groupBy .= " HAVING ".join(' AND ', $havings);
            }
        }
        return ' GROUP BY '.$groupBy;
    }

    /**
     * 拼装Limit
     * @param $limit
     * @param bool $update
     * @return string
     */
    protected function buildLimit($limit, $update=false)
    {
        if (empty($limit)) {
            return '';
        } else if ($update){
            return sprintf(' LIMIT %d', $limit[1]);
        } else {
            return sprintf(' LIMIT %d,%d', $limit[0], $limit[1]);
        }
    }

    /**
     * 拼装Sets
     * @param $set
     * @return string
     */
    protected function buildSets($set)
    {
        $doubles = $this->doubles;
        $sets = [];
        foreach ($set as $k => $val){
            if (is_string($k) && in_array($k, $doubles)){
                $table = $k;
            } else if (isset($doubles[$k])){
                $table = $doubles[$k];
            } else {
                continue;
            }
            foreach($val as $key => $value) {
                $key = $this->real_escape_string($key);
                if (is_array($value)){
                    $k = array_keys($value)[0];
                    if (!in_array($k, $this->setOps)){
                        continue;
                    }
                    $val = array_values($value)[0];
                    if (is_array($val)){
                        $arr = [];
                        foreach ($val as $v){
                            if (is_numeric($v)) {
                                $arr[] = $v;
                            } else if (strpos($v, '.', 1)) {
                                $ts = explode('.', $v);
                                $arr[] = '`' . $this->real_escape_string($ts[0]) . '`.`' . $this->real_escape_string($ts[1]) . '`';
                            } else {
                                $arr[] = "`{$table}`.`" . $this->real_escape_string($v) . '`';
                            }
                        }
                        $sets[] = "`{$table}`.`{$key}`= ".join($k, $arr);
                    } else {
                        $val = intval($val);
                        $sets[] = "`{$table}`.`{$key}`= `{$table}`.`{$key}` {$k} {$val}";
                    }
                } else if ($value === null) {
                    $sets[] = "`{$table}`.`{$key}`=NULL";
                } else if (is_string($value)) {
                    $value = $this->real_escape_string($value);
                    $sets[] = "`{$table}`.`{$key}`='{$value}'";
                } else if ($value instanceof \stdClass) {
                    $sets[] = "`{$table}`.`{$key}`={$value->scalar}";
                } else {
                    $sets[] = "`{$table}`.`{$key}`={$value}";
                }
            }
        }
        return join(', ', $sets);
    }

    /**
     * count=count+1
     * @param $set
     * @return string
     */
    protected function buildCount($set)
    {
        $doubles = $this->doubles;
        $sets = [];
        foreach ($set as $k => $val){
            if (is_string($k) && in_array($k, $doubles)){
                $table = $k;
            } else if (isset($doubles[$k])){
                $table = $doubles[$k];
            } else {
                continue;
            }
            foreach($val as $key => $value) {
                if (!is_int($value) || $value == 0) {
                    continue;
                }
                $key = $this->real_escape_string($key);
                $sets[] = "`{$table}`.`{$key}`=`{$table}`.`{$key}`+ {$value}";
            }
        }
        return join(', ', $sets);
    }

    /**
     * 附加on条件
     * @param $conds
     * @return $this
     */
    public function on($conds=[])
    {
        $tmp = &$this->relates[count($this->relates)-1][1];
        foreach ($conds as $k => $relate){
            if (is_string($k) && in_array($k, $this->doubles)){
                $table = $k;
            } else if (isset($this->doubles[$k])){
                $table = $this->doubles[$k];
            } else {
                continue;
            }
            foreach ($relate as $key => $value){
                $key = "`{$this->real_escape_string($table)}`.`{$this->real_escape_string($key)}`";
                if (is_array($value) && count($value)>=2 && in_array($value[0], $this->extracts)){
                    if ($value[1] instanceof \stdClass){
                        $tmp[] = [$key, [$value[0], $value[1]->scalar]];
                    } else {
                        $tmp[] = [$key, [$value[0], "'{$this->real_escape_string($value[1])}'"]];
                    }
                } elseif (is_array($value)) {
                    foreach ($value as &$v){
                        $v = "'{$this->real_escape_string($v)}'";
                    }
                    unset($v);
                    $tmp[] = array($key, array('in', "(".join(',', $value).')'));
                } elseif ($value instanceof \stdClass) {
                    $tmp[] = [$key, $value->scalar];
                } else {
                    $tmp[] = [$key, "'{$this->real_escape_string($value)}'"];
                }
            }
        }
        unset($tmp);
        return $this;
    }

    /**
     * and 操作
     * @param $cond
     * @return DoubleFilter
     */
    public function filter($cond=[])
    {
        return $cond ? new DoubleFilter($this, $cond, "__and__") : $this;
    }

    /**
     * or 操作
     * @param $cond
     * @return DoubleFilter
     */
    public function merge($cond=[])
    {
        return $cond ? new DoubleFilter($this, $cond, "__or__") : $this;
    }

    /**
     * 条件预设
     * @param $cond
     * @return DoubleFilter
     */
    public function setCond($cond)
    {
        return new DoubleFilter($this, [], "__and__", $cond);
    }
}