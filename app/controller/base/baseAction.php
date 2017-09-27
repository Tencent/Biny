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
            'webRoot' => TXConfig::getAppConfig('webRoot'),
            'CDN_ROOT' => TXConfig::getAppConfig('CDN_ROOT'),
        ), $objects);
        return parent::display($view, $array, $objects);
    }
}