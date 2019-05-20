<?php
/**
 * Tencent is pleased to support the open source community by making Biny available.
 * Copyright (C) 2017 THL A29 Limited, a Tencent company. All rights reserved.
 * Licensed under the BSD 3-Clause License (the "License"); you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * https://opensource.org/licenses/BSD-3-Clause
 * Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
 */

namespace biny\lib;
use App;

/**
 * 数据库
 * @method SingleCond limit($len, $start=0)
 * @method SingleCond group($groupby)
 * @method SingleCond having($having)
 * @method SingleCond order($orderby)
 * @method SingleCond addition($additions)
 */
class SingleDAO extends DAO
{
    /**
     * 表格名称
     * @var string
     */
    protected $table;
    private $calledClass;

    private $dbTable;

    private $database = null;

    public function __construct($table=null, $called=null)
    {
        if ($table) $this->table = $table;
        if ($called) {
            $this->calledClass = $called;
        } else {
            $name = explode('\\', get_called_class());
            $this->calledClass = substr(end($name), 0, -3);
        }
        $this->setDbTable($table ?: $this->table);
    }

    public function setDbTable($table)
    {
        if (null === $this->database){
            if (is_string($this->dbConfig) && $db = App::$base->app_config->get($this->dbConfig, 'dns')['database']){
                $this->database = $db;
            } else if (is_array($this->dbConfig)){
                $master = App::$base->app_config->get($this->dbConfig[0], 'dns')['database'];
                $slave = App::$base->app_config->get($this->dbConfig[1], 'dns')['database'];
                if ($master === $slave){
                    $this->database = $master;
                } else {
                    throw new BinyException(3008, [$slave, $master]);
                }
            }
        }
        $this->dbTable = $this->database.".`{$table}`";
    }

    /**
     * 获取预设类
     * @return null
     */
    public function getCalledClass()
    {
        return $this->calledClass;
    }

    /**
     * 返回Log
     * @return string
     */
    public function getDAO()
    {
        return $this->dbTable;
    }

    /**
     * 返回表名
     * @return string
     */
    public function getTable()
    {
        return $this->dbTable;
    }

    /**
     * 纯表名
     * @return string
     */
    public function tableName()
    {
        return $this->table;
    }

    /**
     * 选分表
     * @param $id
     * @return $this
     */
    public function choose($id)
    {
        $this->setDbTable($this->table.'_'.$id);
        return $this;
    }

    /**
     * 表是否存在
     * @return bool
     */
    public function exist()
    {
        return $this->isExist();
    }

    /**
     * 链接表
     * @param $dao SingleDAO
     * @param $relate
     * @param string $type
     * @return $this|DoubleDAO
     * @throws BinyException
     */
    protected function _join($dao, $relate, $type='join')
    {
        $selfClass = $this->getCalledClass();
        $relateClass = $dao->getCalledClass();
        if ($selfClass == $relateClass){
            $relateClass .= '1';
        }
        if (!$this->checkConfig($dao)){
            throw new BinyException(3002, "DAOs must be the same Host");
        }
        $DAOs = [
            $selfClass => $this->dbTable,
            $relateClass => $dao->dbTable
        ];
        $relates = [];
        $join = [];
        foreach ($relate as $key => $value){
            $key = "`{$this->real_escape_string($selfClass)}`.`{$this->real_escape_string($key)}`";
            if (is_array($value) && count($value)>=2 && in_array($value[0], $this->extracts)){
                $join[] = [$key, [$value[0], "`{$this->real_escape_string($relateClass)}`.`{$this->real_escape_string($value[1])}`"]];
            } else {
                $join[] = [$key, "`{$this->real_escape_string($relateClass)}`.`{$this->real_escape_string($value)}`"];
            }
        }
        $relates[] = [$type, $join];
        return new DoubleDAO($DAOs, $relates, $this->dbConfig);
    }

