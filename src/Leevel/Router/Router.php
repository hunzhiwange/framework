<?php

declare(strict_types=1);

namespace Leevel\Router;

use Leevel\Di\IContainer;
use Leevel\Http\Request;
use Leevel\Pipeline\Pipeline;
use Leevel\Support\Arr\convert_json;
use function Leevel\Support\Arr\convert_json;
use Leevel\Support\Arr\should_json;
use function Leevel\Support\Arr\should_json;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * 路由.
 */
class Router implements IRouter
{
    /**
     * IOC Container.
     */
    protected IContainer $container;

    /**
     * HTTP 请求.
     */
    protected Request $request;

    /**
     * 路由匹配数据.
     */
    protected array $matchedData = [];

    /**
     * 基础路径.
     */
    protected array $basePaths = [];

    /**
     * 分组路径.
     */
    protected array $groupPaths = [];

    /**
     * 分组.
     */
    protected array $groups = [];

    /**
     * 路由.
     */
    protected array $routers = [];

    /**
     * 中间件分组.
     */
    protected array $middlewareGroups = [];

    /**
     * 中间件别名.
     */
    protected array $middlewareAlias = [];

    /**
     * 控制器相对目录.
     * 
     * - 反斜杠分隔多层目录
     * - 命名空间风格
     */
    protected string $controllerDir = 'App\\Controller';

    /**
     * 设置路由请求预解析结果.
     */
    protected array $preRequestMatched = [];

