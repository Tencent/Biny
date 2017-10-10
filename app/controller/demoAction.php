<?php
namespace app\controller;
use biny\lib\TXEvent;
use biny\lib\TXLogger;

/**
 * 演示Action
 * @property \app\dao\userDAO $userDAO
 */
class demoAction extends baseAction
{
//    // 权限配置
//    protected function privilege()
//    {
//        return array(
//            'login_required' => array(
//                'actions' => '*', //绑定action
//            ),
//        );
//    }

    public function action_index()
    {
        $view = $this->display('demo/demo');
        $view->title = "Biny演示页面";
        return $view;
    }
}