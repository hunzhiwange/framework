<?php

declare(strict_types=1);

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

use Leevel\Di\IContainer;
use Leevel\Http\IRequest;
use Leevel\Http\IResponse;
use Leevel\Http\Response;
use Leevel\Mvc\IController;
use Leevel\Mvc\IView;
use Leevel\Pipeline\Pipeline;
use Leevel\Support\TMacro;

/**
 * 路由解析
 * 2018.04.10 开始进行一次重构系统路由架构.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.01.10
 *
 * @version 1.0
 */
class Router implements IRouter
{
    use TMacro;

    /**
     * IOC Container.
     *
     * @var \Leevel\Di\IContainer
     */
    protected $container;

    /**
     * http 请求
     *
     * @var \Leevel\Http\IRequest
     */
    protected $request;

    /**
     * 路由匹配数据.
     *
     * @var array
     */
    protected $matchedData;

    /**
     * 路由匹配初始化数据.
     *
     * @var array
     */
    protected static $matcheDataInit = [
        self::APP         => self::DEFAULT_APP,
        self::CONTROLLER  => null,
        self::ACTION      => null,
        self::PREFIX      => null,
        self::PARAMS      => null,
        self::MIDDLEWARES => null,
        self::VARS        => null,
    ];

    /**
     * 是否已经进行匹配.
     *
     * @var bool
     */
    protected $isMatched;

    /**
     * 基础路径.
     *
     * @var array
     */
    protected $basePaths = [];

    /**
     * 分组路径.
     *
     * @var array
     */
    protected $groupPaths = [];

    /**
     * 分组.
     *
     * @var array
     */
    protected $groups = [];

    /**
     * 路由.
     *
     * @var array
     */
    protected $routers = [];

    /**
     * 中间件分组.
     *
     * @var array
     */
    protected $middlewareGroups = [];

    /**
     * 中间件别名.
     *
     * @var array
     */
    protected $middlewareAlias = [];

    /**
     * 控制器相对目录.
     *
     * @var string
     */
    protected $controllerDir = 'App\\Controller';

    /**
     * 构造函数.
     *
     * @param \Leevel\Di\IContainer $container
     */
    public function __construct(IContainer $container)
    {
        $this->container = $container;
    }

    /**
     * 分发请求到路由.
     *
     * @param \Leevel\Http\IRequest $request
     *
     * @return \Leevel\Http\IResponse
     */
    public function dispatch(IRequest $request): IResponse
    {
        $this->request = $request;

        return $this->dispatchToRoute($request);
    }

    /**
     * 初始化请求
     */
    public function initRequest()
    {
        $this->matchedData = null;
    }

    /**
     * 设置匹配路由
     * 绕过路由解析，可以用于高性能 RPC 快速匹配资源.
     *
     * @param array $matchedData
     */
    public function setMatchedData(array $matchedData): void
    {
        $this->matchedData = array_merge(self::$matcheDataInit, $matchedData);

        $this->isMatched = true;
    }

    /**
     * 穿越中间件.
     *
     * @param \Leevel\Http\IRequest $passed
     * @param array                 $passedExtend
     */
    public function throughMiddleware(IRequest $passed, array $passedExtend = [])
    {
        $method = !$passedExtend ? 'handle' : 'terminate';
        $middlewares = $this->matchedMiddlewares();

        if (empty($middlewares[$method])) {
            return;
        }

        (new Pipeline($this->container))->

        send($passed)->

        send($passedExtend)->

        through($middlewares[$method])->

        then();
    }

    /**
     * 设置控制器相对目录.
     *
     * @param string $controllerDir
     */
    public function setControllerDir(string $controllerDir)
    {
        $controllerDir = str_replace('/', '\\', $controllerDir);

        $this->controllerDir = $controllerDir;
    }

    /**
     * 返回控制器相对目录.
     *
     * @return string
     */
    public function getControllerDir(): string
    {
        return $this->controllerDir;
    }

    /**
     * 设置路由.
     *
     * @param array $routers
     */
    public function setRouters(array $routers)
    {
        $this->routers = $routers;
    }

    /**
     * 取得当前路由.
     *
     * @return array
     */
    public function getRouters(): array
    {
        return $this->routers;
    }

