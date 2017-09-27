<?php
/**
 * 数据库
 * @method int sum($field)
 * @method int max($field)
 * @method int min($field)
 * @method int avg($field)
 */
class TXDAO
{
    protected $extracts = ['=', '>', '>=', '<', '<=', '!=', '<>', 'is', 'is not'];
    protected $calcs = ['max', 'min', 'sum', 'avg', 'count', 'distinct'];
    protected $methods = ['group', 'limit', 'order', 'addition', 'having'];
    protected $setOps = ['+', '-', '*', '/'];

    protected $dbConfig = 'database';

    /**
     * @return string
     */
    public function getDbConfig()
    {
        return $this->dbConfig;
    }

    /**
     * 判断是否可以合表
     * @param $dao TXDAO
     * @return bool
     */
    protected function checkConfig($dao)
    {
        $tMaster = is_array($this->dbConfig) ? $this->dbConfig[0] : $this->dbConfig;
        $tSlave = is_array($this->dbConfig) ? $this->dbConfig[1] : $this->dbConfig;
        $dDbConfig = $dao->getDbConfig();
        $dMaster = is_array($dDbConfig) ? $dDbConfig[0] : $dDbConfig;
        $dSlave = is_array($dDbConfig) ? $dDbConfig[1] : $dDbConfig;
        if ($tMaster === $dMaster && $tSlave === $dSlave){
            return true;
        }
        if ($tMaster !== $dMaster){
            $tConfig = TXConfig::getAppConfig($tMaster, 'dns');
            $dConfig = TXConfig::getAppConfig($dMaster, 'dns');
            unset($tConfig['database']);
            unset($dConfig['database']);
            if (array_diff($tConfig, $dConfig)){
                return false;
            }
        } else if ($tSlave !== $dSlave){
            $tConfig = TXConfig::getAppConfig($tSlave, 'dns');
            $dConfig = TXConfig::getAppConfig($dSlave, 'dns');
            unset($tConfig['database']);
            unset($dConfig['database']);
            if (array_diff($tConfig, $dConfig)){
                return false;
            }
        }
        return true;
    }

    /**
     * 左联接
     * @param $dao
     * @param $relate
     * @return $this|TXDoubleDAO
     */
    public function leftJoin($dao, $relate)
    {
        return $this->_join($dao, $relate, 'LEFT JOIN');
    }

    /**
     * 右联接
     * @param $dao
     * @param $relate
     * @return $this|TXDoubleDAO
     */
    public function rightJoin($dao, $relate)
    {
        return $this->_join($dao, $relate, 'RIGHT JOIN');
    }

    /**
     * 联接
     * @param $dao
     * @param $relate
     * @return $this|TXDoubleDAO
     */
    public function join($dao, $relate)
    {
        return $this->_join($dao, $relate, 'JOIN');
    }

    /**
     * 存在表
     * @return bool
     */
    protected function isExist()
    {
        List($db, $table) = explode('.', $this->getTable());
        $sql = sprintf("select table_name from INFORMATION_SCHEMA.TABLES where TABLE_SCHEMA='%s' and TABLE_NAME='%s' ;", $db, trim($table, '`'));
        $result = $this->sql($sql);
        return count($result) ? true : false;
    }

    /**
     * 执行sql
     * @param $sql
     * @param bool $id
     * @return bool|int|mysqli_result|string
     */
    protected function execute($sql, $id=false) {
        $dns = is_array($this->dbConfig) ? $this->dbConfig[0] : $this->dbConfig;
        return TXDatabase::instance($dns)->execute($sql, $id);
    }

    /**
     * 从库查询SQL
     * @param $sql
     * @param $key
     * @param int $mode
     * @return array
     */
    private function sql($sql, $key=null, $mode=TXDatabase::FETCH_TYPE_ALL) {
        $dns = is_array($this->dbConfig) ? $this->dbConfig[1] : $this->dbConfig;
        return TXDatabase::instance($dns)->sql($sql, $key, $mode);
    }

    /**
     * 语句执行
     * @param $sql
     * @param array $querys
     * @param int $mode
     * @return array
     */
    public function select($sql, $querys=array(), $mode=TXDatabase::FETCH_TYPE_ALL)
    {
        $params = func_get_args();
        $cond = isset($params[3]) ? $params[3] : null;

        List($keys, $values) = $this->buildQuery($querys, $cond);
        $sql = str_replace($keys, $values, $sql);
        TXEvent::trigger(onSql, [$sql]);
        return $this->sql($sql, null, $mode);
    }

