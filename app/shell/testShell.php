<?php
namespace app\shell;
use biny\lib\Shell;
use biny\lib\Logger;
/**
 * Created by PhpStorm.
 * User: billge
 * Date: 16-10-1
 * Time: 上午11:00
 */
class testShell extends Shell
{
    public function init()
    {
        Logger::addLog('init');
        return 0;
    }

    public function action_index()
    {
        $this->response->correct('success');
    }
}