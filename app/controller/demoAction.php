<?php
namespace app\controller;
use biny\lib\TXLogger;
use biny\lib\TXLanguage;
use TXApp;

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
        $lang = $this->get('lang');
        $lang && TXLanguage::setLanguage($lang, \TXConst::month);
        TXLogger::info('you can print some information in console like time: '.date('Y-m-d H:i:s'), 'info');
        $view = $this->display('demo/demo', ['lan'=>TXLanguage::getLanguage('cn')]);
        $view->title = "Biny Framework Wiki";
        return $view;
    }
}