    /**
     * 构造函数.
     */
    public function __construct(IContainer $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function dispatch(Request $request): Response
    {
        $this->request = $request;
        $this->setOptionsPathInfo($request);

        return $this->dispatchToRoute($request);
    }

    /**
     * {@inheritDoc}
     */
    public function initRequest(): void
    {
        $this->matchedData = [];
    }

    /**
     * {@inheritDoc}
     */
    public function setPreRequestMatched(Request $request, array $matchedData): void
    {
        $this->preRequestMatched[spl_object_id($request)] = $matchedData;
    }

    /**
     * {@inheritDoc}
     */
    public function throughMiddleware(Request $passed, array $passedExtend = []): void
    {
        $method = !$passedExtend ? 'handle' : 'terminate';
        $middlewares = $this->matchedMiddlewares();
        if (empty($middlewares[$method])) {
            return;
        }

        (new Pipeline($this->container))
            ->send([$passed])
            ->send($passedExtend)
            ->through($middlewares[$method])
            ->then();
    }

    /**
     * {@inheritDoc}
     */
    public function setControllerDir(string $controllerDir): void
    {
        $controllerDir = str_replace('/', '\\', $controllerDir);
        $this->controllerDir = $controllerDir;
    }

    /**
     * {@inheritDoc}
     */
    public function getControllerDir(): string
    {
        return $this->controllerDir;
    }

    /**
     * {@inheritDoc}
     */
    public function setRouters(array $routers): void
    {
        $this->routers = $routers;
    }

    /**
     * {@inheritDoc}
     */
    public function getRouters(): array
    {
        return $this->routers;
    }

    /**
     * {@inheritDoc}
     */
    public function setBasePaths(array $basePaths): void
    {
        $this->basePaths = $basePaths;
    }

    /**
     * {@inheritDoc}
     */
    public function getBasePaths(): array
    {
        return $this->basePaths;
    }

    /**
     * {@inheritDoc}
     */
    public function setGroups(array $groups): void
    {
        $this->groups = $groups;
    }

    /**
     * {@inheritDoc}
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * {@inheritDoc}
     */
    public function setMiddlewareGroups(array $middlewareGroups): void
    {
        $this->middlewareGroups = $middlewareGroups;
    }

    /**
     * {@inheritDoc}
     */
    public function getMiddlewareGroups(): array
    {
        return $this->middlewareGroups;
    }

    /**
     * {@inheritDoc}
     */
    public function setMiddlewareAlias(array $middlewareAlias): void
    {
        $this->middlewareAlias = $middlewareAlias;
    }

    /**
     * {@inheritDoc}
     */
    public function getMiddlewareAlias(): array
    {
        return $this->middlewareAlias;
    }

    /**
     * {@inheritDoc}
     */
    public function mergeMiddlewares(array $middlewares, array $newMiddlewares): array
    {
        $handle = array_unique(array_merge(
            $middlewares['handle'] ?? [],
            $newMiddlewares['handle'] ?? []
        ));

        $terminate = array_unique(array_merge(
            $middlewares['terminate'] ?? [],
            $newMiddlewares['terminate'] ?? []
        ));

        return ['handle' => $handle, 'terminate' => $terminate];
    }

    /**
     * 路由匹配.
     *
     * - 高效匹配，如果默认 PathInfo 路由能够匹配上则忽略注解路由匹配.
     */
    protected function matchRouter(): callable
    {
        $this->initRequest();
        $this->resolveMatchedData($dataPathInfo = $this->normalizeMatchedData('PathInfo'));

        if (false === ($bind = $this->normalizeRouterBind())) {
            $bind = $this->annotationRouterBind($dataPathInfo);
        }

        if (false === $bind) {
            $this->routerNotFound();
        }

        return $bind;
    }

    /**
     * 注解路由绑定.
     */
    protected function annotationRouterBind(array $dataPathInfo): callable|false
    {
        $data = $this->normalizeMatchedData('Annotation');
        if (!$data) {
            $data = $dataPathInfo;
        } else {
            $this->initRequest();
        }

        $this->resolveMatchedData($data);

        return $this->normalizeRouterBind();
    }

    /**
     * 完成路由匹配数据.
     */
    protected function resolveMatchedData(array $data): void
    {
        $data = $this->mergeMatchedData(
            $data,
            $this->preRequestMatched[spl_object_id($this->request)] ?? [],
        );

        if (!$data[IRouter::APP]) {
            $data[IRouter::APP] = self::DEFAULT_APP;
        }

        $this->matchedData = $data;
    }

    /**
     * 合并匹配数据.
     */
    protected function mergeMatchedData(array $before, array $after): array
    {
        $result = [];
        foreach (self::MATCHED as $key) {
            if (self::MIDDLEWARES === $key) {
                $result[$key] = $this->mergeMiddlewares($before[$key] ?? [], $after[$key] ?? []);
            } elseif (in_array($key, [self::ATTRIBUTES, self::VARS], true)) {
                $result[$key] = array_merge($before[$key] ?? [], $after[$key] ?? []);
            } else {
                $result[$key] = $after[$key] ?? ($before[$key] ?? null);
            }
        }

        return $result;
    }

    /**
     * 解析路由匹配数据.
     */
    protected function normalizeMatchedData(string $matching): array
    {
        $matching = __NAMESPACE__.'\\Matching\\'.$matching;
        /** @var \Leevel\Router\Matching\IMatching $matching */
        $matching = new $matching();

        return $matching->match($this, $this->request);
    }

    /**
     * 解析路由绑定.
     */
    protected function normalizeRouterBind(): mixed
    {
        $this->completeRequest();

        return $this->parseMatchedBind();
    }

    /**
     * 发送路由并返回响应.
     */
    protected function dispatchToRoute(Request $request): Response
    {
        return $this->runRoute($request, $this->matchRouter());
    }

    /**
     * 运行路由.
     */
    protected function runRoute(Request $request, callable $bind): Response
    {
        $this->throughMiddleware($request);

        $response = $this->container->call($bind, $this->matchedVars());
        if (!($response instanceof Response)) {
            if (should_json($response)) {
                $response = JsonResponse::fromJsonString(convert_json($response, JSON_UNESCAPED_UNICODE));
            } else {
                $response = new Response((string) $response);
            }
        }

        return $response;
    }

    /**
     * 路由未找到异常.
     *
     * @throws \Leevel\Router\RouterNotFoundException
     */
    protected function routerNotFound(): void
    {
        $message = sprintf('The router %s was not found.', $this->makeRouterNode());

        throw new RouterNotFoundException($message);
    }

    /**
     * 生成路由节点资源.
     */
    protected function makeRouterNode(): string
    {
        if ($matchedBind = $this->matchedBind()) {
            return $matchedBind;
        }
        
        return $this->matchedApp().'\\'.
                $this->parseControllerDir().'\\'.
                $this->matchedController().'::'.
                $this->matchedAction().'()';
    }

    /**
     * 取得控制器命名空间目录.
     */
    protected function parseControllerDir(): string
    {
        $result = $this->getControllerDir();
        if ($this->matchedPrefix()) {
            $result .= '\\'.$this->matchedPrefix();
        }

        return $result;
    }

    /**
     * 完成请求.
     */
    protected function completeRequest(): void
    {
        $this->pathinfoRestful();
        $this->container->instance('app_name', $this->matchedApp(), IContainer::DEFAULT_COROUTINE_ID);
        $this->request->attributes->add($this->matchedAttributes());
    }

    /**
     * 智能 RESTFUL 解析.
     *
     * - 路由匹配如果没有匹配上方法则系统会进入 RESTFUL 解析.
     */
    protected function pathinfoRestful(): void
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
                $attributes = $this->matchedAttributes();
                if (isset($attributes[static::RESTFUL_ID])) {
                    $this->matchedData[static::ACTION] = static::RESTFUL_SHOW;
                } else {
                    $this->matchedData[static::ACTION] = static::RESTFUL_INDEX;
                }

                break;
        }
    }

    /**
     * 分析匹配路由绑定控制器.
     */
    protected function parseMatchedBind(): callable|false
    {
        if ($matchedBind = $this->matchedBind()) {
            return $this->normalizeControllerForBind($matchedBind);
        }

        return $this->normalizeControllerForDefault();
    }

    /**
     * 格式化基于注解路由的绑定控制器.
     */
    protected function normalizeControllerForBind(string $matchedBind): callable|false
    {
        if (false !== strpos($matchedBind, '@')) {
            list($bindClass, $method) = explode('@', $matchedBind);
        } else {
            $bindClass = $matchedBind;
            $method = 'handle'; 
        }

        if (!class_exists($bindClass)) {
            return false;
        }
        $controller = $this->container->make($bindClass);
            
        if (!method_exists($controller, $method) ||
            !is_callable([$controller, $method])) {
            return false;
        }

        return [$controller, $method];
    }

    /**
     * 格式化基于 pathInfo 的默认控制器.
     */
    protected function normalizeControllerForDefault(): callable|false
    {
        $matchedApp = $this->matchedApp();
        $matchedController = $this->matchedController();
        $matchedAction = $this->matchedAction();

        // 尝试直接读取方法控制器类
        $controllerClass = $matchedApp.'\\'.$this->parseControllerDir().'\\'.
            $matchedController.'\\'.ucfirst($matchedAction);
        $controllerClass = $this->normalizeForSubdir($controllerClass);
        if (class_exists($controllerClass)) {
            $controller = $this->container->make($controllerClass);
            $method = 'handle';
        }

        // 尝试读取默认控制器
        else {
            $controllerClass = $matchedApp.'\\'.$this->parseControllerDir().'\\'.$matchedController;
            $controllerClass = $this->normalizeForSubdir($controllerClass);
            if (!class_exists($controllerClass)) {
                return false;
            }
            $controller = $this->container->make($controllerClass);
            $method = $this->normalizeForSubdir($matchedAction, true);
        }

        if (!method_exists($controller, $method)) {
            return false;
        }

        return [$controller, $method];
    }

    /**
     * 控制器和方法子目录支持.
     */
    protected function normalizeForSubdir(string $className, bool $forAction = false): string
    {
        if (false === strpos($className, ':')) {
            return $className;
        }

        $className = (string) preg_replace_callback(
            '/:([a-zA-Z])/',
            function (array $matches) use ($forAction): string {
                return false === $forAction ? '\\'.ucfirst($matches[1]) : ucfirst($matches[1]);
            },
            $className
        );

        return str_replace('\\\\', '\\', $className);
    }

    /**
     * 取回应用名.
     */
    protected function matchedApp(): string
    {
        return ucfirst($this->matchedData[static::APP]);
    }

    /**
     * 取回控制器前缀.
     */
    protected function matchedPrefix(): ?string
    {
        $prefix = $this->matchedData[static::PREFIX];
        if (!$prefix || is_scalar($prefix)) {
            return $prefix;
        }

        $prefix = array_map(function ($item) {
            return $this->convertMatched(ucfirst($item));
        }, $prefix);

        return $this->matchedData[static::PREFIX] = implode('\\', $prefix);
    }

    /**
     * 取回控制器名.
     */
    protected function matchedController(): string
    {
        return $this->convertMatched(ucfirst($this->matchedData[static::CONTROLLER]));
    }

    /**
     * 取回方法名.
     */
    protected function matchedAction(): string
    {
        return $this->convertMatched($this->matchedData[static::ACTION]);
    }

    /**
     * 转换匹配资源.
     */
    protected function convertMatched(string $matched): string
    {
        if (false !== strpos($matched, '-')) {
            $matched = str_replace('-', '_', $matched);
        }

        if (false !== strpos($matched, '_')) {
            $matched = '_'.str_replace('_', ' ', $matched);
            $matched = ltrim(str_replace(' ', '', ucwords($matched)), '_');
        }

        return $matched;
    }

    /**
     * 取回绑定资源.
     */
    protected function matchedBind(): ?string
    {
        return $this->matchedData[static::BIND];
    }

    /**
     * 取回匹配参数.
     */
    protected function matchedAttributes(): array
    {
        return $this->matchedData[static::ATTRIBUTES] ?? [];
    }

    /**
     * 取回匹配中间件.
     */
    protected function matchedMiddlewares(): array
    {
        return $this->matchedData[static::MIDDLEWARES] ?? [
            'handle'    => [],
            'terminate' => [],
        ];
    }

    /**
     * 取回匹配变量.
     */
    protected function matchedVars(): array
    {
        return $this->matchedData[static::VARS] ?? [];
    }

    /**
     * 设置 OPTIONS PathInfo.
     */
    protected function setOptionsPathInfo(Request $request): void
    {
        if ($this->isOptionsRequest()) {
            $app = $this->findApp($this->request->getPathInfo());
            $optionsPathInfo = '/'.$app.self::DEFAULT_OPTIONS.'/'.self::RESTFUL_INDEX;
            $request->setPathInfo($optionsPathInfo);
        }
    }

    /**
     * 查找 app.
     */
    protected function findApp(string $path): string
    {
        $paths = explode('/', trim($path, '/'));
        if ($paths && 0 === strpos($paths[0], ':')) {
            return $paths[0].'/';
        }

        return '';
    }

    /**
     * 是否为 OPTIONS 请求.
     */
    protected function isOptionsRequest(): bool
    {
        return 'OPTIONS' === $this->request->getMethod();
    }
}

// import fn.
class_exists(convert_json::class);
class_exists(should_json::class);
