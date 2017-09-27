<?php
/**
 * Created by PhpStorm.
 * User: billge
 * Date: 16-5-4
 * Time: 上午11:39
 */
class demoDAO extends baseDAO
{
    protected $_pk = ['date', 'rtx'];
    protected $table = 'Biny_Demo_Uv';
    protected $_pkCache = true;
}