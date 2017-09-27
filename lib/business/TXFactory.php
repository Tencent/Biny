<?php
/**
 * object factory
 */
class TXFactory {
    /**
     * 对象列表
     *
     * @var array
     */
    private static $objects = array();

    /**
     * dynamic create object
     * @param string $class
     * @param string $alias
     * @return TXSingleDAO | mixed
     */
    public static function create($class, $alias=null)
    {
        if (null === $alias) {
            $alias = $class;
        }
        if (!isset(self::$objects[$alias])) {
            //可以不写DAO文件自动建立对象
            if (substr($class, -3) == 'DAO') {
                $key = substr($class, 0, -3);
                $dbConfig = TXConfig::getConfig('dbConfig', 'database');
                if (isset($dbConfig[$key])){
                    $dao = new TXSingleDAO($dbConfig[$key], $class);
                    self::$objects[$alias] = $dao;
                } else {
                    self::$objects[$alias] = new $class();
                }
            } else {
                self::$objects[$alias] = new $class();
            }
        }

        return self::$objects[$alias];
    }
}