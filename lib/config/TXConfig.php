<?php
/**
 * 配置读取类
 */
class TXConfig
{
    private static $cfgCaches = [];

    private static $appcfgCaches = [];

    private static $alias = [];

    /**
     * Load config file
     * @param string $module
     * @throws TXException
     */
    private static function loadConfig($module)
    {
        if (!isset(self::$cfgCaches[$module])) {
            $path = TXApp::$base_root . DS . 'config' . DS . $module . '.php';

            $n_module = $module. (ENV_DEV ? '_dev' : (ENV_PRE ? '_pre' : (ENV_PUB ? '_pub' : '')));
            $n_path = TXApp::$base_root . DS . 'config' . DS . $n_module . '.php';
            if (is_readable($path) || is_readable($n_path)) {
                $config = is_readable($path) ? require($path) : [];
                $config = is_readable($n_path) ? array_merge($config, require($n_path)) : $config;
                self::$cfgCaches[$module] = $config;
            } else {
                throw new TXException(1002, array($path));
            }
        }

        return self::$cfgCaches[$module];
    }

    /**
     * @param string $module
     * @return mixed
     * @throws TXException
     */
    private static function loadAppConfig($module)
    {
        if (!isset(self::$appcfgCaches[$module])) {
            $path = TXApp::$app_root . DS . 'config' . DS . $module . '.php';

            $n_module = $module. (ENV_DEV ? '_dev' : (ENV_PRE ? '_pre' : (ENV_PUB ? '_pub' : '')));
            $n_path = TXApp::$app_root . DS . 'config' . DS . $n_module . '.php';
            if (is_readable($path) || is_readable($n_path)) {
                $config = is_readable($path) ? require($path) : [];
                $config = is_readable($n_path) ? array_merge($config, require($n_path)) : $config;
                self::$appcfgCaches[$module] = $config;
            } else {
                throw new TXException(1002, array($path));
            }
        }

        return self::$appcfgCaches[$module];
    }

    /**
     * get core config
     * @param $key
     * @param string $module
     * @param bool $alias
     * @return mixed|null
     */
    public static function getConfig($key, $module='config', $alias=true)
    {
        self::loadConfig($module);

        if (isset(self::$cfgCaches[$module][$key])) {
            return $alias ? self::getAlias(self::$cfgCaches[$module][$key]) : self::$cfgCaches[$module][$key];
        } else {
            return null;
        }
    }

    /**
     * get app config
     * @param $key
     * @param string $module
     * @param bool $alias
     * @return mixed|null
     */
    public static function getAppConfig($key, $module='config', $alias=true)
    {
        self::loadAppConfig($module);

        if (isset(self::$appcfgCaches[$module][$key])) {
            return $alias ? self::getAlias(self::$appcfgCaches[$module][$key]) : self::$appcfgCaches[$module][$key];
        } else {
            return null;
        }
    }

    /**
     * 设置别名
     * @param $key
     * @param $value
     */
    public static function setAlias($key, $value)
    {
        self::$alias["@{$key}@"] = $value;
    }

    /**
     * 获取别名转义
     * @param $value
     * @return mixed
     */
    private static function getAlias($value)
    {
        if (self::$alias && is_string($value)){
            $value = str_replace(array_keys(self::$alias), array_values(self::$alias), $value);
            return $value;
        } else {
            return $value;
        }
    }
}