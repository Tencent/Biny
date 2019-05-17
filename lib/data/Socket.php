<?php
/**
 * Tencent is pleased to support the open source community by making Biny available.
 * Copyright (C) 2017 THL A29 Limited, a Tencent company. All rights reserved.
 * Licensed under the BSD 3-Clause License (the "License"); you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * https://opensource.org/licenses/BSD-3-Clause
 * Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
 * Socket
 */

namespace biny\lib;
use App;

class Socket
{
    private static $_instance = [];

    /**
     * @param string $name
     * @return Socket
     */
    public static function instance($name='socket')
    {
        if (!isset(self::$_instance[$name])){
            $config = App::$base->app_config->get($name, 'dns');
            self::$_instance[$name] = new self($config);
        }
        return self::$_instance[$name];
    }

    /**
     * @var resource
     */
    private $handler;
    private $connect;

    public function __construct($config)
    {
        $this->connect = $config;
    }

    /**
     * 选择库
     * @param $name
     * @return Socket
     */
    public function choose($name)
    {
        return self::instance($name);
    }

    /**
     * 创建handler
     * @throws BinyException
     */
    private function connect()
    {
        $config = $this->connect;
        if ($config['type'] == SOL_UDP){
            $type = SOCK_DGRAM;
        } else {
            $type = SOCK_STREAM;
        }
        $autoThrow = isset($config['auto-throw']) ? $config['auto-throw'] : true;
        if( ($this->handler = socket_create(AF_INET, $type, $config['type'] ?: SOL_TCP)) === false) {
            if ($autoThrow){
                throw new BinyException(4001);
            } else {
                Logger::addError(BinyException::fmt_code(4001), 'SOCKET', WARNING);
                return false;
            }

        }
        if ($config['timeout']){
            socket_set_option($this->handler, SOL_SOCKET, SO_RCVTIMEO, ["sec"=>$config['timeout']/1000, "usec"=>$config['timeout']%1000]);
        }
        if (@socket_connect($this->handler, $config['host'], $config['port']) === false) {
            if ($autoThrow){
                throw new BinyException(4002, [$config['host'], $config['port']]);
            } else {
                Logger::addError(BinyException::fmt_code(4002, [$config['host'], $config['port']]), 'SOCKET', WARNING);
                return false;
            }
        }
        return true;
    }

    /**
     * 发送buff
     * @param $buff
     * @return bool
     */
    public function sendBuff($buff){
        if (!$this->handler){
            $this->connect();
        }
        if (is_array($buff)){
            $buff = json_encode($buff);
        }
        $len = strlen($buff);
        @socket_send($this->handler, $buff, $len, 0);
        return true;
    }


    /**
     * 收Buff
     * @return bool|mixed|string
     * @throws BinyException
     */
    public function revBuff(){
        if (!$this->handler){
            $this->connect();
        }
        $nCnt = @socket_recv($this->handler, $buf, 4, 0);
        if ($nCnt === false){
            return -1;//timeout
        }
        if ($nCnt != 4) {
            return false;
        }
        $ret = unpack("LLen", $buf);
        $len = $ret['Len'];
        $data='';
        $recvlen = $len;
        $i = 0;
        while ($recvlen > 0) {
            if (++$i == 100){
                throw new BinyException(4003);
            }
            $nCnt = @socket_recv($this->handler, $buf, $recvlen, 0);
            $data .= $buf;
            $recvlen -= $nCnt;
        }
//        $data = chop($data);
        if ($result = json_decode($data, true)){
            return $result;
        } else {
            return $data;
        }
    }

    /**
     * 发送socket请求
     * @param $buff
     * @return bool|mixed|string
     */
    public function send($buff){
        if (!$this->handler){
            $this->connect();
        }
        if ($this->sendBuff($buff)){
            return $this->revBuff();
        }
        return false;
    }
}