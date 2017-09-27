<?php
/**
 * Created by PhpStorm.
 * User: billge
 * Date: 15-11-2
 * Time: 下午6:04
 */
class TXEvent
{
    /**
     * 事件句柄
     * @var int
     */
    private static $fh = 0;

    /**
     * 事件观察者
     * @var array
     */
    private static $monitors = array();

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
    }

    /**
     * 绑定事件
     * @param callable $method
     * @param $event
     * @param null $times
     * @return int
     * @throws TXException
     */
    public static function bind($method, $event, $times=null)
    {
        if (!is_callable($method)){
            throw new TXException(5003, isset($method[1]) ? $method[1] : 'null');
        }
        $fh = ++self::$fh;
        self::$monitors[$event][$fh] = array('m'=>$method, 't'=>$times);
        return $fh;
    }

    /**
     * 绑定永久事件
     * @param callable $method
     * @param $event
     * @return int
     */
    public static function on($event, $method=null)
    {
        $method = $method ?: [TXLogger::instance(), 'event'];
        return self::bind($method, $event);
    }

    /**
     * 绑定一次事件
     * @param callable $method
     * @param $event
     * @return int
     */
    public static function one($event, $method=null)
    {
        $method = $method ?: [TXLogger::instance(), 'event'];
        return self::bind($method, $event, 1);
    }

    /**
     * 解绑事件
     * @param $event
     * @param $fh
     * @return bool
     */
    public static function off($event, $fh=null)
    {
        if ($fh){
            if (isset(self::$monitors[$event][$fh])){
                unset(self::$monitors[$event][$fh]);
                return true;
            } else {
                return false;
            }
        } else {
            unset(self::$monitors[$event]);
            return true;
        }
    }

    /**
     * 触发事件
     * @param $event
     * @param array $params
     * @return bool
     */
    public static function trigger($event, $params=array())
    {
        if (!isset(self::$monitors[$event])){
            return false;
        }
        array_unshift($params, $event);
        foreach (self::$monitors[$event] as $fh => &$value){
            $method = $value['m'];
            call_user_func_array($method, $params);
            if (isset($value['t']) && --$value['t'] <= 0){
                unset(self::$monitors[$event][$fh]);
            }
        }
        unset($value);
        return true;
    }

    /**
     * 启动类
     */
    public static function init()
    {
        TXEvent::on(onException, ['TXEvent', 'onException']);
        TXEvent::on(onRequest, ['TXEvent', 'onRequest']);
    }

    /**
     * 异常错误
     * @param $event
     * @param $code
     * @param $params
     */
    public static function onException($event, $code, $params)
    {
        TXLogger::addError("ERROR CODE: $code\n".join("\n", $params));
    }

    /**
     * 请求入口
     * @param $event
     * @param $request TXRequest
     */
    public static function onRequest($event, $request)
    {
        TXLogger::addLog('request: '.$request->getHostInfo().$request->getUrl());
    }
}