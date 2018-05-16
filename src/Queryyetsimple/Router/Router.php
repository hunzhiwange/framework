<?php declare(strict_types=1);
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
    Http\IResponse,
    Support\TMacro,
    Mvc\IController,
    Pipeline\Pipeline
};
use Leevel\Router\Match\{
    Cli as CliMatch,
    Url as UrlMatch,
    PathInfo as PathInfoMatch
};

/**
 * 路由解析
 * 2018.04.10 开始进行一次重构系统路由架构
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.01.10
 * @version 1.0
 */
class Router implements IRouter
{
    use TMacro;

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
     * 全局路由绑定中间件
     *
     * @var array
     */
    protected $globalMiddlewares = [];

    /**
     * 当前的中间件
     *
     * @var array
     */
    protected $currentMiddlewares;

    /**
     * 路由匹配数据
     * 
     * @var array
     */
    protected $matchedData;

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
        self::VARS => null
    ];

    /**
     * 基础路径 
     *
     * @var array
     */
    protected $basepaths = [];

    /**
     * 分组
     *
     * @var array
     */
    protected $groups = [];

    /**
     * 路由 
     *
     * @var array
     */
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
     * 匹配应用名字
     *
     * @var string
     */
    protected $matchedApp;

    /**
     * 匹配控制器名字
     *
     * @var string
     */
    protected $matchedController;

    /**
     * 匹配方法名字
     *
     * @var string
     */
    protected $matchedAction;

    /**
     * 构造函数
     *
     * @param \Leevel\Di\IContainer $container
     * @return void
     */
    public function __construct(IContainer $container)
    {
        $this->container = $container;
    }

    /**
     * 分发请求到路由
     *
     * @param \Leevel\Http\Request $request
     * @return \Leevel\Http\IResponse
     */
    public function dispatch(Request $request)
    {
        $this->request = $request;

        return $this->dispatchToRoute($request);
    }

    /**
     * 初始化请求
     *
     * @return void
     */
    public function initRequest()
    {
        $this->matchedApp = null;
        $this->matchedController = null;
        $this->matchedAction = null;
        $this->matchedData = null;
    }

    /**
     * 设置匹配路由
     * 绕过路由解析，可以用于高性能 RPC 快速匹配资源
     *
     * @param array $matchedData
     * @return void
     */
    public function setMatchedData(array $matchedData): void
    {
        $this->matchedData = array_merge(self::$matcheDataInit, $matchedData);
    }

    /**
     * 穿越中间件
     *
     * @param \Leevel\Http\Request $passed
     * @param array $passedExtend
     * @return void
     */
    public function throughMiddleware(Request $passed, array $passedExtend = [])
    {
        if (is_null($this->currentMiddlewares)) {
            $this->currentMiddlewares = $this->parseMiddleware();
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
     * 设置控制器相对目录
     *
     * @param string $controllerDir
     * @return void
     */
    public function setControllerDir(string $controllerDir)
    {
        $controllerDir = str_replace('/', '\\', $controllerDir);
        $this->controllerDir = $controllerDir;
    }

    /**
     * 返回控制器相对目录
     *
     * @param string $controllerDir
     * @return void
     */
    public function getControllerDir()
    {
        return $this->controllerDir;
    }

    /**
     * 设置路由
     *
     * @param array $routers
     * @return void
     */
    public function setRouters(array $routers)
    {
        $this->routers = $routers;
    }

    /**
     * 取得当前路由
     *
     * @return array
     */
    public function getRouters()
    {
        return $this->routers;
    }

    /**
     * 设置基础路径
     *
     * @param array $basepaths
     * @return void
     */
    public function setBasepaths(array $basepaths)
    {
        $this->basepaths = $basepaths;
    }

    /**
     * 添加基础路径
     *
     * @param array $basepaths
     * @return void
     */
    public function addBasepaths(array $basepaths)
    {
        $this->basepaths = array_unique(array_merge($this->basepaths, $basepaths));
    }

    /**
     * 取得基础路径
     *
     * @return array
     */
    public function getBasepaths()
    {
        return $this->basepaths;
    }

    /**
     * 设置路由分组
     *
     * @param array $groups
     * @return void
     */
    public function setGroups(array $groups)
    {
        $this->groups = $groups;
    }

    /**
     * 添加路由分组
     *
     * @param array $groups
     * @return void
     */
    public function addGroups(array $groups)
    {
        $this->groups = array_unique(array_merge($this->groups, $groups));
    }

    /**
     * 取得路由分组
     *
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * 设置中间件分组
     *
     * @param array $middlewareGroups
     * @return void
     */
    public function setMiddlewareGroups(array $middlewareGroups)
    {
        $this->middlewareGroups = $middlewareGroups;
    }
    
    /**
     * 取得中间件分组
     *
     * @return array
     */
    public function getMiddlewareGroups()
    {
        return $this->middlewareGroups;
    }

    /**
     * 设置全局中间件
     *
     * @param array $middlewares
     * @return void
     */
    public function setGlobalMiddlewares(array $middlewares)
    {
        $this->globalMiddlewares = $middlewares;
    }

    /**
     * 取得全局中间件
     *
     * @return array
     */
    public function getGlobalMiddlewares()
    {
        return $this->globalMiddlewares;
    }
    
    /**
     * 设置中间件别名
     *
     * @param array $middlewareAlias
     * @return void
     */
    public function setMiddlewareAlias(array $middlewareAlias)
    {
        $this->middlewareAlias = $middlewareAlias;
    }

    /**
     * 取得中间件别名
     *
     * @return array
     */
    public function getMiddlewareAlias()
    {
        return $this->middlewareAlias;
    }

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
                    $arrData[Router::PARAMS][$strQuery[0]] = $strQuery[1];
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
     * 路由匹配
     * 高效匹配，如果默认 pathInfo 路由能够匹配上则忽略 swagger 路由匹配
     *
     * @return mixed
     */
    protected function matchRouter()
    {
        if (! is_null($this->matchedData)) {
            $this->completeRequest();

            $bind = $this->parseDefaultBind();
            if ($bind === false) {
                $this->nodeNotFound();
            }

            return $bind;
        }

        $this->initRequest();

        if ($this->request->isCli()) {
            $data = (new CliMatch)->matche($this, $this->request);
            $this->matchedData = array_merge(self::$matcheDataInit, $data);

            $this->completeRequest();

            $bind = $this->parseDefaultBind();
            if ($bind === false) {
                $this->nodeNotFound();
            }
        } else {
            // 默认 pathInfo 匹配
            $dataPathInfo = (new PathInfoMatch)->matche($this, $this->request);
            $this->matchedData = array_merge(self::$matcheDataInit, $dataPathInfo);

            $this->completeRequest();

            $bind = $this->parseDefaultBind();

            if ($bind === false) {
                $data = (new UrlMatch)->matche($this, $this->request);
                if (! $data) {
                    $data = $dataPathInfo;
                } else {
                    $this->initRequest();
                }
                $this->matchedData = array_merge(self::$matcheDataInit, $data);

                $this->completeRequest();

                $bind = $this->parseDefaultBind();
                if ($bind === false) {
                    $this->nodeNotFound();
                }
            }
        }

        return $bind;
    }

    /**
     * 发送路由并返回响应
     *
     * @param \Leevel\Http\Request $request
     * @return \Leevel\Http\IResponse
     */
    protected function dispatchToRoute(Request $request)
    {
        return $this->runRoute($request, $this->matchRouter());
    }

    /**
     * 运行路由
     * 
     * @param \Leevel\Http\Request $request
     * @param callable $bind
     * @return \Leevel\Http\IResponse
     */
    protected function runRoute(Request $request, callable $bind)
    {
        $this->throughMiddleware($this->request);

        $response = $this->container->call($bind, $this->matchedVars());

        if (! ($response instanceof IResponse)) {
            $response = new Response($response);
        }

        return $response;
    }

    /**
     * 节点资源未注册异常
     *
     * @return void
     */
    protected function nodeNotFound()
    {
        $message = sprintf('The node %s is not found.', $this->makeNode());

        throw new InvalidArgumentException($message);
    }

    /**
     * 生成节点资源
     *
     * @return string
     */
    protected function makeNode()
    {
        return $this->matchedApp() . '\\' . 
            $this->parseControllerDir() . '\\' . 
            $this->matchedController() . '->' . 
            $this->matchedAction() . '()';
    }

    /**
     * 取得控制器命名空间目录
     *
     * @return string
     */
    protected function parseControllerDir()
    {
        $result = $this->getControllerDir();

        if ($this->matchedPrefix()) {
            $result = $result . '\\' . $this->matchedPrefix();
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
            'App',
            'Controller',
            'Action'
        ] as $type) {
            $this->request->{'set' . $type}($this->{'matched' . $type}());
        }

        $this->request->params->replace($this->matchedParams());
    }

    /**
     * 智能 restful 解析
     * 路由匹配如果没有匹配上方法器则系统会进入 restful 解析
     *
     * @return void
     */
    protected function pathinfoRestful()
    {
        if (isset($this->matchedData[static::ACTION])) {
            return;
        }

        switch ($this->request->getMethod()) {
            case 'GET':
                if (! empty($this->matchedData[static::PARAMS])) {
                    $this->matchedData[static::ACTION] = static::RESTFUL_SHOW;
                }
                break;

            case 'POST':
                $this->matchedData[static::ACTION] = static::RESTFUL_STORE;
                break;

            case 'PUT':
                $this->matchedData[static::ACTION] = static::RESTFUL_UPDATE;
                break;

            case 'DELETE':
                $this->matchedData[static::ACTION] = static::RESTFUL_DESTROY;
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
        $app = $this->matchedApp();
        $controller = $this->matchedController();
        $action = $this->matchedAction();

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
     * 获取绑定的中间件
     * 暂时不做重复过滤，允许中间件多次执行
     *
     * @return array
     */
    protected function parseMiddleware()
    {
        return [
            'handle' => array_merge($this->globalMiddlewares['handle'], $this->matchedMiddlewares()['handle']),
            'terminate' => array_merge($this->globalMiddlewares['terminate'], $this->matchedMiddlewares()['terminate'])
        ];
    }

    /**
     * 取回应用名
     *
     * @return string
     */
    protected function matchedApp()
    {
        if ($this->matchedApp) {
            $app = $this->matchedApp;
        } else {
            if (($this->matchedApp = env('app_name'))) {
                $app = $this->matchedApp;
            } else {
                $app = $this->matchedApp = $this->matchedData[static::APP];
            }
        }

        return ucfirst($app);
    }

    /**
     * 取回控制器名
     *
     * @return string
     */
    protected function matchedController()
    {
        if ($this->matchedController) {
            $controller = $this->matchedController;
        } else {
            if (($this->matchedController = env('controller_name'))) {
                $controller = $this->matchedController;
            } else {
                $controller = $this->matchedData[static::CONTROLLER];
            }
        }

        return ucfirst($controller);
    }

    /**
     * 取回方法名
     *
     * @return string
     */
    protected function matchedAction()
    {
        if ($this->matchedAction) {
            $action = $this->matchedAction;
        } else {
            if (($this->matchedAction = env('action_name'))) {
                $action = $this->matchedAction;
            } else {
                $action = $this->matchedData[static::ACTION];
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
     * @return string|null
     */
    protected function matchedPrefix()
    {
        return $this->matchedData[static::PREFIX];
    }

    /**
     * 取回匹配参数
     *
     * @return array
     */
    protected function matchedParams()
    {
        return $this->matchedData[static::PARAMS] ?? [];
    }

    /**
     * 取回匹配中间件
     *
     * @return array
     */
    protected function matchedMiddlewares()
    {
        return $this->matchedData[static::MIDDLEWARES] ?? [
            'handle' => [],
            'terminate' => []
        ];
    }

    /**
     * 取回匹配变量
     *
     * @return array
     */
    protected function matchedVars()
    {
        return $this->matchedData[static::VARS] ?? [];
    }
}
