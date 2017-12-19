<?php
namespace app\service;
use biny\lib\TXService;
use TXApp;

/**
 * Created by PhpStorm.
 * User: billge
 * Date: 16-8-18
 * Time: 下午7:32
 */
class privilegeService extends TXService
{
    private $_errors;

    /**
     * 验证登录
     * @param $action \app\controller\baseAction
     * @return bool
     */
    public function login_required($action)
    {
        if (!$this->checkUser()){
//            return $this->error();
            TXApp::$base->session->lastUrl = $_SERVER['REQUEST_URI'];
            TXApp::$base->request->redirect('/login/');
//            echo $action->display('main/login');
//            exit;
        }
        return true; // return $this->correct();
    }

    public function my_required($action, $key)
    {
        return true;
    }

    /**
     * 验证用户登录
     * @return bool
     */
    private function checkUser()
    {
        $user = TXApp::$model->person;
        if ($user->exist()){
            return true;
        } else {
            return false;
        }
    }

    /**
     * 返回正确，兼容老写法
     * @return bool
     */
    private function correct()
    {
        return true;
    }

    /**
     * 返回异常
     * @param $msg
     * @return bool
     */
    private function error($msg='校验失败')
    {
        $this->_errors = $msg;
        return false;
    }

    public function getError()
    {
        return $this->_errors;
    }
}