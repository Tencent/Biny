<?php
/**
 * Class TXDispatcher
 */
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
        TXEvent::trigger(onRequest, array($requests));
        $result = $this->call($requests);
        return $result;
    }

    /**
     * @param $module
     * @param $request
     * @return mixed
     * @throws TXException
     */
    private function getAction($module, $request)
    {
        $object = new $module();
        if (method_exists($object, 'init')){
            $result = $object->init();
            if ($result instanceof TXResponse || $result instanceof TXJSONResponse){
                return $result;
            }
        }
        TXEvent::trigger(beforeAction, array($request));
        return $object;
    }

    /**
     * 执行请求
     * @param TXRequest $request
     * @throws TXException
     * @return mixed
     */
    private function call(TXRequest $request)
    {
        $module = $request->getModule() . 'Action';
        $method = $request->getMethod();
        $args = $this->getArgs($module, $method);

        $object = $this->getAction($module, $request);
        if ($object instanceof TXResponse || $object instanceof TXJSONResponse){
            TXEvent::trigger(afterAction, array($request));
            return $object;
        }

        if ($object instanceof TXAction) {
            $result = call_user_func_array([$object, $method], $args);
            TXEvent::trigger(afterAction, array($request));
            return $result;
        } else {
            throw new TXException(2001, $request->getModule(), 404);
        }
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
            throw new TXException(2002, array($method, $obj), 404);
        }
        $action = new ReflectionMethod($obj, $method);
        if ($action->getName() !== $method){
            throw new TXException(2002, array($method, $obj), 404);
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
        $shell = new $module($params);
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