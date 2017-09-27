<?php
/**
 * @method bool delete($key)
 * @method mixed get($key)
 */
class TXMemcache
{
    private static $_instance = [];

    /**
     * @param string $name
     * @return TXMemcache
     */
    public static function instance($name='memcache')
    {
        if (!isset(self::$_instance[$name])){
            $config = TXConfig::getAppConfig($name, 'dns');
            self::$_instance[$name] = new self($config);
        }
        return self::$_instance[$name];
    }


    /**
     * @var Memcache
     */
    private $handler;
    private $connect;

    public function __construct($config)
    {
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
        $this->handler = new Memcache();
        if (isset($config['keep-alive']) && $config['keep-alive']){
            $fd = $this->handler->pconnect($config['host'], $config['port'], 60);
        } else {
            $fd = $this->handler->connect($config['host'], $config['port']);
        }
        if (!$fd){
            throw new TXException(4004, array($config['host'], $config['port']));
        }
    }

    public function set($key, $value, $expire=0)
    {
        if (!$this->handler){
            $this->connect();
        }
        return $this->handler->set($key, $value, MEMCACHE_COMPRESSED, $expire);
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