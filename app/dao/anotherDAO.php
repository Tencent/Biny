<?php
/**
 * 测试表
 */
class anotherDAO extends baseDAO
{
    protected $dbConfig = 'testDb';
    protected $table = 'person';
    protected $_pk = 'id';
}