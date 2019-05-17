<?php
namespace app\controller;
use biny\lib\Action;
use biny\lib\Response;
use App;
/**
 * Base action
 */
class baseAction extends Action
{
    /**
     * @param $view
     * @param array $array
     * @param array $objects 直接使用参数
     * @return Response
     */
    public function display($view, $array=array(), $objects=array())
    {
        $objects = array_merge(array(
            'webRoot' => App::$base->app_config->get('webRoot'),
        ), $objects);
        return parent::display($view, $array, $objects);
    }
}