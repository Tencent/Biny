<?php
/**
 * Tencent is pleased to support the open source community by making Biny available.
 * Copyright (C) 2017 THL A29 Limited, a Tencent company. All rights reserved.
 * Licensed under the BSD 3-Clause License (the "License"); you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * https://opensource.org/licenses/BSD-3-Clause
 * Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
 * Class TXResponse
 */

namespace biny\lib;
use TXApp;

class TXResponse {
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
     * @param $view
     * @param array $params
     * @param array $objects 直接引用对象
     */
    public function __construct($view, $params=[], $objects=[])
    {
        $this->view = $view;
        $this->params = $params;
        $this->objects = $objects;
        $this->config = TXApp::$base->config->get('response');
    }

    /**
     * 获取csrf
     * @return null
     */
    private function getCsrfToken()
    {
        return TXApp::$base->request->getCsrfToken();
    }

    /**
     * 实体化转义
     * @param $content
     * @return string
     */
    private function encode($content)
    {
        return TXString::encode($content);
    }

    /**
     * 获得模板渲染后的内容
     * @return string
     * @throws TXException
     */
    public function getContent()
    {
        if ($this->config['paramsType'] == 'keys'){
            //老版本兼容过滤XSS
            foreach ($this->params as $key => &$param) {
                if (!in_array($key, $this->objects)){
                    if (is_string($param)){
                        $param = TXString::encode($param);
                    } else if (is_array($param)){
                        $param = new TXArray($param);
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
                        $object = new TXArray($object);
                    }
                }
                unset($object);
            }
            $key = $this->config['paramsKey'];
            $this->objects[$key] = new TXArray($this->params);
            extract($this->objects);
        }

        ob_start();
        //include template
        $lang = TXLanguage::getLanguage();
        $file = sprintf('%s/template/%s%s.tpl.php', TXApp::$app_root, $this->view, $lang ?'.'.$lang : "");
        if (!is_readable($file)){
            $file = sprintf('%s/template/%s.tpl.php', TXApp::$app_root, $this->view);
        }
        if (!is_readable($file)){
            throw new TXException(2005, $this->view);
        }
        include $file;
        TXLogger::showLogs();

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
}

/**
 * 获取多语言
 * @param $content
 * @return mixed
 */
function _L($content){
    return TXLanguage::getLanguage() ? TXLanguage::getContent($content) : $content;
}