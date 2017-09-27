<?php
/**
 * Created by PhpStorm.
 * User: billge
 * Date: 15-11-4
 * Time: 下午4:37
 */
class testForm extends TXForm
{
    protected $_rules = [
        'id'=>[self::typeInt],
        'name'=>[self::typeNonEmpty],
        'status'=>['testCmp']
    ];

    public function get_user()
    {
        $this->_rules['id'] = [self::typeDate, 2];
    }

    public function valid_testCmp()
    {
        return $this->correct();
    }
}