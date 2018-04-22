<?php
/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Leevel\Router;

use Closure;
use Exception;
use RuntimeException;
use ReflectionMethod;
use ReflectionException;
use InvalidArgumentException;
use Leevel\{
    Http\Request,
    Di\IContainer,
    Http\Response,
    Option\TClass,
    Support\TMacro,
    Mvc\IController,
    Pipeline\Pipeline
};
use Leevel\Router\Match{
    Cli as CliMatch,
    Url as UrlMatch,
    PathInfo as PathInfoMatch
}

/**
 * 路由解析
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.01.10
 * @version 1.0
 */
class Router implements IRouter
{
    use TMacro;

    //use TClass;

    /**
     * IOC Container
     *
     * @var \Leevel\Di\IContainer
     */
    protected $container;

    /**
     * http 请求
     *
     * @var \Leevel\Http\Request
     */
    protected $request;

    /**
     * 应用名字
     *
     * @var string
     */
    protected $app;

    /**
     * 控制器名字
     *
     * @var string
     */
    protected $controller;

    /**
     * 方法名字
     *
     * @var string
     */
    protected $action;

    /**
     * 全局路由绑定中间件
     *
     * @var array
     */
    protected $globalMiddlewares = [];

    /**
     * 路由绑定中间件
     *
     * @var array
     */
    protected $arrMiddlewares = [];

    /**
     * 当前的中间件
     *
     * @var array
     */
    protected $currentMiddlewares;

    /**
     * 路由匹配变量
     *
     * @var array
     */
    protected $arrVariable = [];

    /**
     * 路由匹配数据
     * 
     * @var array
     */
    protected $matcheData = [];

    protected $bind;

    /**
     * 路由匹配初始化数据
     * 
     * @var array
     */
    protected static $matcheDataInit = [
        self::APP => self::DEFAULT_APP,
        self::CONTROLLER => null,
        self::ACTION => null,
        self::PREFIX => null,
        self::PARAMS => null,
        self::MIDDLEWARES => null,
        self::BIND => null
    ];

    protected $basepaths = [];

    protected $groups = [];

    protected $routers = [];
    
    /**
     * 中间件分组 
     *
     * @var array
     */
    protected $middlewareGroups = [];

    /**
     * 中间件别名 
     *
     * @var array
     */
    protected $middlewareAlias = [];



    /**
     * 控制器相对目录
     * 
     * @var string
     */
    protected $controllerDir = 'App\Controller';

    /**
     * 构造函数
     *
     * @param \Leevel\Di\IContainer $container
     * @param \Leevel\Http\Request $request
     * @param array $arrOption
     * @return void
     */
    public function __construct(IContainer $container, request $request, array $arrOption = [])
    {
        $this->container = $container;
        $this->request = $request;
        //$this->options($arrOption);
    }

    /**
     * 执行请求
     *
     * @return $this
     */
    public function run()
    {
        // 初始化
        $this->initRequest();

        // 匹配路由
        $this->matchRouter();

        // 穿越中间件
        $this->throughMiddleware($this->request);
    }

    /**
     * 穿越中间件
     *
     * @param \Leevel\Http\Request $passed
     * @param array $passedExtend
     * @return void
     */
    public function throughMiddleware(request $passed, array $passedExtend = [])
    {
        if (is_null($this->currentMiddlewares)) {
            $this->currentMiddlewares = $this->getMiddleware();
        }

        $method = ! $passedExtend ? 'handle' : 'terminate';

        if (! $this->currentMiddlewares[$method]) {
            return;
        }

        if ($this->currentMiddlewares[$method]) {
            (new Pipeline($this->container))->

            send($passed)->

            send($passedExtend)->

            through($this->currentMiddlewares[$method])->

            then();
        }
    }

    /**
     * 执行绑定
     *
     * @return mixed|void
     */
    public function doBind()
    {
        return $this->container->call($this->bind, $this->arrVariable);
    }