    /**
     * 语句执行
     * @param $sql
     * @param $querys
     * @return bool|int|mysqli_result|string
     */
    public function command($sql, $querys=array())
    {
        $params = func_get_args();
        $cond = isset($params[2]) ? $params[2] : null;

        List($keys, $values) = $this->buildQuery($querys, $cond);
        $sql = str_replace($keys, $values, $sql);
        TXEvent::trigger(onSql, [$sql]);
        return $this->execute($sql);
    }

    /**
     * 组合sql
     * @param $querys
     * @param $cond TXCond
     * @return array
     */
    private function buildQuery($querys, $cond)
    {
        $keys = $values = array();
        foreach ($querys as $k => $arg){
            $keys[] = ":$k";
            if (is_array($arg)){
                $karg = $arg;
                foreach ($arg as &$value){
                    $value = is_int($value) ? $value : "'{$this->real_escape_string($value, false)}'";
                }
                unset($value);
                $values[] = join(",", $arg);
                //keys
                $keys[] = ";$k";
                foreach ($karg as &$value){
                    $value = "`{$this->real_escape_string($value)}`";
                }
                $values[] = join(",", $karg);
            } else if (is_string($arg)){
                $values[] = "'{$this->real_escape_string($arg, false)}'";
                //keys
                $keys[] = ";$k";
                $values[] = "`{$this->real_escape_string($arg)}`";
            } else {
                $values[] = $arg;
            }
        }
        if (!in_array(":where", $keys) && $cond){
            $keys[] = ":where";
            $values[] = $cond->get('where');
        }
        if (!in_array(":table", $keys)){
            $keys[] = ":table";
            $values[] = $this->getTable();
        }
        if (!in_array(":order", $keys) && $cond){
            $keys[] = ":order";
            $values[] = $this->buildOrderBy($cond->get('orderby'));
        }
        if (!in_array(":group", $keys) && $cond){
            $keys[] = ":group";
            $values[] = $this->buildGroupBy($cond->get('groupby'), $cond->get('having'));
        }
        if (!in_array(":addition", $keys) && $cond){
            $keys[] = ":addition";
            $values[] = $this->buildFields('', $cond->get('additions'));
        }

        return array($keys, $values);
    }

    /**
     * real_escape_string
     * @param $string
     * @param bool $ignore
     * @return mixed|string
     */
    protected function real_escape_string($string, $ignore=true){
        $string = addslashes($string);
        return $ignore ? str_replace('`', '\`', $string) : $string;
    }

    /**
     * real_like_string\
     * @param $str
     * @return mixed
     */
    protected function real_like_string($str){
        return str_replace(["_", "%"], ["\\_", "\\%"], addslashes($str));
    }

    /**
     * 找单条数据
     * @param string $fields
     * @return array
     */
    public function find($fields='')
    {
        $params = func_get_args();
        $where = isset($params[1]) && $params[1]->get('where') ? " WHERE ".$params[1]->get('where') : "";
        $fields = $this->buildFields($fields, isset($params[1]) ? $params[1]->get('additions') : array());
        $sql = sprintf("SELECT %s FROM %s%s", $fields, $this->getTable(), $where);
        TXEvent::trigger(onSql, [$sql]);
        $result = $this->sql($sql, null, TXDatabase::FETCH_TYPE_ONE);
        return $result;
    }

    /**
     * 查询数据
     * @param string $fields
     * @param null $key
     * @return array
     */
    public function query($fields='', $key=null)
    {
        $params = func_get_args();
        $where = isset($params[2]) && $params[2]->get('where') ? " WHERE ".$params[2]->get('where') : "";
        $limit = $this->buildLimit(isset($params[2]) ? $params[2]->get('limit') : []);
        $orderBy = $this->buildOrderBy(isset($params[2]) ? $params[2]->get('orderby') : []);
        $fields = $this->buildFields($fields, isset($params[2]) ? $params[2]->get('additions') : []);
        $groupBy = $this->buildGroupBy(isset($params[2]) ? $params[2]->get('groupby') : [], isset($params[2]) ? $params[2]->get('having') : []);
        $sql = sprintf("SELECT %s FROM %s%s%s%s%s", $fields, $this->getTable(), $where, $groupBy, $orderBy, $limit);
        TXEvent::trigger(onSql, [$sql]);

        return $this->sql($sql, $key);
    }