    /**
     * 设置基础路径.
     *
     * @param array $basePaths
     */
    public function setBasePaths(array $basePaths)
    {
        $this->basePaths = $basePaths;
    }

    /**
     * 取得基础路径.
     *
     * @return array
     */
    public function getBasePaths(): array
    {
        return $this->basePaths;
    }

    /**
     * 设置分组路径.
     *
     * @param array $groupPaths
     */
    public function setGroupPaths(array $groupPaths)
    {
        $this->groupPaths = $groupPaths;
    }

    /**
     * 取得分组路径.
     *
     * @return array
     */
    public function getGroupPaths(): array
    {
        return $this->groupPaths;
    }

    /**
     * 设置路由分组.
     *
     * @param array $groups
     */
    public function setGroups(array $groups)
    {
        $this->groups = $groups;
    }

    /**
     * 取得路由分组.
     *
     * @return array
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * 设置中间件分组.
     *
     * @param array $middlewareGroups
     */
    public function setMiddlewareGroups(array $middlewareGroups)
    {
        $this->middlewareGroups = $middlewareGroups;
    }

    /**
     * 取得中间件分组.
     *
     * @return array
     */
    public function getMiddlewareGroups(): array
    {
        return $this->middlewareGroups;
    }

    /**
     * 设置中间件别名.
     *
     * @param array $middlewareAlias
     */
    public function setMiddlewareAlias(array $middlewareAlias)
    {
        $this->middlewareAlias = $middlewareAlias;
    }

    /**
     * 取得中间件别名.
     *
     * @return array
     */
    public function getMiddlewareAlias(): array
    {
        return $this->middlewareAlias;
    }

    /**
     * 匹配路径.
     *
     * @param string $path
     * @param bool   $ignoreMiddleware
     *
     * @return array
     */
    public function matchePath(string $path, bool $ignoreMiddleware = false): array
    {
        $result = [];

        if (false !== strpos($path, '?')) {
            list($path, $query) = explode('?', $path);

            if ($query) {
                foreach (explode('&', $query) as $item) {
                    $item = explode('=', $item);
                    $result[static::PARAMS][$item[0]] = $item[1];
                }
            }
        }

        $middlewares = [];

        if (false === $ignoreMiddleware) {
            // 匹配基础路径
            foreach ($this->getBasePaths() as $item => $option) {
                if ('*' === $item) {
                    if (isset($option['middlewares'])) {
                        $middlewares = $option['middlewares'];
                    }
                } elseif (preg_match($item, $path, $matches)) {
                    if (isset($option['middlewares'])) {
                        $middlewares = $this->mergeMiddlewares($middlewares, $option['middlewares']);
                    }

                    break;
                }
            }

            // 匹配分组路径
            foreach ($this->getGroupPaths() as $item => $option) {
                if (0 === strpos($path, $item)) {
                    $path = substr($path, strlen($item) + 1);

                    if (isset($option['middlewares'])) {
                        $middlewares = $this->mergeMiddlewares($middlewares, $option['middlewares']);
                    }

                    break;
                }
            }

            $result[IRouter::MIDDLEWARES] = $middlewares;
        }

        $path = trim($path, '/');
        $paths = $path ? explode('/', $path) : [];

        // 应用
        if ($paths && $this->findApp($paths[0])) {
            $result[static::APP] = substr(array_shift($paths), 1);
        }

        if (!$paths) {
            $result[IRouter::CONTROLLER] = IRouter::DEFAULT_HOME_CONTROLLER;

            return $result;
        }

        list($paths, $params) = $this->normalizePathsAndParams($paths);

        if (1 === count($paths)) {
            $result[static::CONTROLLER] = array_pop($paths);
        } else {
            if ($paths) {
                $result[static::ACTION] = array_pop($paths);
            }

            if ($paths) {
                $result[static::CONTROLLER] = array_pop($paths);
            }

            if ($paths) {
                $result[static::PREFIX] = implode('\\', array_map(function ($item) {
                    if (false !== strpos($item, '_')) {
                        $item = str_replace('_', ' ', $item);
                        $item = str_replace(' ', '', ucwords($item));
                    } else {
                        $item = ucfirst($item);
                    }

                    return $item;
                }, $paths));
            }
        }

        $result[static::PARAMS] = array_merge($result[static::PARAMS] ?? [], $params);

        return $result;
    }

