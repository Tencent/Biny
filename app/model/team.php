<?php

namespace app\model;
use biny\lib\ModelArray;

/**
 * Created by PhpStorm.
 * User: billge
 * Date: 19-11-04
 * Time: 下午5:37
 * @method person creator()
 * @method person admin()
 *
 * @property $id int
 * @property $name string
 */
class team extends ModelArray
{
    protected $DAO = 'teamDAO';

    /**
     * @var array 模型关系
     */
    protected $_relates = array(
//        '方法名' => array('模型名', '对应参数名'|array(反向DAO, 反向关联key[, pk])),
        'creator' => array('person', 'creator'),
        'admin' => array('person', ['userDAO', 'adminTeam']),
    );

    /**
     * 自定义方法 返回用户人数
     */
    public function getTotal()
    {
        // 获取team_id标记为当前team的用户数
        return $this->userDAO->filter(['team_id'=>$this->id])->count();
    }
}