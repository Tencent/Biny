<?php
/**
 * Test service
 * @author billge
 * @property TXSingleDAO $testDAO
 * @property userDAO $userDAO
 * @property projectDAO $projectDAO
 * @property anotherDAO $anotherDAO
 */
class testService extends baseService
{
    /**
     * @return array
     */
    public function test()
    {
        TXEvent::on(onSql);

        $this->testDAO->filter(['id'=>1])->query();
        $this->userDAO->filter(['id'=>1])->update(['name'=>'xx']);
        $DAO = $this->userDAO->leftJoin($this->projectDAO, ['projectId'=>'id'])->on([['projectId'=>1, 'loginTime'=>['>', 100]]])
            ->leftJoin($this->testDAO, [['id'=>'id']])->on(['test'=>['id'=>'test']]);
        $result = $DAO->filter([[], ['id'=>[1,2,3]]])
            ->addition([['avg'=>['cash'=>'a_c']]])
            ->group(["FROM_UNIXTIME(time,'%Y-%m-%d')"])
            ->having(['>'=>['a_c'=>10]])
            ->order([['name'=>'asc']])
            ->limit(10)->query([['projectId']]);
        TXLogger::info($result);
        $test = $this->userDAO->query();
        return $test;
    }
}