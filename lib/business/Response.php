<?php
/**
 * Tencent is pleased to support the open source community by making Biny available.
 * Copyright (C) 2017 THL A29 Limited, a Tencent company. All rights reserved.
 * Licensed under the BSD 3-Clause License (the "License"); you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * https://opensource.org/licenses/BSD-3-Clause
 * Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
 * Class Response
 */

namespace biny\lib;
use App;

class Response {
    /**
     * @var string 视图名称
     */
    private $view;
    private $params;
    private $objects;

    public $title=null;
    public $keywords=null;
    public $descript=null;

    private $config;

    /**
     * @var null|Request
     */
    private static $_instance = null;

    /**
     * @return null|Request
     */
    public static function getInstance()
    {
        if (NULL === self::$_instance){
            return new self();
        }
        return self::$_instance;
    }

    private function __construct()
    {

    }

    /**
     * @param $view
     * @param array $params
     * @param array $objects
     * @param bool $return
     * @return $this
     * @throws BinyException
     */
    public function display($view, $params=[], $objects=[], $return=true)
    {
        $this->view = $view;
        $this->params = $params;
        $this->objects = $objects;
        $this->config = App::$base->config->get('response');
        if ($return) {
            return $this;
        } else {
            echo $this->getContent();
            exit;
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
     * 获取csrf
     * @return null
     */
    private function getCsrfToken()
    {
        return App::$base->request->getCsrfToken();
    }

    /**
     * 实体化转义
     * @param $content
     * @return string
     */
    private function encode($content)
    {
        return BinyString::encode($content);
    }

    /**
     * 获得模板渲染后的内容
     * @return string
     * @throws BinyException
     */
    public function getContent()
    {
        if ($this->config['paramsType'] == 'keys'){
            //老版本兼容过滤XSS
            foreach ($this->params as $key => &$param) {
                if (!in_array($key, $this->objects)){
                    if (is_string($param)){
                        $param = BinyString::encode($param);
                    } else if (is_array($param)){
                        $param = new BinyArray($param);
                    }
                }
            }
            unset($param);
            extract($this->params);

        } else {
            if (!isset($this->config['objectEncode']) || $this->config['objectEncode']){
                //防XSS注入
                foreach ($this->objects as &$object) {
                    if (is_string($object)){
                        $object = $this->encode($object);
                    } elseif (is_array($object)) {
                        $object = new BinyArray($object);
                    }
                }
                unset($object);
            }
            $key = $this->config['paramsKey'];
            $this->objects[$key] = new BinyArray($this->params);
            extract($this->objects);
        }

        ob_start();
        //include template
        $lang = Language::getLanguage();
        $file = sprintf('%s/template/%s%s.tpl.php', App::$app_root, $this->view, $lang ?'.'.$lang : "");
        if (!is_readable($file)){
            $file = sprintf('%s/template/%s.tpl.php', App::$app_root, $this->view);
        }
        if (!is_readable($file)){
            throw new BinyException(2005, $this->view);
        }
        include $file;
        Logger::showLogs();

        $content = ob_get_clean();
        return $content;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getContent();
    }

    /**
     * display to json
     * @param $data
     * @param bool $encode
     * @return JSONResponse
     */
    public function json($data, $encode=false)
    {
        $config = App::$base->config->get('response');
        if ($config['jsonContentType']){
            $this->setContentType($config['jsonContentType']);
        }
        echo new JSONResponse($data, $encode);
        exit;
    }

    /**
     * @param array $ret
     * @param bool $encode
     * @return JSONResponse
     */
    public function correct($ret=[], $encode=false)
    {
        if (RUN_SHELL) {
            Logger::addLog($ret);
            if (is_array($ret) || is_object($ret)){
                $ret = var_export($ret, true);
            }
            echo "$ret\n";exit;
        } else {
            $data = ["flag" => true, "ret" => $ret];
            return $this->json($data, $encode);
        }
    }

    /**
     * @param string $msg
     * @param bool $json 是否强制显示json
     * @param bool $encode
     * @return JSONResponse|Response
     */
    public function error($msg="数据异常", $json=false, $encode=false)
    {
        Event::trigger(onError, [$msg]);
        if (RUN_SHELL) {
            Logger::addError($msg);
            if (is_array($msg) || is_object($msg)){
                $msg = var_export($msg, true);
            }
            echo "$msg\n";exit;
        } else {
            if (!$json && (App::$base->request->isShowTpl() || !App::$base->request->isAjax())){
                $config = App::$base->config->get('exception');
                return $this->display($config['errorTpl'], ['msg'=> $msg]);
            } else {
                $data = ["flag" => false, "error" => $msg];
                return $this->json($data, $encode);
            }
        }
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

/**
 * 获取多语言
 * @param $content
 * @return mixed
 */
function _L($content){
    return Language::getLanguage() ? Language::getContent($content) : $content;
}