<?php
namespace app\shell;
use biny\lib\TXShell;
use biny\lib\TXLogger;
/**
 * Created by PhpStorm.
 * User: billge
 * Date: 16-10-1
 * Time: 上午11:00
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
        return $this->correct('success');
    }
}