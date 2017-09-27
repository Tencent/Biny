<?php
/**
 * 业务基类
 */
class TXService {

    /**
     * 获取Service
     * @param $obj
     * @return TXSingleDAO
     */
    public function __get($obj)
    {
        if (substr($obj, -3) == 'DAO') {
            return TXFactory::create($obj);
        }
    }
}