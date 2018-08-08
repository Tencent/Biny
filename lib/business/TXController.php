<?php
/**
 * Tencent is pleased to support the open source community by making Biny available.
 * Copyright (C) 2017 THL A29 Limited, a Tencent company. All rights reserved.
 * Licensed under the BSD 3-Clause License (the "License"); you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * https://opensource.org/licenses/BSD-3-Clause
 * Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
 * Class TXDispatcher
 */

namespace biny\lib;
use TXApp;

class TXController {
    /**
     * @var TXRouter
     */
    private $router;

    public function __construct()
    {
        $this->router = TXApp::$base->router;
    }

    /**
     * router
     */
    private function router()
    {
        $this->router->router();
    }

    /**
     * 执行Action
     * @throws TXException
     * @return mixed
     */
    private function execute()
    {
        $requests = TXApp::$base->request;
        TXEvent::trigger(onRequest, [$requests]);
        $result = $this->call($requests);
        return $result;
    }

    /**
     * 执行请求
     * @param TXRequest $request
     * @throws TXException
     * @return mixed
     */
    private function call(TXRequest $request)
    {
        $action = $request->getModule(true);
        if (!($action instanceof TXAction)){
            throw new TXException(2001, $request->getModule(), 404);
        }
        if (method_exists($action, 'init')){
            $result = $action->init();
            if ($result instanceof TXResponse || $result instanceof TXJSONResponse){
                return $result;
            }
        }

        $method = $request->getMethod();
        $args = $this->getArgs($action, $method);

        TXEvent::trigger(beforeAction, [$request]);
        $result = call_user_func_array([$action, $method], $args);
        TXEvent::trigger(afterAction, [$request]);
        return $result;
    }

    /**
     * 获取默认参数
     * @param $obj
     * @param $method
     * @return array
     * @throws TXException
     */
    private function getArgs($obj, $method)
    {
        $params = TXRouter::$ARGS;
        $args = [];
        if (!method_exists($obj, $method)){
            throw new TXException(2002, [$method, get_class($obj)], 404);
        }
        $action = new \ReflectionMethod($obj, $method);
        if ($action->getName() !== $method){
            throw new TXException(2002, [$method, get_class($obj)], 404);
        }
        foreach ($action->getParameters() as $param) {
            $name = $param->getName();
            $args[] = isset($params[$name]) ? $params[$name] : ($param->isDefaultValueAvailable() ? $param->getDefaultValue() : null);
        }
        return $args;
    }

    /**
     * Dispatcher method
     */
    public function dispatcher()
    {
        $this->router();    //router
        $result = $this->execute(); //execute
        if ($result instanceof TXResponse) {    //view
            echo $result;
        } elseif ($result instanceof TXJSONResponse) {  //json数据
            echo $result;
        } else {
            echo $result;
        }
    }

    /**
     * Shell执行入口
     * @throws TXException
     */
    public function shellStart()
    {
        TXApp::$base->router->shellRouter();
        $module = TXApp::$base->request->getModule()."Shell";
        $method = TXApp::$base->request->getMethod();
        $params = TXApp::$base->router->getArgs();
        $shell = TXFactory::create($module);
        if ($shell instanceof TXShell){
            if (method_exists($shell, 'init')){
                $result = $shell->init();
                if ($result){
                    if (is_array($result) || is_object($result)){
                        $result = var_export($result, true);
                    }
                    echo "$result\n";exit;
                }
            }
            // 兼容原模式
            $args = $params['params'] ? $this->getArgs($shell, $method) : $params['args'];
            $result = call_user_func_array([$shell, $method], $args);
            if (is_array($result) || is_object($result)){
                $result = var_export($result, true);
            }
            echo "$result\n";exit;
        } else {
            throw new TXException(2006, $module);
        }
    }
}