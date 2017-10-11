<?php
namespace app\controller;
use biny\lib\TXAction;
use biny\lib\TXResponse;
use TXApp;
/**
 * Base action
 * @property \app\service\baseService $baseService
 * @property \app\service\testService $testService
 */
class baseAction extends TXAction
{
    /**
     * @param $view
     * @param array $array
     * @param array $objects 直接使用参数
     * @return TXResponse
     */
    public function display($view, $array=array(), $objects=array())
    {
        header("Content-type:text/html;charset=utf-8");  //解决克隆为ASC文件后产生的乱码问题
        $objects = array_merge(array(
            'webRoot' => TXApp::$base->app_config->get('webRoot'),
        ), $objects);
        return parent::display($view, $array, $objects);
    }
}
