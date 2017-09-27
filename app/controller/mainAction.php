<?php
/**
 * 主页Action
 * @property TXSingleDAO $testDAO
 * @property userDAO $userDAO
 * @property projectDAO $projectDAO
 * @property testService $testService
 */
class mainAction extends baseAction
{
    public function init()
    {
        TXLogger::time('start-time');
        TXLogger::memory('start-memory');
        TXLogger::log('do something');
        TXLogger::time('end-time');
        TXLogger::memory('end-memory');
    }

    public function action_index($id=10, $type)
    {
        $arr = $this->testService->test();
        $params = [
            'testArr' => $arr,
            'string' => 'bb<>',
            'src' => 'alert(1)'
        ];
        $view = $this->display('main/test', $params, array('src'=>'<f333>'));
        $view->title = "主页标题";
        return $view;
    }

    public function action_test($id, $type="ss")
    {
        return $this->error('aaaa');
    }

    /**
     * @param $event
     * @param array $param
     */
    public function testEvent($event, $param=array())
    {
        TXLogger::info("tigger in beforeAction".$event);
    }

    /**
     * @param $event
     * @param array $param
     */
    public function testEvent2($event, $param=array())
    {
        TXLogger::info("tigger in afterAction".$event);
    }
}