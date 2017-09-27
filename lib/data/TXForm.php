<?php
/**
 * Created by PhpStorm.
 * User: billge
 * Date: 15-11-4
 * Time: 下午3:05
 */
class TXForm
{
    const typeInt = 1;
    const typeBool = 2;
    const typeArray = 3;
    const typeObject = 4;
    const typeDate = 5;
    const typeDatetime = 6;
    const typeNonEmpty = 7;
    const typeRequired = 8;

    protected $_params = array();
    protected $_rules = array();
    protected $_method;
    protected $_dateFormats = array("Y-m-d", "Y/m/d");
    protected $_datetimeFormats = array("Y-m-d H:i", "Y/m/d H:i", "Y-m-d H:i:s", "Y/m/d H:i:s");

    protected $_datas = array();

    private $_errorMsg = array();
    private $checkMethods = array();

    /**
     * 构造函数
     * @param array $params
     * @param null $method
     */
    public function __construct($params=array(), $method=null)
    {
        $this->_params = array_merge($params, TXRouter::$ARGS);
        $this->_method = $method;
        if ($method && method_exists($this, $method)){
            $this->$method();
        }
    }

    /**
     * 构造form
     */
    public function init()
    {
        foreach ($this->_rules as $key => $default){
            $this->_datas[$key] = isset($this->_params[$key]) ? $this->_params[$key] : (isset($default[1]) ? $default[1] : null);
        }
    }

    /**
     * 获取form
     * @return array
     */
    public function values()
    {
        return $this->_datas;
    }

    /**
     * 获取form字段
     * @param $name
     * @return mixed
     * @throws TXException
     */
    public function __get($name)
    {
        if (substr($name, -7) == 'Service' || substr($name, -3) == 'DAO') {
            return TXFactory::create($name);
        }
        if (!array_key_exists($name, $this->_datas)){
            throw new TXException(5001, array($name, get_class($this)));
        }
        return $this->_datas[$name];
    }

    /**
     * 返回正确
     * @return bool
     */
    protected function correct()
    {
        return true;
    }

    /**
     * 返回错误
     * @param $arr
     * @return bool
     */
    protected function error($arr=array())
    {
        $this->_errorMsg = $arr;
        return false;
    }

    /**
     * 获取错误信息
     * @return bool
     */
    public function getError()
    {
        return $this->_errorMsg ?: false;
    }

    /**
     * 检测form合法性
     * @return bool
     * @throws TXException
     */
    public function check()
    {
        foreach ($this->_rules as $key => $value){
            if (!isset($value[0]) || !$value[0]){
                continue;
            }
            switch ($value[0]){
                case self::typeInt:
                    if (!is_numeric($this->__get($key))){
                        return $this->error(array($key=>sprintf("type Error [%s] given", $this->__get($key))));
                    }
                    break;

                case self::typeBool:
                    if ($this->__get($key) !== "true" && $this->__get($key) !== "false"){
                        return $this->error(array($key=>sprintf("type Error [%s] given", $this->__get($key))));
                    }
                    break;

                case self::typeArray:
                    if (!is_array($this->__get($key))){
                        return $this->error(array($key=>sprintf("type Error [%s] given", $this->__get($key))));
                    }
                    break;

                case self::typeObject:
                    if (!is_object($this->__get($key))){
                        return $this->error(array($key=>sprintf("type Error [%s] given", $this->__get($key))));
                    }
                    break;

                case self::typeDate:
                    $str = $this->__get($key);
                    $time = strtotime($this->__get($key));
                    if (!$time){
                        return $this->error(array($key=>sprintf("type Error [%s] given", $this->__get($key))));
                    }
                    $match = false;
                    foreach ($this->_dateFormats as $format){
                        if (date($format, $time) == $str){
                            $match = true;
                        }
                    }
                    if (!$match){
                        return $this->error(array($key=>sprintf("type Error [%s] given", $this->__get($key))));
                    }
                    break;

                case self::typeDatetime:
                    $str = $this->__get($key);
                    $time = strtotime($this->__get($key));
                    if (!$time){
                        return $this->error(array($key=>sprintf("type Error [%s] given", $this->__get($key))));
                    }
                    $match = false;
                    foreach ($this->_datetimeFormats as $format){
                        if (date($format, $time) !== $str){
                            $match = true;
                        }
                    }
                    if (!$match){
                        return $this->error(array($key=>sprintf("type Error [%s] given", $this->__get($key))));
                    }
                    break;

                case self::typeNonEmpty:
                    $value = $this->__get($key);
                    if ($value === NULL || (is_string($value) && trim($value) === "")){
                        return $this->error(array($key=>sprintf("type Error [%s] given", $value)));
                    }
                    break;

                case self::typeRequired:
                    if (!isset($this->_params[$key])){
                        return $this->error(array($key=>"type Error [NULL] given"));
                    }
                    break;

                default:
                    $value = 'valid_'.$value[1];
                    if (!isset($this->checkMethods[$value])){
                        if (!method_exists($this, $value)){
                            throw new TXException(5002, array($value, get_class($this)));
                        }
                        $this->checkMethods[$value] = $this->$value();
                    }
                    return $this->checkMethods[$value];
            }
        }
        return true;
    }
}