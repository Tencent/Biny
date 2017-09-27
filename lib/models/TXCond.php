<?php
/**
 * Created by PhpStorm.
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
class TXCond
{
    protected $DAO;
    protected $where;
    protected $limit=array();
    protected $orderby=array();
    protected $additions=array();
    protected $groupby=array();
    protected $having=array();

    protected $methods = ['distinct', 'find', 'count', 'update', 'addCount'];
    protected $calcs = ['max', 'min', 'sum', 'avg', 'count'];

    /**
     * 构造函数
     * @param TXDAO $DAO
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
     * @throws TXException
     */
    public function __call($method, $args)
    {
        if (in_array($method, $this->methods) || in_array($method, $this->calcs)){
            $args = $args ? $args : [''];
            $args[] = $this;
            return call_user_func_array([$this->DAO, $method], $args);
        } else {
            throw new TXException(3009, array($method, __CLASS__));
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
     * cursor
     * @param string $field
     * @return array
     */
    public function cursor($field='')
    {
        return $this->DAO->cursor($field, $this);
    }

    /**
     * select
     * @param $sql
     * @param array $querys
     * @param int $mode
     * @return array
     */
    public function select($sql, $querys=array(), $mode=TXDatabase::FETCH_TYPE_ALL)
    {
        return $this->DAO->select($sql, $querys, $mode, $this);
    }

    /**
     * command
     * @param $sql
     * @param $querys
     * @return array
     */
    public function command($sql, $querys=array())
    {
        return $this->DAO->command($sql, $querys, $this);
    }

    /**
     * 调试专用
     */
    public function __toLogger()
    {
        return array(
            'DAO' => $this->DAO->getDAO(),
            'where' => $this->where,
            'limit' => $this->limit,
            'orderby' => $this->orderby,
            'additions' => $this->additions,
            'groupby' => $this->groupby,
            'having' => $this->having,
        );
    }
}