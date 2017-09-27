<?php
/**
 * Created by PhpStorm.
 * User: billge
 * Date: 16-10-1
 * Time: 上午11:00
 * @property userDAO $userDAO
 */
class testShell extends TXShell
{
    public function init()
    {
        TXLogger::addLog('init');
        return 0;
    }

    public function action_index()
    {
        return $this->correct('sdfdfd');
    }

    public function action_prm($id)
    {
        var_dump($this->getParam('test', 'aaa'));
        $user = $this->userDAO->filter(['id'=>$id])->find();
        return $this->correct($user);
    }
}