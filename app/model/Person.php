<?php

namespace app\model;
use biny\lib\TXFactory;
use biny\lib\TXModel;
use TXApp;

/**
 * Created by PhpStorm.
 * User: billge
 * Date: 15-7-28
 * Time: 下午5:37
 */
class Person extends TXModel
{
    private static $_cache = [];

    protected $_data;
    /**
     * @var \app\dao\baseDAO
     */
    protected $DAO;
    protected $_pk;

    /**
     * @param null $id
     * @return Person
     */
    public static function get($id=null)
    {
        $id = $id ?: TXApp::$base->session->userId;
        if (!isset(self::$_cache[$id])){
            self::$_cache[$id] = new self($id);
        }
        return self::$_cache[$id];
    }

    private function __construct($id)
    {
        $this->DAO = TXFactory::create('userDAO');
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