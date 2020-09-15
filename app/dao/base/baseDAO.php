<?php

namespace app\dao;
use biny\lib\SingleDAO;
use App;

/**
 * Created by PhpStorm.
 * User: billge
 * Date: 15-7-30
 * Time: 下午7:55
 */

class baseDAO extends SingleDAO
{
    protected $_pk;
    protected $_pkCache = false;
    private $cacheKey;

    public function __construct()
    {
        parent::__construct();
        if ($this->_pkCache){
            $config = App::$base->config->get('cache');
            $this->cacheKey = sprintf($config['pkCache'], substr(get_called_class(), 0, -3));
        }
    }

    /**
     * 获取PK
     * @return mixed
     */
    public function getPk()
    {
        return $this->_pk;
    }

    /**
     * 组合PK
     * @param $pk
     * @return array
     */
    private function buildPK($pk)
    {
        if (is_array($this->_pk)){
            return array_combine($this->_pk, $pk);
        } else {
            return [$this->_pk => $pk];
        }
    }

    /**
     * 获取主键
     * @param $pk
     * @return array
     */
    public function getByPk($pk)
    {
        if ($this->_pkCache && $cache = $this->getCache($pk)){
            return $cache;
        }
        $cond = $this->buildPK($pk);
        $result = $this->filter($cond)->find();
        if ($this->_pkCache){
            $this->setCache($pk, $result);
        }
        return $result;
    }

    /**
     * 更新主键
     * @param $pk
     * @param $sets
     * @return bool
     */
    public function updateByPk($pk, $sets)
    {
        $cond = $this->buildPK($pk);
        $flag = $this->filter($cond)->update($sets);
        if ($flag && $this->_pkCache && $cache = $this->getCache($pk)){
            foreach ($sets as $k => $v) {
                if (is_array($v)) {
                    // ['type'=>['+'=>5]]
                    foreach ($v as $cal => $num) {
                        $cache[$k] = eval("return {$cache[$k]} $cal $num;");
                    }
                } else {
                    $cache[$k] = $v;
                }
            }
            $this->setCache($pk, $cache);
        }
        return $flag;
    }

    /**
     * 删除主键
     * @param $pk
     * @return bool
     */
    public function deleteByPk($pk)
    {
        $cond = $this->buildPK($pk);
        $flag = $this->filter($cond)->delete();
        if ($flag && $this->_pkCache){
            $this->delCache($pk);
        }
        return $flag;
    }

    /**
     * 获取redisPK
     * @param $pk
     * @return string
     */
    private function getHash($pk)
    {
        if (is_array($this->_pk)){
            $pk = implode('_', $pk);
        }
        return $pk;
    }

    /**
     * 获取cache
     * @param $pk
     * @return bool|array
     */
    private function getCache($pk)
    {
        if ($this->_pkCache){
            $hash = $this->getHash($pk);
            return App::$base->redis->hget($this->cacheKey, $hash);
        } else {
            return false;
        }

    }

    /**
     * 设置cache
     * @param $pk
     * @param $value
     * @return mixed
     */
    private function setCache($pk, $value)
    {
        if ($this->_pkCache){
            $hash = $this->getHash($pk);
            return App::$base->redis->hset($this->cacheKey, $hash, $value);
        }
    }

    /**
     * 删除cache
     * @param $pk
     * @return mixed
     */
    private function delCache($pk)
    {
        if ($this->_pkCache){
            $hash = $this->getHash($pk);
            return App::$base->redis->hdel($this->cacheKey, $hash);
        }
    }

    /**
     * 清除全表缓存
     * @return mixed
     */
    public function clearCache()
    {
        if ($this->_pkCache){
            return App::$base->redis->del($this->cacheKey);
        }
    }

}