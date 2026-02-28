<?php

namespace app\controller;
use App;

/**
 * Created by PhpStorm.
 * User: billge
 * Date: 16-6-22
 * Time: 上午11:38
 * @property \app\dao\userDAO $userDAO
 */
class loginAction extends baseAction
{
    /**
     * 登录
     */
    public function action_index()
    {
        if (App::$model->person->exist()){
            $this->response->redirect('/');
        }
        $username = $this->param('username');
        if (!$username){
            return $this->response->display('main/login');
        }
        if ($user = $this->userDAO->filter(['name'=>$username])->find()){
            App::$model->person($user['id'])->login();
        } else {
            $id = $this->userDAO->add(['name'=>$username, 'registerTime'=>time()]);
            App::$model->person($id)->login();
        }
        if ($lastUrl = App::$base->session->lastUrl){
            unset(App::$base->session->lastUrl);
            $this->response->redirect($lastUrl);
        } else {
            $this->response->redirect('/');
        }
    }

}