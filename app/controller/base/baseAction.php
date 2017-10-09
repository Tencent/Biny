<?php
/**
 * Base action
 * @property baseService $baseService
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
        $objects = array_merge(array(
            'webRoot' => TXApp::$base->app_config->get('webRoot'),
        ), $objects);
        return parent::display($view, $array, $objects);
    }
}