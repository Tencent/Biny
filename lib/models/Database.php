<?php
/**
 * Tencent is pleased to support the open source community by making Biny available.
 * Copyright (C) 2017 THL A29 Limited, a Tencent company. All rights reserved.
 * Licensed under the BSD 3-Clause License (the "License"); you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * https://opensource.org/licenses/BSD-3-Clause
 * Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
 * Database
 */

namespace biny\lib;
use App;

class Database {
    private static $instance = [];
    private static $autocommit = true;

    /**
     * @param string $name
     * @param bool $instance
     * @return Database
     */
    public static function instance($name, $instance=true)
    {
        // 兼容异步模式
        if (!$instance){
            $dbConfig = App::$base->app_config->get($name, 'dns');
            return new self($dbConfig);
        }
        if (!isset(self::$instance[$name])) {
            $dbConfig = App::$base->app_config->get($name, 'dns');
            self::$instance[$name] = new self($dbConfig);
        }

        return self::$instance[$name];
    }

    const FETCH_TYPE_ALL = 0;
    const FETCH_TYPE_ONE = 1;
    const FETCH_TYPE_CURSOR = 2;


    /**
     * @var \mysqli
     */
    private $handler;

    public function __construct($config)
    {
        if (!$config || !isset($config['host']) || !isset($config['user']) || !isset($config['password']) || !isset($config['port'])){
            throw new BinyException(3001, ['unKnown']);
        }
        if (isset($config['keep-alive']) && $config['keep-alive']){
            $config['host'] = 'p:'.$config['host'];
        }
        $this->handler = mysqli_connect($config['host'], $config['user'], $config['password'], '', $config['port']);
        if (!$this->handler) {
            throw new BinyException(3001, [$config['host']]);
        }
        $this->handler->autocommit(self::$autocommit);
        $dataConfig = App::$base->config->get('database');
        if ($dataConfig['returnIntOrFloat']){
            $this->handler->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
        } else {
            $this->handler->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 0);
        }

