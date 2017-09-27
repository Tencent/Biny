<?php
/**
 * Shell class
 */
class TXShell
{
    /**
     * 请求参数
     * @var array
     */
    private $params;

    /**
     * 顺序参数
     * @var array
     */
    private $args;

    /**
     * 构造函数
     */
    public function __construct($params)
    {
        $this->args = $params['args'];
        $this->params = $params['params'];
    }

    /**
     * 获取Service|DAO
     * @param $obj
     * @return TXService | TXDAO
     */
    public function __get($obj)
    {
        if (substr($obj, -7) == 'Service' || substr($obj, -3) == 'DAO') {
            return TXFactory::create($obj);
        }
    }

    /**
     * 获取请求参数
     * @param $key
     * @param null $default
     * @return float|int|mixed|null
     */
    public function getParam($key, $default=null)
    {
        if (is_int($key)){
            return isset($this->args[$key]) ? $this->args[$key] : $default;
        } else {
            return isset($this->params[$key]) ? $this->params[$key] : $default;
        }
    }

    /**
     * @param string $ret
     * @return mixed
     */
    public function correct($ret='success')
    {
        TXLogger::addLog($ret);
        return $ret;
    }

    /**
     * @param string $msg
     * @return string
     */
    public function error($msg="error")
    {
        TXEvent::trigger(onError, array($msg));
        TXLogger::addError($msg);
        return $msg;
    }
}