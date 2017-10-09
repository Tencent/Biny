<?php
/**
 * 演示Action
 * @property userDAO $userDAO
 * @property testService $testService
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