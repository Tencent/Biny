<?php
/**
 * Cache
 */
class TXCache {
    /**
     * @var array
     */
    private $cache= [];

    /**
     * 获取全局临时缓存
     * @param $key
     * @return null
     */
    public function __get($key)
    {
        return isset($this->cache[$key]) ? $this->cache[$key] : null;
    }

    /**
     * 设置全局临时缓存
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        $this->cache[$key] = $value;
    }

    /**
     * 判断是否存在
     * @param $key
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->cache[$key]);
    }

    /**
     * 删除全局临时缓存
     * @param $key
     */
    public function __unset($key)
    {
        unset($this->cache[$key]);
    }

    /**
     * 调试专用接口
     * @return array
     */
    public function __toLogger()
    {
        return $this->cache;
    }
}