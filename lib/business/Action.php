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
     * csrf验证
     * @var bool
     */
    protected $csrfValidate = true;

    /**
     * restful接口
     * @var bool
     */
    protected $restApi = false;

    protected $request;
    protected $response;

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->request = App::$base->request;
        $this->response = App::$base->response;
        if ($this->restApi){
            $this->request->setRestApi();
        }
        //判断是否维护中
        if (isMaintenance){
            $this->response->display('Main/maintenance', [], ['webRoot' => App::$base->app_config->get('webRoot')], false);
        }
        if ($this->csrfValidate && !$this->request->validateCsrfToken()){
            header(App::$base->config->get(401, 'http'));
            echo $this->error("Unauthorized");
            exit;
        }
        // 权限验证
        $this->valid_privilege();
        $this->request->createCsrfToken();
        $this->response->setContentType();
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
            foreach ($privileges as $method => $privilege){
                if (is_callable([$this->privilegeService, $method])){
                    if (!isset($privilege['requires']) || empty($privilege['requires'])){
                        $privilege['requires'] = [['actions'=>$privilege['actions'] ?: [],
                            'params'=>isset($privilege['params']) ? $privilege['params'] : []]];
                    }
                    foreach ($privilege['requires'] as $require){
                        $actions = $require['actions'];
                        if ($actions === '*' || (is_array($actions) && in_array($this->request->getMethod(true), $actions))){
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
        $form->init($this->request->params(), $method);
        return $form;
    }

    /*********************************以下api都已废弃，将在后续版本删除********************************/

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
        Logger::warn('please use $this->request->param() instead');
        return $this->request->param($key, $default);
    }

    /**
     * 获取POST参数
     * @param $key
     * @param null $default
     * @return float|int|mixed|null
     */
    public function post($key, $default=null)
    {
        Logger::warn('please use $this->request->post() instead');
        return $this->request->post($key, $default);
    }

    /**
     * 获取GET参数
     * @param $key
     * @param null $default
     * @return float|int|mixed|null
     */
    public function get($key, $default=null)
    {
        Logger::warn('please use $this->request->get() instead');
        return $this->request->get($key, $default);
    }

    /**
     * 获取json数据
     * @param $key
     * @param null $default
     * @return float|int|mixed|null
     */
    public function getJson($key, $default=null)
    {
        Logger::warn('please use $this->request->json() instead');
        return $this->request->json($key, $default);
    }

    /**
     * display to json
     * @param $data
     * @param bool $encode
     * @return JSONResponse
     */
    public function json($data, $encode=false)
    {
        Logger::warn('please use $this->response->json() instead');
        return $this->response->json($data, $encode);
    }

    /**
     * @param array $ret
     * @param bool $encode
     * @return JSONResponse
     */
    public function correct($ret=[], $encode=false)
    {
        Logger::warn('please use $this->response->correct() instead');
        return $this->response->correct($ret, $encode);
    }

    /**
     * @param string $msg
     * @param bool $json 是否强制显示json
     * @param bool $encode
     * @return JSONResponse|Response
     */
    public function error($msg="数据异常", $json=false, $encode=false)
    {
        Logger::warn('please use $this->response->error() instead');
        return $this->response->error($msg, $json, $encode);
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
        Logger::warn('please use $this->response->display() instead');
        return $this->response->display($view, $params, $objects);
    }
}