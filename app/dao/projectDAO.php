<?php
/**
 * 项目表
 */
class projectDAO extends baseDAO
{
    protected $table = 'Biny_Project';
    protected $_pk = 'id';

    /**
     * 分表
     * @param $id
     * @return $this
     */
    public function choose($id)
    {
        $this->setDbTable($this->table.'_'.$id);
        return $this;
    }
}