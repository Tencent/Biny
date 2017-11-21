<?php

namespace app\model;
use TXApp;

/**
 * Created by PhpStorm.
 * User: billge
 * Date: 15-7-28
 * Time: 下午5:37
 */
class person extends baseModel
{
    /**
     * @var array 单例对象
     */
    protected static $_instance = [];

    /**
     * @param null $id
     * @return person
     */
    public static function init($id=null)
    {
        $id = $id ?: TXApp::$base->session->userId;
        if (!isset(self::$_instance[$id])){
            self::$_instance[$id] = new self($id);
        }
        return self::$_instance[$id];
    }

    protected function __construct($id)
    {
        $this->DAO = $this->userDAO;
        if ($id !== NULL){
            $this->_data = $this->DAO->getByPk($id);
            $this->_pk = $id;
        }
    }

    /**
     * 是否存在
     * @return mixed
     */
    public function exist()
    {
        return $this->_data ? true : false;
    }

    /**
     * 获取键值
     * @return mixed
     */
    public function getPk()
    {
        return $this->_pk;
    }

    /**
     * 登录
     */
    public function login()
    {
        $this->DAO->updateByPk($this->_pk, ['loginTime'=>time(), 'count'=>['+'=>1]]);
        TXApp::$base->session->userId = $this->_pk;
    }

    /**
     * 登出
     */
    public function loginOut()
    {
        unset(TXApp::$base->session->userId);
    }
}