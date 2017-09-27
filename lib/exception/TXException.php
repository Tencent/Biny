<?php
/**
 * Core Exception
 */
class TXException extends ErrorException
{
    private $config;

    /**
     * 构造函数
     * @param string $code
     * @param array $params
     * @param string $html
     */
    public function __construct($code, $params=array(), $html="500")
    {
        $this->config = TXConfig::getConfig('exception');
        $message = self::fmt_code($code, $params);
        TXEvent::trigger(onException, array($code, array($message, $this->getTraceAsString())));
        if (class_exists('TXDatabase')){
            TXDatabase::rollback();
        }
        try{
            if (RUN_SHELL){
                echo "<b>Fatal error</b>:  $message in <b>{$this->getFile()}</b>:<b>{$this->getLine()}</b>\nStack trace:\n{$this->getTraceAsString()}";
                exit;
            }
            if ($httpCode = TXConfig::getConfig($html, 'http')){
                header($httpCode);
            }
            if (SYS_DEBUG){
                echo "<pre>";
                echo "<b>Fatal error</b>:  $message in <b>{$this->getFile()}</b>:<b>{$this->getLine()}</b>\nStack trace:\n{$this->getTraceAsString()}";

            } else {
                if (TXApp::$base->request->isShowTpl() || !TXApp::$base->request->isAjax()){
                    echo new TXResponse($this->config["exceptionTpl"], array('msg'=>$this->config['messages'][$html] ?: "系统数据异常：$html"), $params);
                } else {
                    $data = array("flag" => false, "error" => $this->config['messages'][$html] ?: "系统数据异常：$html");
                    echo new TXJSONResponse($data);
                }
            }
            die();
        } catch (TXException $ex) {
            //防止异常的死循环
            echo "system Error";
            exit;
        }
    }

    /**
     * 格式化代码为字符串
     * @param int $code
     * @param array $params
     * @return string
     */
    public static function fmt_code($code, $params=array())
    {
        try {
            $msgtpl = TXConfig::getConfig($code, 'exception');
        } catch (TXException $ex) { //防止异常的死循环
            $msgtpl = $ex->getMessage();
        }
        return vsprintf($msgtpl, $params);
    }
}