    /**
     * buildWhere
     * @param $cond
     * @param string $type
     * @return string
     */
    public function buildWhere($cond, $type='and')
    {
        if (empty($cond)) {
            return '';
        } else {
            $where = [];
            foreach($cond as $key => $value) {
                $key = $this->real_escape_string($key);
                if (in_array(strtolower($key), $this->extracts)){
                    foreach ($value as $arrk => $arrv){
                        $arrk = $this->real_escape_string($arrk);
                        if (is_null($arrv)){
                            $where[] = "`{$arrk}`{$key} NULL";
                        } elseif (is_string($arrv)){
                            $arrv = $this->real_escape_string($arrv);
                            $where[] = "`{$arrk}`{$key}'{$arrv}'";
                        } elseif ($arrv instanceof \stdClass){
                            $where[] = "`{$arrk}`{$key}{$arrv->scalar}";
                        } else if (is_array($arrv)){
                            foreach ($arrv as $av){
                                $arrv = $this->real_escape_string($av);
                                $where[] = "`{$arrk}`{$key}'{$arrv}'";
                            }
                        } else {
                            $where[] = "`{$arrk}`{$key}{$arrv}";
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
                                $where[] = "`{$arrk}` like '{$like}'";
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
                            $where[] = "`{$arrk}` like '{$arrv}'";
                        }
                    }
                } elseif (is_null($value)){
                    $where[] = "`{$key}`is NULL";
                } elseif ($value instanceof \stdClass){
                    $where[] = "`{$key}`={$value->scalar}";
                } elseif (is_string($value)) {
                    $value = $this->real_escape_string($value);
                    $where[] = "`{$key}`='{$value}'";
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
                    $value = "(". join(',', $value).")";
                    $where[] = "`{$key}` in {$value}";
                } else {
                    $where[] = "`{$key}`={$value}";
                }
            }

            return join(" {$type} ", $where);
        }
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
     * 拼接fields
     * @param $fields
     * @param array $group
     * @return string
     * @throws BinyException
     */
    protected function buildFields($fields, $group=[]){
        if (is_array($fields)){
            foreach ($fields as $key => &$field){
                if (is_int($key)){
                    $field = '`'.$this->real_escape_string($field).'`';
                } elseif ($field instanceof \stdClass) {
                    $field = $field->scalar;
                } else {
                    $field = "`{$this->real_escape_string($key)}` AS `{$this->real_escape_string($field)}`";
                }
            }
            unset($field);
            $fields = join(',', $fields);
        }
        if ($group){
            if ($fields){
                $groups = [$fields];
            } else {
                $groups = [];
            }
            foreach ($group as $key => $values){
                if (!in_array(strtolower($key), $this->calcs)){
                    throw new BinyException(3011, [$key]);
                }
                $calc = $key == 'distinct' ? "COUNT(DISTINCT " : "{$key}(";
                if (is_string($values)){
                    $values = $values === '*' ? $values : '`'.$this->real_escape_string($values).'`';
                    $groups[] = $calc."{$values}) as '{$key}'";
                    continue;
                } else if ($values instanceof \stdClass){
                    $groups[] = $calc."{$values->scalar}) as '{$key}'";
                    continue;
                }
                foreach ($values as $k => $value){
                    $value = $this->real_escape_string($value);
                    if (is_string($k)){
                        $k = $this->real_escape_string($k);
                        $groups[] = $calc."`{$k}`) as '{$value}'";
                    } else {
                        $groups[] = $calc."`{$value}`) as '{$value}'";
                    }
                }
            }
            return join(',', $groups);
        }
        return $fields ?: "*";
    }

    /**
     * 拼装Sets
     * @param $set
     * @return string
     */
    protected function buildSets($set)
    {
        $sets = [];
        foreach($set as $key => $value) {
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
                        if (is_numeric($v)){
                            $arr[] = $v;
                        } else {
                            $arr[] = '`'.$this->real_escape_string($v).'`';
                        }
                    }
                    $sets[] = "`{$key}`= ".join($k, $arr);
                } else {
                    $val = intval($val);
                    $sets[] = "`{$key}`= `{$key}` {$k} {$val}";
                }
            } else if ($value === null) {
                $sets[] = "`{$key}`=NULL";
            } else if (is_string($value)) {
                $value = $this->real_escape_string($value);
                $sets[] = "`{$key}`='{$value}'";
            } else if ($value instanceof \stdClass) {
                $sets[] = "`{$key}`= {$value->scalar}";
            } else {
                $sets[] = "`{$key}`={$value}";
            }

        }
        return join(', ', $sets);
    }

    /**
     * 可用update替代
     * count=count+1
     * @param $set
     * @return string
     */
    protected function buildCount($set)
    {
        $sets = [];
        foreach($set as $key => $value) {
            if (!is_numeric($value) || $value == 0 ) {
                continue;
            }
            $key = $this->real_escape_string($key);
            $sets[] = "`{$key}`=`{$key}`+ {$value}";
        }
        return join(', ', $sets);
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
            foreach ($groupBy as &$group){
                if ($group instanceof \stdClass){
                    $group = $group->scalar;
                } else {
                    $group = '`'.$this->real_escape_string($group).'`';
                }
            }
            unset($group);
            $groupBy = join(',', $groupBy);
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
     * 拼装Insert
     * @param $sets
     * @internal param $data
     * @return string $fileds
     */
    protected function buildInsert($sets)
    {
        $field = [];
        $value = [];
        foreach ($sets as $key => $val){
            $field[] = "`{$this->real_escape_string($key)}`";
            if ($val === null) {
                $value[] = "NULL";
            } elseif (is_string($val)) {
                $val = $this->real_escape_string($val);//mysqli_real_escape_string(null, $val);
                $value[] = "'{$val}'";
            } else {
                $value[] = "{$val}";
            }
        }
        $fields = '('.join(',', $field).') VALUES('.join(',', $value).')';
        return $fields;
    }

    /**
     * 拼装orderby ['id'=>'ASC', 'name'=>[1,2,3,4], 'rand']
     * @param $orderBys
     * @return string
     */
    protected function buildOrderBy($orderBys)
    {
        $orders = [];
        foreach ($orderBys as $orderBy){
            foreach ($orderBy as $key => $val){
                if (is_int($key) && strtoupper($val) == 'RAND'){
                    $orders[] = 'RAND()';
                    continue;
                }
                $key = $this->real_escape_string($key);
                if (is_array($val)){
                    // order by field()
                    foreach ($val as &$v){
                        $v = $this->real_escape_string($v);
                    }
                    unset($v);
                    $val = join("','", $val);
                    $orders[] = "FIELD(`$key`,'$val')";
                } else {
                    if (!in_array(strtoupper($val), ['ASC', 'DESC'])){
                        Logger::error("order must be ASC/DESC, {$val} given", 'sql Error');
                        continue;
                    }
                    $orders[] = '`'.$key."` ".$val;
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
     * 获取所有表名
     * @param bool $detail
     * @return array|bool
     * @throws BinyException
     */
    public function tables($detail=false)
    {
        $field = $detail ? '*' : 'TABLE_NAME';
        $sql = "SELECT $field FROM information_schema.tables WHERE TABLE_SCHEMA='{$this->database}'";
        $result = $this->sql($sql);
        return $detail ? $result : array_column($result, 'TABLE_NAME');
    }

    /**
     * 获取表字段
     * @param bool $detail
     * @return array|bool
     * @throws BinyException
     */
    public function columns($detail=false)
    {
        $field = $detail ? '*' : 'COLUMN_NAME';
        $sql = "SELECT $field FROM information_schema.columns WHERE TABLE_SCHEMA = '{$this->database}' and TABLE_NAME = '{$this->table}'";
        $result = $this->sql($sql);
        return $detail ? $result : array_column($result, 'COLUMN_NAME');
    }

    /**
     * 添加数据 等同于add
     * @param $sets
     * @param bool $id
     * @return int
     */
    public function insert($sets, $id=true)
    {
        return $this->add($sets, $id);
    }

    /**
     * 添加数据
     * @param $sets
     * @param bool $id
     * @return int
     */
    public function add($sets, $id=true)
    {
        $fields = $this->buildInsert($sets);
        $sql = sprintf("INSERT INTO %s %s", $this->dbTable, $fields);
        return $this->execute($sql, $id);
    }

    /**
     * 等同于addList
     * @param $values
     * @param int $max
     * @param null $replace
     * @return bool|int|\mysqli_result|string
     */
    public function insertList($values, $max=100, $replace=null)
    {
        return $this->addList($values, $max, $replace);
    }

    /**
     * 批量添加
     * @param $values
     * @param int $max
     * @param bool $replace
     * @return bool|int|\mysqli_result|string
     */
    public function addList($values, $max=100, $replace=null)
    {
        if (count($values) == 0){
            return true;
        }
        $command = is_null($replace) ? "INSERT" : ($replace ? "REPLACE" : "INSERT IGNORE");
        $value = $values[0];
        $fields = array_keys($value);
        foreach ($fields as &$field){
            $field = $this->real_escape_string($field);
        }
        unset($field);
        $fields = '(`'.join('`,`', $fields).'`)';

        $columns = [];
        $i = 0;
        $flag = true;
        foreach ($values as $value){
            foreach ($value as &$val){
                if ($val === null) {
                    $val = "NULL";
                } else {
                    $val = is_string($val) ? "'{$this->real_escape_string($val)}'" : $val;
                }
            }
            unset($val);
            $columns[] = '('.join(',', $value).')';
            if (++$i == $max){
                $values = join(',', $columns);
                $columns = []; $i = 0;
                $sql = sprintf("$command INTO %s %s VALUES  %s", $this->dbTable, $fields, $values);
                if (!$this->execute($sql, false)){
                    $flag = false;
                }
            }
        }
        if ($columns){
            $values = join(',', $columns);
            $sql = sprintf("$command INTO %s %s VALUES  %s", $this->dbTable, $fields, $values);
            if (!$this->execute($sql, false)){
                $flag = false;
            }
        }
        return $flag;
    }

    /**
     * 删除数据
     * @return bool
     */
    public function delete()
    {
        $params = func_get_args();
        $where = isset($params[0]) && $params[0]->get('where') ? " WHERE ".$params[0]->get('where') : "";
        $sql = sprintf("DELETE FROM %s%s", $this->dbTable, $where);

        return $this->execute($sql);
    }

    /**
     * 更新数据或者插入数据
     * @param $inserts
     * @param $sets
     * @return bool|int|\mysqli_result|string
     */
    public function createOrUpdate($inserts, $sets=[])
    {
        $set = $this->buildSets($sets ?: $inserts);
        $fields = $this->buildInsert($inserts);
        $sql = sprintf("INSERT INTO %s %s ON DUPLICATE KEY UPDATE %s", $this->dbTable, $fields, $set);

        return $this->execute($sql, true);
    }

    /**
     * 更新数据或者加1
     * @param $inserts
     * @param $sets ['num'=>2]
     * @return bool|int|\mysqli_result|string
     */
    public function createOrAdd($inserts, $sets)
    {
        Logger::addError('function createOrAdd is deprecated and will be removed in the future. use createOrUpdate instead', 'deprecated', WARNING);
        $set = $this->buildCount($sets);
        $fields = $this->buildInsert($inserts);
        $sql = sprintf("INSERT INTO %s %s ON DUPLICATE KEY UPDATE %s", $this->dbTable, $fields, $set);

        return $this->execute($sql, true);
    }


    /**
     * and 操作
     * @param $cond
     * @return SingleFilter
     */
    public function filter($cond=[])
    {
        return $cond ? new SingleFilter($this, $cond, "__and__") : $this;
    }

    /**
     * or 操作
     * @param $cond
     * @return SingleFilter
     */
    public function merge($cond=[])
    {
        return $cond ? new SingleFilter($this, $cond, "__or__") : $this;
    }
}