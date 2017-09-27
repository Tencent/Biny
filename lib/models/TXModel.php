<?php
/**
 * Created by PhpStorm.
 * User: billge
 * Date: 15-7-29
 * Time: 上午11:18
 */
class TXModel
{
    protected $_data;
    private $_cache = [];
    protected $_dirty = false;
    /**
     * @var baseDAO
     */
    protected $DAO = null;
    protected $_pk;

    public function __get($key)
    {
        if (substr($key, -7) == 'Service' || substr($key, -3) == 'DAO') {
            return TXFactory::create($key);
        }
        $data = array_merge($this->_data, $this->_cache);
        return isset($data[$key]) ? TXString::encode($data[$key]) : null;
    }

    public function _get($key)
    {
        $data = array_merge($this->_data, $this->_cache);
        return isset($data[$key]) ? $data[$key] : null;
    }

    public function __set($key, $value)
    {
        if (array_key_exists($key, $this->_data)){
            $this->_data[$key] = $value;
            $this->_dirty = true;
        } else {
            $this->_cache[$key] = $value;
        }
    }

    public function __isset($key)
    {
        return isset($this->_data[$key]) || isset($this->_cache[$key]);
    }

    public function save()
    {
        if ($this->_dirty && $this->_data && $this->DAO){
            $this->DAO->updateByPK($this->_pk, $this->_data);
            $this->_dirty = false;
        }
    }



    public function __toLogger()
    {
        return $this->_data;
    }

}