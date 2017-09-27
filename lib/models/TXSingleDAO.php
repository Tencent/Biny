<?php
/**
 * 数据库
 * @method TXSingleCond limit($len, $start=0)
 * @method TXSingleCond group($groupby)
 * @method TXSingleCond having($having)
 * @method TXSingleCond order($orderby)
 * @method TXSingleCond addition($additions)
 */
class TXSingleDAO extends TXDAO
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
        if ($called) $this->calledClass = $called;
        $this->setDbTable($table ?: $this->table);
    }

    public function setDbTable($table)
    {
        if (null === $this->database){
            if (is_string($this->dbConfig) && $db = TXConfig::getAppConfig($this->dbConfig, 'dns')['database']){
                $this->database = $db;
            } else if (is_array($this->dbConfig)){
                $master = TXConfig::getAppConfig($this->dbConfig[0], 'dns')['database'];
                $slave = TXConfig::getAppConfig($this->dbConfig[1], 'dns')['database'];
                if ($master === $slave){
                    $this->database = $master;
                } else {
                    throw new TXException(3008, array($slave, $master));
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
     * @param $dao
     * @param $relate
     * @param string $type
     * @return $this|TXDoubleDAO
     * @throws TXException
     */
    protected function _join($dao, $relate, $type='join')
    {
        $selfClass = substr($this->getCalledClass() ?: get_called_class(), 0, -3);
        $relateClass = substr($dao->getCalledClass() ?: get_class($dao), 0, -3);
        if ($selfClass == $relateClass){
            return $this;
        }
        if (!$this->checkConfig($dao)){
            throw new TXException(3002, "DAOs must be the same Host");
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
        return new TXDoubleDAO($DAOs, $relates, $this->dbConfig);
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
            $where = array();
            foreach($cond as $key => $value) {
                $key = $this->real_escape_string($key);
                if (in_array(strtolower($key), $this->extracts)){
                    foreach ($value as $arrk => $arrv){
                        $arrk = $this->real_escape_string($arrk);
                        if (is_null($arrv)){
                            $where[] = "`{$arrk}`{$key} NULL";
                        }elseif (is_string($arrv)){
                            $arrv = $this->real_escape_string($arrv);
                            $where[] = "`{$arrk}`{$key}'{$arrv}'";
                        }  else if (is_array($arrv)){
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
     * @return string
     */
    protected function buildLimit($limit)
    {
        if (empty($limit)) {
            return '';
        } else {
            return sprintf(' LIMIT %d,%d', $limit[0], $limit[1]);
        }
    }

    /**
     * 拼接fields
     * @param $fields
     * @param array $group
     * @return string
     * @throws TXException
     */
    protected function buildFields($fields, $group=array()){
        if (is_array($fields)){
            foreach ($fields as $key => &$field){
                if (is_int($key)){
                    $field = '`'.$this->real_escape_string($field).'`';
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
                    throw new TXException(3011, array($key));
                }
                foreach ($values as $k => $value){
                    $value = $this->real_escape_string($value);
                    if (is_string($k)){
                        $k = $this->real_escape_string($k);
                        if ($key == 'distinct'){
                            $groups[] = "COUNT(DISTINCT `{$k}`) as '{$value}'";
                        } else {
                            $groups[] = "{$key}(`{$k}`) as '{$value}'";
                        }
                    } else {
                        if ($key == 'distinct'){
                            $groups[] = "COUNT(DISTINCT `{$value}`) as '{$value}'";
                        } else {
                            $groups[] = "{$key}(`{$value}`) as '{$value}'";
                        }
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
        $sets = array();
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
        $sets = array();
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
    protected function buildGroupBy($groupBy, $having=array())
    {
        if (!$groupBy){
            return '';
        }
        if (is_array($groupBy)){
            foreach ($groupBy as &$group){
                $group = $this->real_escape_string($group);
            }
            unset($group);
            $groupBy = '`'.join('`,`', $groupBy).'`';
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
        $field = array();
        $value = array();
        foreach ($sets as $key => $val){
            $field[] = "`{$this->real_escape_string($key)}`";
            if ($val === null) {
                $value[] = "NULL";
            }
            elseif (is_string($val)) {
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
     * 拼装orderby
     * @param $orderBy
     * @return string
     */
    protected function buildOrderBy($orderBy)
    {
        $orders = array();
        foreach ($orderBy as $key => $val){
            $key = $this->real_escape_string($key);
            if (is_array($val)){
                $asc = isset($val[0]) ? $val[0] : 'ASC';
                $code = isset($val[1]) ? $val[1] : 'gbk';
                if (!in_array(strtoupper($asc), array('ASC', 'DESC'))){
                    TXLogger::error("order must be ASC/DESC, {$asc} given", 'sql Error');
                    continue;
                }
                $orders[] = "CONVERT(`{$key}` USING {$code}) $asc";
            } else {
                if (!in_array(strtoupper($val), array('ASC', 'DESC'))){
                    TXLogger::error("order must be ASC/DESC, {$val} given", 'sql Error');
                    continue;
                }
                $orders[] = '`'.$key."` ".$val;
            }
        }
        if ($orders){
            return ' ORDER BY '.join(',', $orders);
        } else {
            return '';
        }
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
        TXEvent::trigger(onSql, [$sql]);
        return $this->execute($sql, $id);
    }

    /**
     * 批量添加
     * @param $values
     * @param int $max
     * @return bool|int|mysqli_result|string
     */
    public function addList($values, $max=100)
    {
        if (count($values) == 0){
            return true;
        }
        $value = $values[0];
        $fields = array_keys($value);
        foreach ($fields as &$field){
            $field = $this->real_escape_string($field);
        }
        unset($field);
        $fields = '(`'.join('`,`', $fields).'`)';

        $columns = array();
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
                $sql = sprintf("INSERT INTO %s %s VALUES  %s", $this->dbTable, $fields, $values);
                TXEvent::trigger(onSql, [$sql]);
                if (!$this->execute($sql, false)){
                    $flag = false;
                }
            }
        }
        if ($columns){
            $values = join(',', $columns);
            $sql = sprintf("INSERT INTO %s %s VALUES  %s", $this->dbTable, $fields, $values);
            TXEvent::trigger(onSql, [$sql]);
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
        TXEvent::trigger(onSql, [$sql]);

        return $this->execute($sql);
    }

    /**
     * 更新数据或者插入数据
     * @param $inserts
     * @param $sets
     * @return bool|int|mysqli_result|string
     */
    public function createOrUpdate($inserts, $sets=array())
    {
        $set = $this->buildSets($sets ?: $inserts);
        $fields = $this->buildInsert($inserts);
        $sql = sprintf("INSERT INTO %s %s ON DUPLICATE KEY UPDATE %s", $this->dbTable, $fields, $set);
        TXEvent::trigger(onSql, [$sql]);

        return $this->execute($sql, true);
    }

    /**
     * 更新数据或者加1
     * @param $inserts
     * @param $sets ['num'=>2]
     * @return bool|int|mysqli_result|string
     */
    public function createOrAdd($inserts, $sets)
    {
        TXLogger::addError('function createOrAdd is deprecated and will be removed in the future. use createOrUpdate instead', 'deprecated', WARNING);
        $set = $this->buildCount($sets);
        $fields = $this->buildInsert($inserts);
        $sql = sprintf("INSERT INTO %s %s ON DUPLICATE KEY UPDATE %s", $this->dbTable, $fields, $set);
        TXEvent::trigger(onSql, [$sql]);

        return $this->execute($sql, true);
    }


    /**
     * and 操作
     * @param $cond
     * @return TXSingleFilter
     */
    public function filter($cond=array())
    {
        return $cond ? new TXSingleFilter($this, $cond, "__and__") : $this;
    }

    /**
     * or 操作
     * @param $cond
     * @return TXSingleFilter
     */
    public function merge($cond=array())
    {
        return $cond ? new TXSingleFilter($this, $cond, "__or__") : $this;
    }
}