    /**
     * 获取绑定的中间件
     * 暂时不做重复过滤，允许中间件多次执行
     *
     * @return array
     */
    public function getMiddleware()
    {
        return [
            'handle' => array_merge($this->globalMiddlewares['handle'], $this->middlewares()['handle']),
            'terminate' => array_merge($this->globalMiddlewares['terminate'], $this->middlewares()['terminate'])
        ];
    }

    /**
     * 注册绑定中间件
     *
     * @param string $sMiddlewareName
     * @param string|array $mixMiddleware
     * @return void
     */
    public function middleware2($sMiddlewareName, $mixMiddleware)
    {
        if (! $mixMiddleware) {
            throw new InvalidArgumentException(sprintf('Middleware %s disallowed empty.', $sMiddlewareName));
        }

        if (! isset($this->arrMiddlewares[$sMiddlewareName])) {
            $this->arrMiddlewares[$sMiddlewareName] = [];
        }

        $this->arrMiddlewares[$sMiddlewareName] = array_merge($this->arrMiddlewares[$sMiddlewareName], $this->parseMiddlewares($mixMiddleware));
    }

    /**
     * 批量注册绑定中间件
     *
     * @param array $arrMiddleware
     * @return void
     */
    public function middlewares2222(array $arrMiddleware)
    {
        foreach ($arrMiddleware as $sMiddlewareName => $mixMiddleware) {
            $this->middleware($sMiddlewareName, $mixMiddleware);
        }
    }

    /**
     * 设置控制器相对目录
     *
     * @param string $controllerDir
     * @return void
     */
    public function setControllerDir(?string $controllerDir) {
        $controllerDir = str_replace('/', '\\', $controllerDir);
        $this->controllerDir = $controllerDir;
    }

    public function getControllerDir() {
        return $this->controllerDir;
    }

    /**
     * 添加匹配变量
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function addVariable($name, $value)
    {
        $this->arrVariable[$name] = $value;
    }

    /**
     * 取得当前路由
     *
     * @return array
     */
    public function getRouters() {
        return $this->routers;
    }
    public function getBasepaths() {
        return $this->basepaths;
    }
    public function getGroups() {
        return $this->groups;
    }

    public function setRouters(array $routers) {
        $this->routers = $routers;
    }
    public function setBasepaths(array $basepaths) {
        $this->basepaths = $basepaths;
    }
    public function setGroups(array $groups) {
        $this->groups = $groups;
    }
    public function setMiddlewareGroups(array $middlewareGroups) {
        $this->middlewareGroups = $middlewareGroups;
    }
    
    public function setMiddlewareAlias(array $middlewareAlias) {
        $this->middlewareAlias = $middlewareAlias;
    }
    
    public function getMiddlewareGroups() {
        return $this->middlewareGroups;
    }
    
    public function getMiddlewareAlias() {
        return $this->middlewareAlias;
    }
    public function setGlobalMiddlewares(array $middlewares) {
        $this->globalMiddlewares = $middlewares;
    }

    // public function getGlobalMiddlewares() {
    //     return $this->globalMiddlewares;
    // }
    /**
     * 分析 url 数据
     * like [:home/blog/index?arg1=1&arg2=2]
     *
     * @param string $sUrl
     * @return array
     */
    public function parseNodeUrl($sUrl)
    {
        $arrData = ['params' => []];

        if (strpos($sUrl, '?') >= 0) {
            $tmp = explode('?', $sUrl);

            // 额外参数
            if (!empty($tmp[1])) {
                foreach (explode('&', $tmp[1]) as $strQuery) {
                    $strQuery = explode('=', $strQuery);
                    $arrData['params'][$strQuery[0]] = $strQuery[1];
                }
            }
        } else {
            $tmp = [$sUrl];
        }   

        $urls = explode('/',$tmp[0]);

        if (strpos($urls[0], ':') === 0) {
            $arrData[static::APP] = substr(array_shift($urls), 1);
        }

        if (count($urls) == 1) {
            $result[Router::CONTROLLER] = array_pop($urls);
        } else { 
            if ($urls) {
                $arrData[Router::ACTION] = array_pop($urls);
            }

            if ($urls) {
                $arrData[Router::CONTROLLER] = array_pop($urls);
            }

            if ($urls) {
                $arrData[Router::PREFIX] = implode('\\', $urls);
            }
        }


        return $arrData;
    }

