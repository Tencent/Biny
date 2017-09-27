<?php
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
    public function filter($cond=array())
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
     * 查询条件
     * @param $method
     * @param $args
     * @return TXSingleCond
     * @throws TXException
     */
    public function __call($method, $args)
    {
        if (in_array($method, $this->methods) || in_array($method, $this->calcs)){
            $cond = new TXSingleCond($this->DAO);
            $cond->setWhere($this->buildWhere($this->conds));
            return call_user_func_array([$cond, $method], $args);
        } else {
            throw new TXException(3009, array($method, __CLASS__));
        }
    }

}