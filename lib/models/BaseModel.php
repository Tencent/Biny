<?php


namespace biny\lib\models;


use App;
use biny\lib\traits\GetInstances;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BaseModel
 * @property Connection $db
 * @property Builder $tb
 * @property Builder $mtb
 */
abstract class BaseModel extends Model
{
    use GetInstances;

    public function __construct()
    {
        $capsule = new Capsule;

        $config = App::$base->app_config->get('database2', 'dns');
        if (is_array($config)) {
            foreach ($config as $key => $item) {
                $capsule->addConnection($item, $key);
            }
        }

        // Make this Capsule instance available globally via static methods... (optional)
        $capsule->setAsGlobal();
        // Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
        $capsule->bootEloquent();

        if ($this->table == '') {
            $called_class = get_called_class();
            $called_class = explode('\\', $called_class);
            $called_class = end($called_class);
            $table = $this->humpToLine($called_class);
            if (substr($table, -6) == '_model') {
                $table = substr($table, 0, -6);
            }
            $this->setTable($table);
        }

        parent::__construct();
    }

    /**
     * hump to line
     * @param string $str
     * @return string
     */
    private function humpToLine($str)
    {
        $str = lcfirst($str);
        $str = preg_replace_callback('/([A-Z]{1})/',
            function ($matches) {
                return '_' . strtolower($matches[0]);
            }, $str);
        return $str;
    }

    public function __get($key)
    {
        if ($key == 'db') {
            return Capsule::connection($this->connection);
        } elseif ($key == 'tb') {
            return Capsule::connection($this->connection)->table($this->table);
        } elseif ($key == 'mtb') {
            return Capsule::connection($this->connection)->table($this->table)->useWritePdo();
        }
        return parent::__get($key);
    }

    public function iInsert($data)
    {
        $data = (array)$data;
        return $this->tb->insertGetId($data);
    }

    public function iInsertBatch($data)
    {
        $data = (array)$data;
        foreach ($data as &$item) {
            $item = (array)$item;
        }
        unset($item);
        return $this->tb->insert($data);
    }

    public function iUpdateOrInsert($where, $data)
    {
        $where = (array)$where;
        $data = (array)$data;
        return $this->tb->updateOrInsert($where, $data);
    }

    final protected function setConn($name)
    {
        $this->connection = $name;
    }
}