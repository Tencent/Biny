<?php


namespace biny\lib\traits;


trait GetInstances
{
    private static $instances = [];

    /**
     * @return static
     */
    final static public function getInstance()
    {
        $name = get_called_class();
        if (!isset(self::$instances[$name])) {
            self::$instances[$name] = new static();
        }
        return self::$instances[$name];
    }
}
