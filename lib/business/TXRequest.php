<?php
/**
 * Tencent is pleased to support the open source community by making Biny available.
 * Copyright (C) 2017 THL A29 Limited, a Tencent company. All rights reserved.
 * Licensed under the BSD 3-Clause License (the "License"); you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * https://opensource.org/licenses/BSD-3-Clause
 * Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
 * Class TXRequest
 */

namespace biny\lib;
use TXApp;

class TXRequest {
    private $module;
    /**
     * @var TXAction
     */
    private $action=null;
    private $method=null;
    private $csrfToken = null;
    private $_hostInfo = null;
    private $_securePort = null;
    private $_port = null;
    private $_isSecure = null;
    private $config;

    /**
     * @var null|TXRequest
     */
    private static $_instance = null;

    /**
     * 单例模式
     * @param $module
     * @param null $method
     * @return null|TXRequest
     */
    public static function create($module, $method=null)
    {
        if (NULL === self::$_instance){
            self::$_instance = new self($module, $method);
        }
        return self::$_instance;
    }

    /**
     * @return null|TXRequest
     */
    public static function getInstance()
    {
        if (NULL === self::$_instance){
            return new self(null);
        }
        return self::$_instance;
    }

    private function __construct($module, $method=null)
    {
        $this->config = TXApp::$base->config->get('request');
        $this->module = $module;
        $this->method = $method ?: 'index';
        $this->csrfToken = $this->getCookie($this->config['csrfToken']);
    }

    /**
     * @param null $key
     * @return mixed
     */
    public function getCookie($key=null)
    {
        if ($key){
            return isset($_COOKIE[$key]) ? $_COOKIE[$key] : null;
        } else {
            return $_COOKIE;
        }
    }

    /**
     * 设置cookie
     * @param $key
     * @param $value
     * @param int $expire
     * @param string $path
     * @param null $domain
     */
    public function setCookie($key, $value, $expire=86400, $path='/', $domain=null)
    {
        setcookie($key, $value, time()+$expire, $path, $domain);
    }

    /**
     * 获取对应csrfToken
     * @return null|string
     */
    public function createCsrfToken()
    {
        if (!$this->csrfToken || !$this->getCookie($this->config['trueToken'])){
            $trueToken = $this->generateCsrf();
            $this->csrfToken = $this->createCsrfCookie($trueToken);
            $trueKey = $this->config['trueToken'];
            $csrfKey = $this->config['csrfToken'];
            $this->setCookie($trueKey, $this->hashData(serialize([$trueKey, $trueToken]), 'platformtest'));
            $this->setCookie($csrfKey, $this->csrfToken);
        }
        return $this->csrfToken;
    }

    private function hashData($data, $key)
    {
        $hash = hash_hmac('sha256', $data, $key);
        return $hash . $data;
    }

