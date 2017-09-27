<?php
/**
 * Created by PhpStorm.
 * User: billge
 * Date: 15-8-3
 * Time: 上午11:50
 * @method int sum($field)
 * @method int max($field)
 * @method int min($field)
 * @method int avg($field)
 * @method array distinct($field)
 * @method array find($field='')
 * @method array query($field='', $key=null)
 * @method array cursor($field='')
 * @method array select($sql, $querys=array())
 * @method array command($sql, $querys=array())
 * @method array count($field='')
 * @method array update($sets)
 * @method array addCount($sets)
 */
class TXFilter
{
    const valueKey = "__values__";

    /**
     * @var TXSingleDAO|TXDoubleDAO
     */
    protected $DAO;
    protected $conds = [];
    protected $methods = ['distinct', 'find', 'cursor', 'query', 'count', 'group', 'limit', 'order', 'addition', 'having', 'select', 'command', 'update', 'addCount'];
    protected $calcs = ['max', 'min', 'sum', 'avg', 'count'];

    /**
     * 静态创建
     * @param $DAO
     * @param $filter
     * @param string $type
     * @param null $cond
     * @return TXDoubleFilter|TXSingleFilter
     * @throws TXException
     */
    public static function create($DAO, $filter, $type="__and__", $cond=null)
    {
        if ($DAO instanceof TXSingleDAO){
            return new TXSingleFilter($DAO, $filter, $type, $cond);
        } elseif ($DAO instanceof TXDoubleDAO) {
            return new TXDoubleFilter($DAO, $filter, $type, $cond);
        } else {
            throw new TXException(3003, gettype($DAO));
        }
    }

    /**
     * 构造函数
     * @param TXSingleDAO|TXDoubleDAO $DAO
     * @param TXFilter $filter
     * @param string $type
     * @param null $cond
     * @throws TXException
     */
    public function __construct($DAO, $filter, $type="__and__", $cond=null)
    {
        if (!($DAO instanceof TXSingleDAO || $DAO instanceof TXDoubleDAO)){
            throw new TXException(3003, gettype($DAO));
        }
        if (!$filter){
            throw new TXException(3007);
        } elseif (is_array($filter)){
            if ($cond){
                $this->conds = [[$type => [[self::valueKey => $filter], $cond]]];
            } else {
                $this->conds = [[$type => [[self::valueKey => $filter]]]];
            }
        } elseif (null === $cond) {
           throw new TXException(3006, gettype($filter));
        } elseif (!$filter instanceof TXFilter) {
            throw new TXException(3004, gettype($filter));
        } elseif ($filter->getDAO() !== $DAO) {
            throw new TXException(3005);
        } elseif ($cond) {
            $this->conds = [[$type => [$filter->getConds()[0], $cond]]];
        } else {
            $this->conds = [[$type => [$filter->getConds()[0]]]];
        }
        $this->DAO = $DAO;

    }

    /**
     * 连表Where
     * @param $conds
     * @param string $type
     * @return string
     */
    protected function buildWhere($conds, $type='and')
    {
        $wheres = [];
        foreach ($conds as $values){
            foreach ($values as $key => $cond){
                if ($key == "__and__" || $key == "__or__"){
                    $key = str_replace("_", "", $key);
                    $sCond = $this->buildWhere($cond, $key);
                    if ($sCond){
                        $wheres[] = $sCond;
                    }
                } elseif ($key == self::valueKey){
                    $sCond = $this->DAO->buildWhere($cond, $type);
                    if ($sCond){
                        $wheres[] = $sCond;
                    }
                }
            }
        }
        if (!$wheres){
            return '';
        } elseif (count($wheres) == 1){
            return $wheres[0];
        }
        return "(" . join(") {$type} (", $wheres) . ")";
    }

    public function getDAO()
    {
        return $this->DAO;
    }

    public function getConds()
    {
        return $this->conds;
    }

    /**
     *
     * @return string
     */
    public function __toLogger()
    {
        return ['DAO' => $this->DAO->getDAO(), 'conds' => $this->conds];
    }
}