<?php
/**
 * Tencent is pleased to support the open source community by making Biny available.
 * Copyright (C) 2017 THL A29 Limited, a Tencent company. All rights reserved.
 * Licensed under the BSD 3-Clause License (the "License"); you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * https://opensource.org/licenses/BSD-3-Clause
 * Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
 * Router class
 */

namespace biny\lib;
use App;

class Router {
    public $rootPath = '';
    private $routerInfo;

    public static $ARGS;

    function __construct()
    {
        $this->routerInfo = App::$base->config->get('router');
        self::$ARGS = $_GET;
    }

    /**
     * 设置资源路径
     * @param $pathInfo
     */
    private function buildRootPath($pathInfo)
    {
        foreach ($pathInfo as $path){
            if ($path !== "index.php"){
                $path = str_replace(['"', "'", '<', '>'], '', $path);
                $this->rootPath .= "/$path";
            }
        }
    }

    /**
     * 获取路由信息
     * @return array|bool
     */
    private function getRouterInfo()
    {
        $scriptInfo = explode("/", trim($_SERVER['SCRIPT_NAME'], '/'));
        $this->buildRootPath($scriptInfo);
        if (substr($_SERVER['REQUEST_URI'], 0, strlen($this->rootPath."/static")) == $this->rootPath."/static"){
            header('HTTP/1.1 404 Not Found');
            header("status: 404 Not Found");
            echo 'Source File Not Found';
            exit;
        }
        App::$base->config->setAlias('web', $this->rootPath);
        App::$base->app_config->setAlias('web', $this->rootPath);
        $pathRoot = strpos($_SERVER['REQUEST_URI'], '?') ? strstr($_SERVER['REQUEST_URI'], '?', true) : $_SERVER['REQUEST_URI'];
        if (substr($pathRoot, -9) === 'index.php'){
            $pathRoot = substr($pathRoot, 0, -9);
        }
        if ($this->rootPath){
            $len = strpos($pathRoot, $this->rootPath) + strlen($this->rootPath);
            $pathRoot = substr($pathRoot, $len);
        }

        $path = $this->reRouter(rtrim($pathRoot, '/'));
        if ($path !== NULL){
            $pathRoot = $path;
        }
        $pathInfo = trim($pathRoot, '/') ? explode("/", trim($pathRoot, '/')) : false;
        if (!$pathInfo){
            return false;
        }
        $module = isset($pathInfo[0]) ? $pathInfo[0] : null;
        $method = isset($pathInfo[1]) ? $pathInfo[1] : null;
        return [$module, $method];
    }

    /**
     * 路由重定向
     * @param $url
     * @return array
     */
    private function reRouter($url)
    {
        $path = NULL;
        $rules = $this->routerInfo['routeRule'];
        foreach ($rules as $key => $value){
            $key = trim($key, '/');
            preg_match_all("/<([\w_]+):([^>]+)>/", $key, $matchs);
            foreach ($matchs[2] as &$val){
                $val = '('.$val.')';
            }
            unset($val);
            $matchs[0][] = '/';
            $matchs[0][] = '.';
            $matchs[2][] = '\/';
            $matchs[2][] = '\.';
            $key = str_replace($matchs[0], $matchs[2], $key);
            if (preg_match('/'.$key.'$/', $url, $args)){
                foreach ($matchs[1] as $key => $val){
                    self::$ARGS[$val] = $args[$key+1];
                }
                if (preg_match_all("/<([\w_]+)>/", $value, $matchs)){
                    $replaces = [];
                    foreach ($matchs[1] as &$val){
                        $replaces[] = isset(self::$ARGS[$val]) ? self::$ARGS[$val] : $val;
                    }
                    $value = str_replace($matchs[0], $replaces, $value);
                }
                $path = str_replace($args[0], $value, $url);
                break;
            }
        }
        return $path;
    }

    /**
     * 路由入口
     */
    public function router()
    {
        if ($pathInfo = $this->getRouterInfo()){
            List($module, $method) = $pathInfo;
            $module = $module ?: $this->routerInfo['base_action'];
        } else {
            $module = $this->routerInfo['base_action'];
            $method = null;
        }
        Request::create($module, $method);
    }

    /**
     * shell路由入口
     */
    public function shellRouter()
    {
        global $argv;
        if (isset($argv[1]) && substr($argv[1], 0, 1) == "-"){
            array_splice($argv, 1, 0, $this->routerInfo['base_shell']);
        }
        $router = isset($argv[1]) ? explode('/', $argv[1]) : [$this->routerInfo['base_shell']];
        $module = $router[0];
        $method = isset($router[1]) ? $router[1] : 'index';
        Request::create($module, $method);
    }

    /**
     * shell构造参数
     * @return array
     */
    public function getArgs()
    {
        global $argv, $argc;
        //构造参数
        $params = ['args'=>[], 'params'=>[]];
        $params['args'] = $argc > 1 ? array_slice($argv, 2) : [];
        foreach($params['args'] as $k => $param){
            if (preg_match_all("/--([\w_]+)=(.*)/", $param, $matchs)){
                $params['params'][$matchs[1][0]] = $matchs[2][0];
                unset($params['args'][$k]);
            }
        }
        $params['args'] = array_values($params['args']);
        self::$ARGS = $params['params'];
        return $params;
    }
}