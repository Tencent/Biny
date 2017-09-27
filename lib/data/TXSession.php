<?php
/**
 * Session service
 * @author billge
 */
class TXSession
{
    private static $instance = null;
    private $_data = null;
    private $config;

    /**
     * 初始化Session
     * @return null|TXSession
     */
    public static function instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->config = TXConfig::getConfig('cache')['session'];
    }

    /**
     * session 已连接
     * @return bool
     */
    private function isActive()
    {
        return session_status() == PHP_SESSION_ACTIVE;
    }

    private function start(){
        if ($cf = TXConfig::getAppConfig($this->config['save_handler'], 'dns')){
            ini_set("session.save_handler", $this->config['save_handler']);
            ini_set("session.save_path", 'tcp://' . $cf['host'] . ':' . $cf['port']);
        }
        if ($lifetime = $this->config['maxlifetime']){
            ini_set("session.gc_maxlifetime", $lifetime);
        }
        @session_start();
        $this->_data = $_SESSION;
    }

    //解决session死锁问题
    public function close()
    {
        if ($this->isActive()){
            @session_write_close();
            $this->_data = null;
        }
    }

    /**
     * 获取key
     * @param $key
     * @return null
     */
    public function __get($key)
    {
        if (!$this->isActive()){
            $this->start();
        }
        return isset($this->_data[$key]) ? $this->_data[$key] : null;
    }

    /**
     * 设置key
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        if (!$this->isActive()){
            $this->start();
        }
        $_SESSION[$key] = $this->_data[$key] = $value;
    }

    /**
     * 删除sessionKey
     * @param $key
     */
    public function __unset($key)
    {
        if (!$this->isActive()){
            $this->start();
        }
        unset($this->_data[$key]);
        unset($_SESSION[$key]);
    }

    /**
     * @param $key
     * @return bool
     */
    public function __isset($key)
    {
        if (!$this->isActive()){
            $this->start();
        }
        return isset($this->_data[$key]);
    }

    /**
     * 清空
     */
    public function clear()
    {
        if (!$this->isActive()){
            $this->start();
        }
        $this->_data = $_SESSION = [];
    }
}