    /**
     * 合并中间件.
     *
     * @param array $middlewares
     * @param array $newMiddlewares
     *
     * @return array
     */
    protected function mergeMiddlewares(array $middlewares, array $newMiddlewares): array
    {
        return [
            'handle'    => array_unique(array_merge(
                $middlewares['handle'] ?? [],
                $newMiddlewares['handle'] ?? []
            )),
            'terminate' => array_unique(array_merge(
                $middlewares['terminate'] ?? [],
                $newMiddlewares['terminate'] ?? []
            )),
        ];
    }

    /**
     * 是否找到 app.
     *
     * @param string $app
     *
     * @return bool
     */
    protected function findApp($app)
    {
        return 0 === strpos($app, ':');
    }

    /**
     * 解析路径和参数.
     *
     * @param array $data
     *
     * @return array
     */
    protected function normalizePathsAndParams(array $data): array
    {
        $paths = $params = [];

        $k = 0;

        foreach ($data as $item) {
            if (is_numeric($item)) {
                $params['_param'.$k] = $item;
                $k++;
            } else {
                $paths[] = $item;
            }
        }

        return [
            $paths,
            $params,
        ];
    }

    /**
     * 路由匹配
     * 高效匹配，如果默认 PathInfo 路由能够匹配上则忽略 OpenApi 路由匹配.
     *
     * @return mixed|void
     */
    protected function matchRouter()
    {
        if (true === $this->isMatched && null !== $this->matchedData) {
            return $this->findRouterBind();
        }

        $this->initRequest();

        $this->resolveMatchedData(
            $dataPathInfo = $this->normalizeMatchedData('PathInfo')
        );

        if (false === ($bind = $this->normalizeRouterBind())) {
            $bind = $this->urlRouterBind($dataPathInfo);
        }

        return $bind;
    }

    /**
     * URL 路由绑定.
     *
     * @param array $dataPathInfo
     *
     * @return mixed
     */
    protected function urlRouterBind(array $dataPathInfo)
    {
        $data = $this->normalizeMatchedData('Url');

        if (!$data) {
            $data = $dataPathInfo;
        } else {
            $this->initRequest();
        }

        $this->resolveMatchedData($data);

        return $this->findRouterBind();
    }

    /**
     * 完成路由匹配数据.
     *
     * @param array $data
     */
    protected function resolveMatchedData(array $data): void
    {
        $this->matchedData = array_merge(self::$matcheDataInit, $data);
    }

    /**
     * 解析路由匹配数据.
     *
     * @param string $matche
     *
     * @return array
     */
    protected function normalizeMatchedData(string $matche): array
    {
        $matche = 'Leevel\Router\Match\\'.$matche;

        return (new $matche())->matche($this, $this->request);
    }

    /**
     * 尝试获取路由绑定.
     *
     * @return callable|void
     */
    protected function findRouterBind()
    {
        if (false === ($bind = $this->normalizeRouterBind())) {
            $this->routerNotFound();
        }

        return $bind;
    }

    /**
     * 解析路由绑定.
     *
     * @return mixed
     */
    protected function normalizeRouterBind()
    {
        $this->completeRequest();

        return $this->parseDefaultBind();
    }

    /**
     * 发送路由并返回响应.
     *
     * @param \Leevel\Http\IRequest $request
     *
     * @return \Leevel\Http\IResponse
     */
    protected function dispatchToRoute(IRequest $request): IResponse
    {
        return $this->runRoute($request, $this->matchRouter());
    }

    /**
     * 运行路由.
     *
     * @param \Leevel\Http\IRequest $request
     * @param callable              $bind
     *
     * @return \Leevel\Http\IResponse
     */
    protected function runRoute(IRequest $request, callable $bind): IResponse
    {
        $this->throughMiddleware($this->request);

        $response = $this->container->call($bind, $this->matchedVars());

        if (!($response instanceof IResponse)) {
            $response = new Response($response);
        }

        return $response;
    }

    /**
     * 路由未找到异常.
     */
    protected function routerNotFound()
    {
        $message = sprintf('The router %s was not found.', $this->makeRouterNode());

        throw new RouterNotFoundException($message);
    }

