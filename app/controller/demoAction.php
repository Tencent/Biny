<?php
namespace app\controller;
use biny\lib\Event;
use biny\lib\Logger;
use biny\lib\Language;

/**
 * 演示Action
 * @property \app\dao\userDAO $userDAO
 * @property \app\dao\teamDAO $teamDAO
 * @property \app\dao\baseDAO $testDAO
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
        $lang = $this->get('lang');
        $lang && Language::setLanguage($lang, \Constant::month);
        Logger::info('you can print some information in console like time: '.date('Y-m-d H:i:s'), 'info');
        $view = $this->display('demo/demo', ['lan'=>Language::getLanguage('cn')]);
        $view->title = "Biny Framework Wiki";
        return $view;
    }

    public function action_test()
    {
        Event::on(onSql);
        $tables = $this->userDAO->tables();
        $columns = $this->userDAO->columns();
        return $this->correct(\App::$base->redis->get('nnn'));
    }
}