    /**
     * 初始化请求
     *
     * @return void
     */
    public function initRequest()
    {
        $this->app = null;
        $this->controller = null;
        $this->action = null;
        $this->matcheData = self::$matcheDataInit;
    }

    /**
     * 取回应用名
     *
     * @return string
     */
    public function app()
    {
        if ($this->app) {
            $app = $this->app;
        } else {
            if (($this->app = env('app_name'))) {
                $app = $this->app;
            } else {
                $app = $this->app = $this->matcheData[static::APP];
            }
        }

        return ucfirst($app);
    }

    /**
     * 取回控制器名
     *
     * @return string
     */
    public function controller()
    {
        if ($this->controller) {
            $controller = $this->controller;
        } else {
            if (($this->controller = env('controller_name'))) {
                $controller = $this->controller;
            } else {
                $controller = $this->matcheData[static::CONTROLLER];
            }
        }

        return ucfirst($controller);
    }

    /**
     * 取回方法名
     *
     * @return string
     */
    public function action()
    {
        if ($this->action) {
            $action = $this->action;
        } else {
            if (($this->action = env('action_name'))) {
                $action = $this->action;
            } else {
                $action = $this->matcheData[static::ACTION];
            }
        }

        if (strpos($action, '-') !== false) {
            $action = str_replace('-', '_', $action);
        } 

        if (strpos($action, '_') !== false) {
            $action = '_' . str_replace('_', ' ', $action);
            $action = ltrim(str_replace(' ', '', ucwords($action)), '_');
        }

        return $action;
    }

    /**
     * 取回控制器前缀
     *
     * @return string
     */
    public function prefix()
    {
        return $this->matcheData[static::PREFIX] ?? '';
    }

    /**
     * 取回匹配参数
     *
     * @return array
     */
    public function params()
    {
        return $this->matcheData[static::PARAMS] ?? [];
    }

    /**
     * 取回匹配中间件
     *
     * @return array
     */
    public function middlewares()
    {
        return $this->matcheData[static::MIDDLEWARES] ?? [];
    }

    /**
     * 取回匹配绑定
     *
     * @return array
     */
    public function bind()
    {
        return $this->matcheData[static::BIND];
    }  

    /**
     * 路由匹配
     * 高效匹配，如果默认 pathInfo 路由能够匹配上则忽略 swagger 路由匹配
     *
     * @return void
     */
    protected function matchRouter()
    {
echo 'xx';
exit();

        if ($this->request->isCli()) {
            $data = (new CliMatch)->matche($this, $this->request);
            $this->matcheData = array_merge(self::$matcheDataInit, $data);
            $bind = $this->parseDefaultBind();
            if ($bind === false) {
                $this->nodeNotRegistered();
            }
        } else {
            // 默认 pathInfo 匹配
            $data = (new PathInfoMatchMatch)->matche($this, $this->request);
            $this->matcheData = array_merge(self::$matcheDataInit, $data);
            $bind = $this->parseDefaultBind();

            if ($bind === false) {
                $data = (new UrlMatchMatch)->matche($this, $this->request);
                $this->matcheData = array_merge(self::$matcheDataInit, $data);

                $bind = $this->parseDefaultBind();
                if ($bind === false) {
                    $this->nodeNotRegistered();
                }
            }
        }

        echo 'hello world';
        exit();

        $this->completeRequest();

        $this->bind = $bind;
    }

    /**
     * 节点资源未注册异常
     *
     * @return void
     */
    protected function nodeNotRegistered()
    {
        $message = sprintf('The node %s is not registered.', $this->makeNode());

        throw new InvalidArgumentException($message);
    }