    /**
     * 返回数据游标
     * @param string $fields
     * @return array
     */
    public function cursor($fields='')
    {
        $params = func_get_args();
        $where = isset($params[1]) && $params[1]->get('where') ? " WHERE ".$params[1]->get('where') : "";
        $limit = $this->buildLimit(isset($params[1]) ? $params[1]->get('limit') : []);
        $orderBy = $this->buildOrderBy(isset($params[1]) ? $params[1]->get('orderby') : []);
        $fields = $this->buildFields($fields, isset($params[1]) ? $params[1]->get('additions') : []);
        $groupBy = $this->buildGroupBy(isset($params[1]) ? $params[1]->get('groupby') : [], isset($params[1]) ? $params[1]->get('having') : []);
        $sql = sprintf("SELECT %s FROM %s%s%s%s%s", $fields, $this->getTable(), $where, $groupBy, $orderBy, $limit);
        TXEvent::trigger(onSql, [$sql]);

        return $this->sql($sql, null, TXDatabase::FETCH_TYPE_CURSOR);
    }

    /**
     * 查询不重复项
     * @param string $fields
     * @return array
     */
    public function distinct($fields='')
    {
        $params = func_get_args();
        $where = isset($params[1]) && $params[1]->get('where') ? " WHERE ".$params[1]->get('where') : "";
        $limit = $this->buildLimit(isset($params[1]) ? $params[1]->get('limit') : []);
        $orderBy = $this->buildOrderBy(isset($params[1]) ? $params[1]->get('orderby') : []);
        $fields = $this->buildFields($fields, isset($params[1]) ? $params[1]->get('additions') : []);
        $groupBy = $this->buildGroupBy(isset($params[1]) ? $params[1]->get('groupby') : [], isset($params[1]) ? $params[1]->get('having') : []);
        $sql = sprintf("SELECT DISTINCT %s FROM %s%s%s%s%s", $fields, $this->getTable(), $where, $groupBy, $orderBy, $limit);
        TXEvent::trigger(onSql, [$sql]);

        return $this->sql($sql);
    }

    /**
     * 计算数量
     * @param string $field
     * @return int
     */
    public function count($field='')
    {
        $params = func_get_args();
        $where = isset($params[1]) && $params[1]->get('where') ? " WHERE ".$params[1]->get('where') : "";
        $field = $field ? 'DISTINCT '.$this->buildFields($field) : '0';
        $groupBy = $this->buildGroupBy(isset($params[1]) ? $params[1]->get('groupby') : [], isset($params[1]) ? $params[1]->get('having') : []);
        $sql = sprintf("SELECT COUNT(%s) as count FROM %s%s%s", $field, $this->getTable(), $where, $groupBy);
        TXEvent::trigger(onSql, [$sql]);

        $ret = $this->sql($sql);
        return $ret[0]['count'] ?: 0;
    }

    /**
     * 更新数据
     * @param array $sets
     * @return bool
     */
    public function update($sets)
    {
        $params = func_get_args();
        $where = isset($params[1]) && $params[1]->get('where') ? " WHERE ".$params[1]->get('where') : "";
        $set = $this->buildSets($sets);
        $sql = sprintf("UPDATE %s SET %s%s", $this->getTable(), $set, $where);
        TXEvent::trigger(onSql, [$sql]);

        return $this->execute($sql);
    }

    /**
     * 添加数量 count=count+1
     * @param $sets
     * @return bool|string
     */
    public function addCount($sets)
    {
        TXLogger::addError('function addCount is deprecated and will be removed in the future. use update instead', 'deprecated', WARNING);
        $params = func_get_args();
        $where = isset($params[1]) && $params[1]->get('where') ? " WHERE ".$params[1]->get('where') : "";
        $set = $this->buildCount($sets);
        $sql = sprintf("UPDATE %s SET %s%s", $this->getTable(), $set, $where);
        TXEvent::trigger(onSql, [$sql]);
        return $this->execute($sql);
    }

    /**
     * 查询条件
     * @param $method ['max', 'min', 'sum', 'avg']
     * @param $args
     * @return mixed
     * @throws TXException
     */
    public function __call($method, $args)
    {
        if (in_array($method, $this->methods)){
            if ($this instanceof TXSingleDAO){
                $cond = new TXSingleCond($this);
            } else {
                $cond = new TXDoubleCond($this);
            }
            return call_user_func_array([$cond, $method], $args);
        } else if (in_array($method, $this->calcs)){
            $where = isset($args[1]) && $args[1]->get('where') ? " WHERE ".$args[1]->get('where') : "";
            $sql = sprintf("SELECT %s(`%s`) as `%s` FROM %s%s", $method, $args[0], $method, $this->getTable(), $where);
            TXEvent::trigger(onSql, [$sql]);

            $ret = $this->sql($sql, null, TXDatabase::FETCH_TYPE_ONE);
            return $ret[$method] ?: 0;
        } else {
            throw new TXException(3009, array($method, get_called_class()));
        }
    }
}