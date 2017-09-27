<?php
/**
 * Redis class
 * @method bool delete($key)
 * @method bool hdel($key, $hash)
 * @method int incr($key)
 * @method int incrBy($key, $value)
 * @method bool expire($key, $value)
 */
class TXRedis
{
    /**
     * @var Redis
     */
    private $handler;
    private $config;
    private $connect;

    private static $_instance = [];

    /**
     * @param string $name
     * @return TXRedis
     */
    public static function instance($name='redis')
    {
        if (!isset(self::$_instance[$name])){
            $config = TXConfig::getAppConfig($name, 'dns');
            self::$_instance[$name] = new self($config);
        }
        return self::$_instance[$name];
    }

    /**
     * @param $config
     * @throws TXException
     */
    private function __construct($config)
    {
        $this->config = TXConfig::getConfig('cache');
        $this->connect = $config;
    }

    /**
     * 选择库
     * @param $name
     * @return TXMemcache
     */
    public function choose($name)
    {
        return self::instance($name);
    }

    /**
     * 创建handler
     * @throws TXException
     */
    private function connect()
    {
        $config = $this->connect;
        $this->handler = new Redis();
        if (isset($config['keep-alive']) && $config['keep-alive']){
            $fd = $this->handler->pconnect($config['host'], $config['port'], 60);
        } else {
            $fd = $this->handler->connect($config['host'], $config['port']);
        }
        if($config["password"]){
            $this->handler->auth($config["password"]);
        }
        if (!$fd){
            throw new TXException(4005, array($config['host'], $config['port']));
        }
    }

    public function get($key, $serialize=null)
    {
        if (!$this->handler){
            $this->connect();
        }
        if ($serialize === null){
            $serialize = $this->config['serialize'];
        }
        return $serialize ? unserialize($this->handler->get($key)) : $this->handler->get($key);
    }

    public function set($key, $value, $timeout=0, $serialize=null)
    {
        if (!$this->handler){
            $this->connect();
        }
        if ($serialize === null){
            $serialize = $this->config['serialize'];
        }
        $value = $serialize ? serialize($value) : $value;
        return $this->handler->set($key, $value, $timeout);
    }

    public function hget($key, $hash, $serialize=null)
    {
        if (!$this->handler){
            $this->connect();
        }
        if ($serialize === null){
            $serialize = $this->config['serialize'];
        }
        return $serialize ? unserialize($this->handler->hget($key, $hash)) : $this->handler->hget($key, $hash);
    }

    public function hset($key, $hash, $value, $serialize=null)
    {
        if (!$this->handler){
            $this->connect();
        }
        if ($serialize === null){
            $serialize = $this->config['serialize'];
        }
        $value = $serialize ? serialize($value) : $value;
        return $this->handler->hset($key, $hash, $value);
    }

    /**
     * 调用redis
     * @param $method
     * @param $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if (!$this->handler){
            $this->connect();
        }
        return call_user_func_array(array($this->handler, $method), $arguments);
    }
}