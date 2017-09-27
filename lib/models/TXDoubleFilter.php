<?php
/**
 * Created by PhpStorm.
 * User: billge
 * Date: 15-8-3
 * Time: 上午11:50
 * @method TXDoubleCond group($groupby)
 * @method TXDoubleCond having($having)
 * @method TXDoubleCond limit($len, $start=0)
 * @method TXDoubleCond order($orderby)
 * @method TXDoubleCond addition($additions)
 */
class TXDoubleFilter extends TXFilter
{
    /**
     * @var TXDoubleDAO
     */
    protected $DAO;
    protected $conds = [];

    /**
     * and 操作
     * @param $cond
     * @return TXDoubleFilter
     */
    public function filter($cond=array())
    {
        return $cond ? new self($this->DAO, $cond, "__and__", $this->conds[0]) : $this;
    }

    /**
     * or 操作
     * @param $cond
     * @return TXDoubleFilter
     */
    public function merge($cond)
    {
        return $cond ? new self($this->DAO, $cond, "__or__", $this->conds[0]) : $this;
    }

    /**
     * 查询条件
     * @param $method
     * @param $args
     * @return TXDoubleCond
     * @throws TXException
     */
    public function __call($method, $args)
    {
        if (in_array($method, $this->methods) || in_array($method, $this->calcs)){
            $cond = new TXDoubleCond($this->DAO);
            $cond->setWhere($this->buildWhere($this->conds));
            return call_user_func_array([$cond, $method], $args);
        } else {
            throw new TXException(3009, array($method, __CLASS__));
        }
    }
}