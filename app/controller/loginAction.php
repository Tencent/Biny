<?php
/**
 * Created by PhpStorm.
 * User: billge
 * Date: 16-6-22
 * Time: 上午11:38
 * @property userDAO $userDAO
 */
class loginAction extends baseAction
{
    /**
     * 登录
     */
    public function action_index()
    {
        if (TXApp::$base->person->exist()){
            TXApp::$base->request->redirect('/');
        }
        $username = $this->getParam('username');
        if (!$username){
            return $this->display('main/login');
        }
        if ($user = $this->userDAO->filter(['name'=>$username])->find()){
            Person::get($user['id'])->login();
        } else {
            $id = $this->userDAO->add(['name'=>$username, 'registerTime'=>time()]);
            Person::get($id)->login();
        }
        if ($lastUrl = TXApp::$base->session->lastUrl){
            unset(TXApp::$base->session->lastUrl);
            TXApp::$base->request->redirect($lastUrl);
        } else {
            TXApp::$base->request->redirect('/');
        }
    }

}