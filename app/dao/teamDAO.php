<?php

namespace app\dao;
use biny\lib\Factory;

/**
 * 用户表
 */
class teamDAO extends baseDAO
{
    protected $table = 'Biny_Project';
    protected $_pk = 'id';

    public function withUser($status=null)
    {
        $dao = $this->leftJoin(Factory::create('userDAO'), ['id'=>'teamid']);
        return $status ? $dao->on(['user' => ['status'=>$status]]) : $dao;
    }
}