    /**
     * 生成路由节点资源.
     *
     * @return string
     */
    protected function makeRouterNode()
    {
        return $this->matchedApp().'\\'.
            $this->parseControllerDir().'\\'.
            $this->matchedController().'::'.
            $this->matchedAction().'()';
    }

    /**
     * 取得控制器命名空间目录.
     *
     * @return string
     */
    protected function parseControllerDir()
    {
        $result = $this->getControllerDir();

        if ($this->matchedPrefix()) {
            $result = $result.'\\'.$this->matchedPrefix();
        }

        return $result;
    }

    /**
     * 完成请求
     */
    protected function completeRequest()
    {
        $this->pathinfoRestful();

        foreach ([
            'App',
            'Controller',
            'Action',
        ] as $type) {
            $this->request->{'set'.$type}($this->{'matched'.$type}());
        }

        $this->request->params->replace($this->matchedParams());
    }

    /**
     * 智能 restful 解析
     * 路由匹配如果没有匹配上方法则系统会进入 restful 解析.
     */
    protected function pathinfoRestful()
    {
        if (!empty($this->matchedData[static::ACTION])) {
            return;
        }

        switch ($this->request->getMethod()) {
            case 'POST':
                $this->matchedData[static::ACTION] = static::RESTFUL_STORE;

                break;
            case 'PUT':
                $this->matchedData[static::ACTION] = static::RESTFUL_UPDATE;

                break;
            case 'DELETE':
                $this->matchedData[static::ACTION] = static::RESTFUL_DESTROY;

                break;
            case 'GET':
            default:
                $this->matchedData[static::ACTION] = static::RESTFUL_SHOW;

                break;
        }
    }

    /**
     * 分析默认控制器.
     *
     * @return callable|false
     */
    protected function parseDefaultBind()
    {
        $app = $this->matchedApp();
        $controller = $this->matchedController();
        $action = $this->matchedAction();

        // 尝试直接读取方法控制器类
        $controllerClass = $app.'\\'.$this->parseControllerDir().'\\'.$controller.'\\'.ucfirst($action);

        if (class_exists($controllerClass)) {
            $controller = $this->container->make($controllerClass);
            $method = method_exists($controller, 'handle') ? 'handle' : 'run';
        }

        // 尝试读取默认控制器
        else {
            $controllerClass = $app.'\\'.$this->parseControllerDir().'\\'.$controller;
            if (!class_exists($controllerClass)) {
                return false;
            }

            $controller = $this->container->make($controllerClass);
            $method = $action;
        }

        if ($controller instanceof IController) {
            $controller->setView($this->container[IView::class]);
        }

        if (!method_exists($controller, $method)) {
            return false;
        }

        return [
            $controller,
            $method,
        ];
    }

    /**
     * 取回应用名.
     *
     * @return string
     */
    protected function matchedApp()
    {
        return ucfirst($this->matchedData[static::APP]);
    }

    /**
     * 取回控制器名.
     *
     * @return string
     */
    protected function matchedController()
    {
        return ucfirst($this->matchedData[static::CONTROLLER]);
    }

    /**
     * 取回方法名.
     *
     * @return string
     */
    protected function matchedAction()
    {
        $action = $this->matchedData[static::ACTION];

        if (false !== strpos($action, '-')) {
            $action = str_replace('-', '_', $action);
        }

        if (false !== strpos($action, '_')) {
            $action = '_'.str_replace('_', ' ', $action);
            $action = ltrim(str_replace(' ', '', ucwords($action)), '_');
        }

        return $action;
    }

    /**
     * 取回控制器前缀
     *
     * @return null|string
     */
    protected function matchedPrefix()
    {
        return $this->matchedData[static::PREFIX];
    }

    /**
     * 取回匹配参数.
     *
     * @return array
     */
    protected function matchedParams()
    {
        return $this->matchedData[static::PARAMS] ?? [];
    }

    /**
     * 取回匹配中间件.
     *
     * @return array
     */
    protected function matchedMiddlewares()
    {
        return $this->matchedData[static::MIDDLEWARES] ?? [
            'handle'    => [],
            'terminate' => [],
        ];
    }

    /**
     * 取回匹配变量.
     *
     * @return array
     */
    protected function matchedVars()
    {
        return $this->matchedData[static::VARS] ?? [];
    }
}
