<?php
/**
 * 演示Action
 * @property demoDAO $demoDAO
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

    /**
     * demo首页
     */
    public function action_index()
    {
        $view = $this->display('demo/demo');
        $view->title = "Biny演示页面";
        return $view;
    }
}