<?php
/**
 * Tencent is pleased to support the open source community by making Biny available.
 * Copyright (C) 2017 THL A29 Limited, a Tencent company. All rights reserved.
 * Licensed under the BSD 3-Clause License (the "License"); you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * https://opensource.org/licenses/BSD-3-Clause
 * Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
 * Action config class
 */
namespace biny\lib;
use App;

/**
 * Class Action
 * @package biny\lib *
 * @property \app\service\privilegeService $privilegeService
 */
class Action
{
    /**
     * 请求参数
     * @var array
     */
    private $params;

    /**
     * POST参数
     * @var array
     */
    private $posts;

    /**
     * GET参数
     * @var array
     */
    private $gets;

    /**
     * JSON参数
     * @var array
     */
    private $jsons = NULL;

    /**
     * csrf验证
     * @var bool
     */
    protected $csrfValidate = true;

    /**
     * restful接口
     * @var bool
     */
    protected $restApi = false;

    /**
     * 构造函数
     */
    public function __construct()
    {
        if ($this->restApi){
            parse_str(file_get_contents('php://input'), $this->params);
            $this->params = array_merge($this->params, $_GET);
        } else {
            $this->params = array_merge($_REQUEST, Router::$ARGS);
        }
        $this->posts = $_POST;
        $this->gets = $_GET;
        //判断是否维护中
        if (isMaintenance){
            echo $this->display('Main/maintenance');
            exit;
        }
        if ($this->csrfValidate && !App::$base->request->validateCsrfToken()){
            header(App::$base->config->get(401, 'http'));
            echo $this->error("Unauthorized");
            exit;
        }
        // 权限验证
        $this->valid_privilege();
        App::$base->request->createCsrfToken();
        App::$base->request->setContentType();
    }

    /**
     * 获取Service|DAO
     * @param $obj
     * @return Service | DAO
     */
    public function __get($obj)
    {
        if (substr($obj, -7) == 'Service' || substr($obj, -3) == 'DAO') {
            return Factory::create($obj);
        }
    }

    public function getRestful()
    {
        return $this->restApi;
    }

    /**
     * 路由验证
     */
    private function valid_privilege()
    {
        if (method_exists($this, 'privilege') && $privileges = $this->privilege()){
            $request = App::$base->request;
            foreach ($privileges as $method => $privilege){
                if (is_callable([$this->privilegeService, $method])){
                    if (!isset($privilege['requires']) || empty($privilege['requires'])){
                        $privilege['requires'] = [['actions'=>$privilege['actions'] ?: [],
                            'params'=>isset($privilege['params']) ? $privilege['params'] : []]];
                    }
                    foreach ($privilege['requires'] as $require){
                        $actions = $require['actions'];
                        if ($actions === '*' || (is_array($actions) && in_array($request->getMethod(true), $actions))){
                            $params = isset($require['params']) ? $require['params'] : [];
                            array_unshift($params, $this);
                            if (!call_user_func_array([$this->privilegeService, $method], $params)){
                                if (isset($privilege['callBack']) && is_callable($privilege['callBack'])){
                                    call_user_func_array($privilege['callBack'], [$this, $this->privilegeService->getError()]);
                                }
                                throw new BinyException(6001, [$method, $this->privilegeService->getError()], 403);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Display to template
     * @param $view
     * @param array $params
     * @param array $objects
     * @return Response
     */
    public function display($view, $params=[], $objects=[])
    {
        return new Response($view, $params, $objects);
    }


    /**
     * 获取Form
     * @param $name
     * @param null $method
     * @return Form
     */
    public function getForm($name, $method=null)
    {
        $name = $name.'Form';
        /**
         * @var Form $form
         */
        $form = Factory::create($name);
        $form->init($this->params, $method);
        return $form;
    }

    /**
     * 获取原始Post数据
     * @return string
     */
    public function getRowPost()
    {
        return file_get_contents('php://input');
    }

    /**
     * 兼容原有api
     * @param $key
     * @param null $default
     * @return float|int|mixed|null
     */
    public function getParam($key, $default=null){return $this->param($key, $default);}
    public function getGet($key, $default=null){return $this->get($key, $default);}
    public function getPost($key, $default=null){return $this->post($key, $default);}

    /**
     * 获取请求参数
     * @param $key
     * @param null $default
     * @return float|int|mixed|null
     */
    public function param($key, $default=null)
    {
        if (App::$base->request->getContentType() == 'application/json' || App::$base->request->getContentType() == 'text/json'){
            return $this->getJson($key, $default);
        } else {
            return isset($this->params[$key]) ? $this->params[$key] : $default;
        }
    }

    /**
     * 获取POST参数
     * @param $key
     * @param null $default
     * @return float|int|mixed|null
     */
    public function post($key, $default=null)
    {
        return isset($this->posts[$key]) ? $this->posts[$key] : $default;
    }

    /**
     * 获取GET参数
     * @param $key
     * @param null $default
     * @return float|int|mixed|null
     */
    public function get($key, $default=null)
    {
        return isset($this->gets[$key]) ? $this->gets[$key] : $default;
    }

    /**
     * 获取json数据
     * @param $key
     * @param null $default
     * @return float|int|mixed|null
     */
    public function getJson($key, $default=null){
        if ($this->jsons === NULL){
            $this->jsons = json_decode($this->getRowPost(), true) ?: [];
        }
        return isset($this->jsons[$key]) ? $this->jsons[$key] : $default;
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
            App::$base->request->setContentType($config['jsonContentType']);
        }
        return new JSONResponse($data, $encode);
    }

    /**
     * @param array $ret
     * @param bool $encode
     * @return JSONResponse
     */
    public function correct($ret=[], $encode=false)
    {
        $data = ["flag" => true, "ret" => $ret];
        return $this->json($data, $encode);
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
        if (!$json && (App::$base->request->isShowTpl() || !App::$base->request->isAjax())){
            $config = App::$base->config->get('exception');
            return $this->display($config['errorTpl'], ['msg'=> $msg]);
        } else {
            $data = ["flag" => false, "error" => $msg];
            return $this->json($data, $encode);
        }
    }
}