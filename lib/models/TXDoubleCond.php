<?php
/**
 * Created by PhpStorm.
 * User: billge
 * Date: 16-4-12
 * Time: 下午12:15
 */
class TXDoubleCond extends TXCond
{
    /**
     * @var TXDoubleDAO
     */
    protected $DAO;

    /**
     * 构建limit
     * @param $len
     * @param int $start
     * @return $this
     */
    public function limit($len, $start=0)
    {
        $this->limit = is_array($len) ? $len : array(intval($start), intval($len));
        return $this;
    }

    /**
     * 构建order
     * @param $orderby
     * @return $this
     */
    public function order($orderby)
    {
        foreach ($orderby as $key => $val){
            if (is_array($val)){
                if (!isset($this->orderby[$key])){
                    $this->orderby[$key] = array();
                }
                if (is_string($this->orderby[$key])){
                    $this->orderby[$key] = $val;
                } else {
                    foreach ($val as $k => $v){
                        $this->orderby[$key][$k] = $v;
                    }
                }
            } else {
                $this->orderby[$key] = $val;
            }
        }
        return $this;
    }

    /**
     * 构建group
     * @param $groupby
     * @return $this
     */
    public function group($groupby)
    {
        foreach ($groupby as $key => $val){
            if (is_array($val)){
                if (!isset($this->groupby[$key])){
                    $this->groupby[$key] = array();
                }
                foreach ($val as $k => $v){
                    $this->groupby[$key][$k] = $v;
                }
            } else {
                $this->groupby[$key] = $val;
            }
        }
        return $this;
    }

    /**
     * 构建having
     * @param $having
     * @return $this
     */
    public function having($having)
    {
        foreach ($having as $key => $val){
            foreach ($val as $k => $v){
                $this->having[$key][$k] = $v;
            }
        }
        return $this;
    }

    /**
     * 构建additions
     * @param $additions
     * @return $this
     */
    public function addition($additions)
    {
        foreach ($additions as $key => $val){
            if (is_array($val)){
                if (!isset($this->additions[$key])){
                    $this->additions[$key] = array();
                }
                foreach ($val as $k => $v){
                    $this->additions[$key][$k] = $v;
                }
            } else {
                $this->additions[$key] = $val;
            }
        }
        return $this;
    }
}