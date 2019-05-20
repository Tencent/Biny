<?php
namespace app\controller;
use App;
use app\dao\baseDAO;
use app\dao\userDAO;
use biny\lib\Event;
use biny\lib\Logger;
use biny\lib\Language;
use Constant;

/**
 * 演示Action
 * @property userDAO $userDAO
 * @property baseDAO $testDAO
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
        $lang && Language::setLanguage($lang, Constant::month);
        Logger::info('you can print some information in console like time: '.date('Y-m-d H:i:s'), 'info');
        $view = $this->display('demo/demo', ['lan'=>Language::getLanguage('cn')]);
        $view->title = "Biny Framework Wiki";
        return $view;
    }

    public function action_test()
    {
        Event::on(onSql);
        $ret = $this->userDAO->join($this->testDAO, ['id'=>'user_id'])
            ->order([[],['type'=>['a','b','c','d','e']]])->order([['id'=>'desc']])->limit(2)->query();
        return $this->correct($ret);
    }
}