<?php
/**
 * Created by PhpStorm.
 * User: billge
 * Date: 2017/10/16
 * Time: 10:21
 */

namespace app\model;
use biny\lib\TXFactory;
use biny\lib\TXString;

/**
 * Class baseModel
 * @package app\model
 * @property \app\dao\userDAO $userDAO
 */
class baseModel
{
    /**
     * @var array 单例对象
     */
    protected static $_instance = [];
    protected $_data;
    protected $_cache = [];
    protected $_dirty = false;
    /**
     * @var \app\dao\baseDAO
     */
    protected $DAO = null;
    protected $_pk;

    /**
     * @param null $id
     * @return person
     */
    public static function init($id=null)
    {
        if (!isset(static::$_instance[$id])){
            static::$_instance[$id] = new static($id);
        }
        return static::$_instance[$id];
    }

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