    /**
     * @param $token
     * @return string
     */
    public function createCsrfCookie($token)
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_-.';
        $mask = substr(str_shuffle(str_repeat($chars, 5)), 0, 8);
        return str_replace('+', '.', base64_encode($mask . $this->xorTokens($token, $mask)));
    }

    private function xorTokens($token1, $token2)
    {
        $n1 = mb_strlen($token1, '8bit');
        $n2 = mb_strlen($token2, '8bit');
        if ($n1 > $n2) {
            $token2 = str_pad($token2, $n1, $token2);
        } elseif ($n1 < $n2) {
            $token1 = str_pad($token1, $n2, $n1 === 0 ? ' ' : $token1);
        }

        return $token1 ^ $token2;
    }

    /**
     * 获取csrf
     * @return null
     */
    public function getCsrfToken()
    {
        return $this->csrfToken;
    }

    /**
     * 获取随机字符串
     * @param int $len
     * @return string
     */
    private function generateCsrf($len = 32)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';
        for ($i = 0; $i < $len; $i++) {
            $code .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $code;
    }

    /**
     * 判断子网掩码是否一致
     * @param $addr
     * @param $cidr
     * @return bool
     */
    private function matchCIDR($addr, $cidr) {
        list($ip, $mask) = explode('/', $cidr);
        return (ip2long($addr) >> (32 - $mask) == ip2long($ip) >> (32 - $mask));
    }

    /**
     * 验证csrfToken
     */
    public function validateCsrfToken()
    {
        if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
            $method = strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
        } else {
            $method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';
        }
        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) {
            return true;
        }
        $ips = $this->config['csrfWhiteIps'];
        foreach ($ips as $ip){
            if ($this->matchCIDR($this->getUserIp(), $ip)){
                return true;
            }
        }
        $trueToken = $this->config['trueToken'];
        $csrfPost = $this->config['csrfPost'];
        $csrfHeader = 'HTTP_'.str_replace('-', '_', $this->config['csrfHeader']);

        $trueToken = $_COOKIE[$trueToken];
        $test = @hash_hmac('sha256', '', '', false);
        $hashLength = mb_strlen($test, '8bit');
        $trueToken = unserialize(mb_substr($trueToken, $hashLength, mb_strlen($trueToken, '8bit'), '8bit'))[1];
        $token = isset($_POST[$csrfPost]) ? $_POST[$csrfPost] : (isset($_SERVER[$csrfHeader]) ? $_SERVER[$csrfHeader] : null);

        $token = base64_decode(str_replace('.', '+', $token));
        $n = mb_strlen($token, '8bit');
        if ($n <= 8) {
            return false;
        }
        $mask = mb_substr($token, 0, 8, '8bit');
        $token = mb_substr($token, 8, $n-8, '8bit');

        $n1 = mb_strlen($mask, '8bit');
        $n2 = mb_strlen($token, '8bit');
        if ($n1 > $n2) {
            $token = str_pad($token, $n1, $token);
        } elseif ($n1 < $n2) {
            $mask = str_pad($mask, $n2, $n1 === 0 ? ' ' : $mask);
        }
        $token = $mask ^ $token;
        return $token === $trueToken;

    }

    /**
     * 获取模块
     * @param bool $action
     * @return TXAction|TXSingleDAO|mixed
     * @throws TXException
     */
    public function getModule($action=false)
    {
        if ($action){
            if (null === $this->action){
                if (!preg_match("/^[\\w_]+$/", $this->module)){
                    throw new TXException(2001, $this->module."Action");
                }
                $this->action = TXFactory::create($this->module."Action");
            }
            return $this->action;
        } else {
            return $this->module;
        }
    }

    /**
     * 获取方法
     * @param bool $row
     * @return null|string
     */
    public function getMethod($row=false)
    {
        if ($row){
            return $this->method;
        } else {
            if ($this->action && $this->action->getRestful()){
                if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
                    $method = strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
                } else {
                    $method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';
                }
                return $method."_".$this->method;
            } else {
                return 'action_' . $this->method;
            }
        }
    }

    /**
     * 获取header内容
     * @param $key
     * @return null
     */
    public function header($key)
    {
        $key = 'HTTP_'.strtoupper(str_replace('-', '_', $key));
        return isset($_SERVER[$key]) ? $_SERVER[$key] : null;
    }

    /**
     * 判断是否强制返回tpl
     * @return null
     */
    public function isShowTpl()
    {
        return $this->header($this->config['showTpl']);
    }

    /**
     * 是否异步请求
     * @return bool
     */
    public function isAjax()
    {
        return $this->header('X_REQUESTED_WITH') === 'XMLHttpRequest';
    }

    /**
     * @param bool $query
     * @return mixed|string
     * @throws TXException
     */
    public function getUrl($query=true)
    {
        if (RUN_SHELL) {
            global $argv;
            return $argv[1];
        } elseif (isset($_SERVER['HTTP_X_REWRITE_URL'])) { // IIS
            $requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
        } elseif (isset($_SERVER['REQUEST_URI'])) {
            $requestUri = $_SERVER['REQUEST_URI'];
            if ($requestUri !== '' && $requestUri[0] !== '/') {
                $requestUri = preg_replace('/^(http|https):\/\/[^\/]+/i', '', $requestUri);
            }
        } elseif (isset($_SERVER['ORIG_PATH_INFO'])) { // IIS 5.0 CGI
            $requestUri = $_SERVER['ORIG_PATH_INFO'];
            if (!empty($_SERVER['QUERY_STRING'])) {
                $requestUri .= '?' . $_SERVER['QUERY_STRING'];
            }
        } else {
            throw new TXException(6000);
        }
        if (!$query && strpos($requestUri, '?')){
            $requestUri = strstr($requestUri, '?', true);
        }
        return $requestUri;
    }

    /**
     * 获取根URL
     * @param bool $host
     * @return string
     */
    public function getBaseUrl($host=false)
    {
        if (RUN_SHELL){
            global $argv;
            return $argv[1];
        } else {
            return $host ? $this->getHostInfo().TXApp::$base->router->rootPath : TXApp::$base->router->rootPath;
        }
    }

    /**
     * @return null|string
     */
    public function getHostInfo()
    {
        if ($this->_hostInfo === null) {
            $secure = $this->getIsSecureConnection();
            $http = $secure ? 'https' : 'http';
            if (isset($_SERVER['HTTP_HOST'])) {
                $this->_hostInfo = $http . '://' . $_SERVER['HTTP_HOST'];
            } else {
                $this->_hostInfo = $http . '://' . $_SERVER['SERVER_NAME'];
                $port = $secure ? $this->getSecurePort() : $this->getPort();
                if (($port !== 80 && !$secure) || ($port !== 443 && $secure)) {
                    $this->_hostInfo .= ':' . $port;
                }
            }
        }

        return $this->_hostInfo;
    }

    /**
     * @return int|null
     */
    public function getPort()
    {
        if ($this->_port === null) {
            $this->_port = !$this->getIsSecureConnection() && isset($_SERVER['SERVER_PORT']) ? (int) $_SERVER['SERVER_PORT'] : 80;
        }

        return $this->_port;
    }

    /**
     * @return bool|null
     */
    public function getIsSecureConnection()
    {
        if ($this->_isSecure === null){
            $this->_isSecure = isset($_SERVER['HTTPS']) && (strcasecmp($_SERVER['HTTPS'], 'on') === 0 || $_SERVER['HTTPS'] == 1)
                || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') === 0;
        }
        return $this->_isSecure;
    }

    /**
     * @return int|null
     */
    public function getSecurePort()
    {
        if ($this->_securePort === null) {
            $this->_securePort = $this->getIsSecureConnection() && isset($_SERVER['SERVER_PORT']) ? (int) $_SERVER['SERVER_PORT'] : 443;
        }

        return $this->_securePort;
    }

    /**
     * @return mixed
     */
    public function getServerName()
    {
        return $_SERVER['SERVER_NAME'];
    }

    /**
     * @return int
     */
    public function getServerPort()
    {
        return (int) $_SERVER['SERVER_PORT'];
    }

    /**
     * @return null
     */
    public function getReferrer()
    {
        return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
    }

    /**
     * @return null
     */
    public function getUserAgent()
    {
        return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
    }

    /**
     * 获取ServerIP
     * @return string
     */
    public function getServerIP()
    {
        return $_SERVER['SERVER_ADDR'] ?: gethostbyname($_SERVER['SERVER_NAME']);
    }

    /**
     * 获取ip
     * @return null
     */
    public function getUserIP()
    {
        if (isset($this->config['userIP']) && $this->config['userIP']){
            $userIP = $this->header($this->config['userIP']);
            return $userIP ?: (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null);
        } else {
            return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
        }
    }

    /**
     * 通关ua判断是否为手机
     * @return bool
     */
    public function isMobile()
    {
        //正则表达式,批配不同手机浏览器UA关键词。
        $regex_match = "/(nokia|iphone|android|motorola|^mot\-|softbank|foma|docomo|kddi|up\.browser|up\.link|";
        $regex_match .= "htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|";
        $regex_match .= "blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|";
        $regex_match .= "symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|";
        $regex_match .= "jig\s browser|hiptop|^ucweb|^benq|haier|^lct|opera\s*mobi|opera\*mini|320×320|240×320|176×220";
        $regex_match .= "|mqqbrowser|juc|iuc|ios|ipad";
        $regex_match .= ")/i";

        return isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE']) or preg_match($regex_match, strtolower($_SERVER['HTTP_USER_AGENT']));
    }

    /**
     * 获取ContentType
     * @return mixed
     */
    public function getContentType()
    {
        return $_SERVER['CONTENT_TYPE'];
    }

    //设置默认编码
    public function setContentType($contentType='text/html', $charset='utf-8')
    {
        header('Content-type: ' . $contentType.'; charset='.$charset);
    }

    public function redirect($url)
    {
        header("Location:$url");
        exit();
    }
}