    /**
     * 生成节点资源
     *
     * @param string $controller
     * @param string $action
     * @return string
     */
    protected function makeNode($controller, $action)
    {
        return $this->app() . '\\' . $this->parseControllerDir() . '\\' . $controller . '->' . $action . '()';
    }

    /**
     * 取得控制器命名空间目录
     *
     * @return string
     */
    protected function parseControllerDir()
    {
        $result = $this->getControllerDir();

        if ($this->prefix()) {
            $result = $result . '\\' . $this->prefix();
        }

        return $result;
    }

    /**
     * 完成请求
     *
     * @return void
     */
    protected function completeRequest()
    {
        $this->pathinfoRestful();

        foreach ([
            'app',
            'controller',
            'action'
        ] as $type) {
            $this->request->{'set' . ucfirst($type)}($this->{$type}());
        }

        $this->request->params->replace($this->params());
    }

    /**
     * 智能 restful 解析
     * 路由匹配失败后尝试智能化解析
     *
     * @return void
     */
    protected function pathinfoRestful()
    {
        if (isset($this->matcheData[static::ACTION])) {
            return;
        }

        switch ($this->request->getMethod()) {
            case 'GET':
                if (! empty($this->matcheData[static::PARAMS])) {
                    $this->matcheData[static::ACTION] = static::RESTFUL_SHOW;
                }
                break;

            case 'POST':
                $this->matcheData[static::ACTION] = static::RESTFUL_STORE;
                break;

            case 'PUT':
                $this->matcheData[static::ACTION] = static::RESTFUL_UPDATE;
                break;

            case 'DELETE':
                $this->matcheData[static::ACTION] = static::RESTFUL_DESTROY;
                break;
        }
    }

    /**
     * 分析默认控制器
     *
     * @return false|callable
     */
    protected function parseDefaultBind()
    {
        $app = $this->app();
        $controller = $this->controller();
        $action = $this->action();

        // 尝试直接读取方法控制器类
        $controllerClass = $app . '\\' . $this->parseControllerDir() . '\\' . $controller . '\\' . $action;

        if (class_exists($controllerClass)) {
            $controller = $this->container->make($controllerClass);
            $method = method_exists($controller, 'handle') ? 'handle' : 'run';
        }

        // 尝试读取默认控制器
        else {  
            $controllerClass = $app . '\\' . $this->parseControllerDir() . '\\' . $controller;
            if (! class_exists($controllerClass)) {
                return false;
            }

            $controller = $this->container->make($controllerClass);
            $method = $action;
        }

        if ($controller instanceof IController) {
            $controller->setView($this->container['view']);
        }

        if (! method_exists($controller, $method)) {
            return false;
        }

        return [
            $controller,
            $method
        ];
    }

    /**
     * 取得打包节点
     *
     * @param string $app
     * @param string $controller
     * @param string $action
     * @return string
     */
    protected function packageNode($controller = null, $action = null, $app = null)
    {
        return ':' . ($app ? : $this->app()) . '/' . ($controller ?  : $this->controller()) . '/' . ($action ?  : $this->action());
    }

    /**
     * 分析节点
     *
     * @param string $app
     * @return arrat
     */
    protected function parseNode($strNode)
    {
        $controller = $action = $app = null;
        $arrTemp = $this->parseNodeUrl($strNode);

        if (! empty($arrTemp[static::APP]) && $arrTemp[static::APP] != '*') {
            $app = $arrTemp[static::APP];
        }
        if (! empty($arrTemp[static::CONTROLLER]) && $arrTemp[static::CONTROLLER] != '*') {
            $controller = $arrTemp[static::CONTROLLER];
        }
        if (! empty($arrTemp[static::ACTION]) && $arrTemp[static::ACTION] != '*') {
            $action = $arrTemp[static::ACTION];
        }

        unset($arrTemp);

        return [
            $controller ?  : $this->controller(),
            $action ?  : $this->action(),
            $app ?  : $this->app()
        ];
    }
}
