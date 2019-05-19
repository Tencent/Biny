<?php


namespace app\dao;


use biny\lib\models\BaseModel;

class DemoModel extends BaseModel
{

    public function __construct()
    {
//        $this->setConn('db2');
        parent::__construct();
    }

    public function test()
    {
        # $result = $this->tb->where('id', '<', 100)->get()->toArray();
        $result = $this->tb->get()->toArray();
//        $result = $this->mtb->get()->toArray();# force master
//        $result = $this->db->select("select * from demo where id > ?", [1]);
//        $result = $this->db->select("select * from demo where id > ?", [1], false);# force master
        return $result;
    }
}