        // 使用 set_charset 而不是 SET NAMES，可正确影响 mysqli_real_escape_string 的字符集感知
        $encode = isset($config['encode']) ? $config['encode'] : 'utf8mb4';
        if (!@$this->handler->set_charset($encode)) {
            // 兼容极旧版本，仍然回退到 SET NAMES
            mysqli_query($this->handler, "set NAMES {$encode}");
        }
    }

    /**
     * 字符集感知的转义（替代 addslashes）
     * @param string $string
     * @return string
     */
    public function escape($string)
    {
        if (!$this->handler) {
            // handler 未初始化时降级；正常路径不会走到这里
            return addslashes((string)$string);
        }
        return mysqli_real_escape_string($this->handler, (string)$string);
    }

    /**
     * 参数化查询：使用 ? 占位符，避免任何字符串拼接
     * @param string $sql   SQL with ? placeholders
     * @param array  $binds ['i'|'d'|'s'|'b' 混合类型自动推断] or 直接给值数组
     * @param int    $mode
     * @return array|bool|int
     */
    public function prepared($sql, array $binds = [], $mode = self::FETCH_TYPE_ALL)
    {
        $start = microtime(true);
        $stmt = $this->handler->prepare($sql);
        if (!$stmt) {
            Logger::addError(sprintf("prepare Error: %s [%s]", mysqli_error($this->handler), $sql), 'sql Error');
            return $mode === self::FETCH_TYPE_ALL ? [] : false;
        }
        if ($binds) {
            $types = '';
            $values = [];
            foreach ($binds as $v) {
                if (is_int($v))         { $types .= 'i'; }
                elseif (is_float($v))   { $types .= 'd'; }
                elseif (is_null($v))    { $types .= 's'; $v = null; }
                else                    { $types .= 's'; $v = (string)$v; }
                $values[] = $v;
            }
            $stmt->bind_param($types, ...$values);
        }
        $ok = $stmt->execute();
        $time = (microtime(true) - $start) * 1000;
        $config = App::$base->config->get('logger');
        if ($time > ($config['slowQuery'] ?: 1000)) {
            Logger::addError(sprintf('Slow Query: %s [%sms]', $sql, $time), 'SQL', WARNING);
        }
        if (!$ok) {
            Logger::addError(sprintf("execute Error: %s [%s]", $stmt->error, $sql), 'sql Error');
            $stmt->close();
            return $mode === self::FETCH_TYPE_ALL ? [] : false;
        }
        if ($mode === self::FETCH_TYPE_ALL) {
            $result = $stmt->get_result();
            $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
            $stmt->close();
            return $rows;
        } elseif ($mode === self::FETCH_TYPE_ONE) {
            $result = $stmt->get_result();
            $row = $result ? ($result->fetch_assoc() ?: []) : [];
            $stmt->close();
            return $row;
        } else {
            // 写操作
            $affected = $stmt->affected_rows;
            $insertId = $stmt->insert_id;
            $stmt->close();
            return $insertId ?: $affected;
        }
    }

    /**
     * 构建表达式
     * @param $field
     * @return object
     */
    public static function field($field)
    {
        return (object)$field;
    }

    /**
     * 开始事务
     */
    public static function start()
    {
        self::$autocommit = false;
        foreach (self::$instance as $db){
            $db->handler->autocommit(false);
        }
    }

    /**
     * 结束事务
     */
    public static function end()
    {
        self::rollback();
        self::$autocommit = true;
        foreach (self::$instance as $db){
            $db->handler->autocommit(true);
        }
    }

    /**
     * 回滚事务
     */
    public static function rollback()
    {
        foreach (self::$instance as $db){
            if (!self::$autocommit){
                $db->handler->rollback();
            }
        }
    }

    /**
     * 提交事务
     */
    public static function commit()
    {
        foreach (self::$instance as $db){
            if (!self::$autocommit){
                $db->handler->commit();
            }
        }
    }

    /**
     * 游标更新
     * @param $rs
     * @return array|null
     */
    public static function step($rs)
    {
        return is_array($rs) ? mysqli_fetch_assoc($rs[0]) : mysqli_fetch_assoc($rs);
    }

    /**
     * sql query data
     * @param string $sql
     * @param $key
     * @param int $mode
     * @return array
     */
    public function sql($sql, $key=null, $mode = self::FETCH_TYPE_ALL)
    {
        $start = microtime(true);
        $rs = mysqli_query($this->handler, $sql, $mode === self::FETCH_TYPE_CURSOR ? MYSQLI_USE_RESULT : MYSQLI_STORE_RESULT);
        $time = (microtime(true)-$start)*1000;
        $config = App::$base->config->get('logger');
        if ($time > ($config['slowQuery'] ?: 1000)){
            Logger::addError(sprintf('Slow Query: %s [%sms]', $sql, $time), 'SQL', WARNING);
            Logger::warn(sprintf('Slow Query: %s [%sms]', $sql, $time));
        }
        if ($rs) {
            if ($mode == self::FETCH_TYPE_ALL) {
                $result = [];
                while($row = mysqli_fetch_assoc($rs)) {
                    if ($key){
                        $result[$row[$key]] = $row;
                    } else {
                        $result[] = $row;
                    }

                }
                return $result;
            } else if ($mode == self::FETCH_TYPE_CURSOR){
                return [$rs, $this->handler];
            } else {
                $result = mysqli_fetch_assoc($rs) ?: [];
            }
            return $result;
        } else {
            Logger::addError(sprintf("sql Error: %s [%s]", mysqli_error($this->handler), $sql), 'sql Error');
            Logger::error(sprintf("%s [%s]", mysqli_error($this->handler), $sql), 'sql Error');
            return [];
        }
    }

    /**
     * sql execute
     * @param $sql
     * @param bool $id
     * @return bool|int|\mysqli_result|string
     */
    public function execute($sql, $id=false)
    {
        $dataConfig = App::$base->config->get('database');
        if (mysqli_query($this->handler, $sql)){
            if ($id){
                return mysqli_insert_id($this->handler);
            }
            return $dataConfig['returnAffectedRows'] ? mysqli_affected_rows($this->handler) : true;
        } else {
            Logger::addError(sprintf("sql Error: %s [%s]", mysqli_error($this->handler), $sql), 'sql Error:');
            Logger::error($sql, 'sql Error:');
            return $dataConfig['returnAffectedRows'] ? -1 : false;
        }
    }

    /**
     * 析构函数
     */
    public function __destruct()
    {
        if (!self::$autocommit){
            $this->handler->rollback();
            $this->handler->autocommit(true);
        }
    }

}