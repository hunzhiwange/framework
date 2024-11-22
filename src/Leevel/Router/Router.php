<?php

declare(strict_types=1);

namespace Leevel\Router;

use Leevel\Di\IContainer;
use Leevel\Http\Request;
use Leevel\Router\Matching\IMatching;
use Leevel\Support\Arr\ConvertJson;
use Leevel\Support\Arr\ShouldJson;
use Leevel\Support\Pipeline;
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
     * 路由匹配数据.
     */
    protected array $matchedData = [];

    /**
     * 基础路径.
     */
    protected array $basePaths = [];

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
    protected string $controllerDir = 'Controller';

    /**
     * 设置路由请求预解析结果.
     */
    protected array $preRequestMatched = [];

    /**
     * 应用是否带有默认应用命名空间.
     */
    protected bool $withDefaultAppNamespace = false;

    /**
     * 构造函数.
     */
    public function __construct(IContainer $container)
    {
        $this->container = $container;
        $this->container->addContextKeys('app_name');
    }

    public function setWithDefaultAppNamespace(bool $withDefaultAppNamespace): void
    {
        $this->withDefaultAppNamespace = $withDefaultAppNamespace;
    }

    public function dispatch(Request $request): Response
    {
        return $this->runRoute($request, $this->matchRouter($request));
    }

    public function setPreRequestMatched(Request $request, array $matchedData): void
    {
        $this->preRequestMatched[$this->getRequestObjectId($request)] = $matchedData;
    }

    public function throughTerminateMiddleware(Request $request, Response $response): void
    {
        $middlewares = $this->matchedMiddlewares($request)['terminate'] ?? [];
        $this->clearMatchedData($request);
        if (empty($middlewares)) {
            return;
        }

        new Pipeline($this->container)
            ->send([$request, $response])
            ->through($middlewares)
            ->then()
        ;
    }

    public function setControllerDir(string $controllerDir): void
    {
        $this->controllerDir = str_replace('/', '\\', $controllerDir);
    }

    public function getControllerDir(): string
    {
        return $this->controllerDir;
    }

    public function setRouters(array $routers): void
    {
        $this->routers = $routers;
    }

    public function getRouters(): array
    {
        return $this->routers;
    }

    public function setBasePaths(array $basePaths): void
    {
        $this->basePaths = $basePaths;
    }

    public function getBasePaths(): array
    {
        return $this->basePaths;
    }

    public function setGroups(array $groups): void
    {
        $this->groups = $groups;
    }

    public function getGroups(): array
    {
        return $this->groups;
    }

    public function setMiddlewareGroups(array $middlewareGroups): void
    {
        $this->middlewareGroups = $middlewareGroups;
    }

    public function getMiddlewareGroups(): array
    {
        return $this->middlewareGroups;
    }

    public function setMiddlewareAlias(array $middlewareAlias): void
    {
        $this->middlewareAlias = $middlewareAlias;
    }

    public function getMiddlewareAlias(): array
    {
        return $this->middlewareAlias;
    }

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

        return [
            'handle' => $handle,
            'terminate' => $terminate,
        ];
    }

    /**
     * 路由匹配.
     *
     * - 高效匹配，如果默认 PathInfo 路由能够匹配上则忽略注解路由匹配.
     */
    protected function matchRouter(Request $request): callable // @phpstan-ignore-line
    {
        $this->clearMatchedData($request);
        $basePathsMatchedData = $this->matchBasePaths($this->getPathInfo($request));

        // CORS 跨域支持
        // 请求头携带 token 等数据会进行 OPTIONS 请求
        if ($this->isOptionsRequest($request)) {
            $this->resolveMatchedData($request, $basePathsMatchedData, []);

            return static fn (): Response => new Response('CORS');
        }

        $this->resolveMatchedData(
            $request,
            $dataPathInfo = $this->normalizeMatchedData($request, 'PathInfo'),
            $basePathsMatchedData
        );

        if (false === ($bind = $this->normalizeRouterBind($request))) {
            $bind = $this->annotationRouterBind($request, $dataPathInfo, $basePathsMatchedData);
        }

        if (false === $bind) {
            $this->routerNotFound($request);
        }

        // @phpstan-ignore-next-line
        return $bind;
    }

    /**
     * 注解路由绑定.
     */
    protected function annotationRouterBind(Request $request, array $dataPathInfo, array $basePathsMatchedData): callable|false // @phpstan-ignore-line
    {
        $data = $this->normalizeMatchedData($request, 'Annotation');
        if (!$data) {
            $data = $dataPathInfo;
        } else {
            $this->clearMatchedData($request);
        }

        $this->resolveMatchedData($request, $data, $basePathsMatchedData);

        return $this->normalizeRouterBind($request);
    }

    /**
     * 完成路由匹配数据.
     */
    protected function resolveMatchedData(Request $request, array $data, array $basePathsMatchedData): void
    {
        if ($preRequestMatched = $this->preRequestMatched[$this->getRequestObjectId($request)] ?? []) {
            $data = $this->mergeMatchedData($data, $preRequestMatched);
        }

        if ($basePathsMatchedData) {
            $data = $this->mergeMatchedData($basePathsMatchedData, $data);
        }

        if (!isset($data[IRouter::APP])) {
            $data[IRouter::APP] = self::DEFAULT_APP;
        }

        $this->matchedData[$this->getRequestObjectId($request)] = $data;
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
            } elseif (\in_array($key, [self::ATTRIBUTES, self::VARS], true)) {
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
    protected function normalizeMatchedData(Request $request, string $matching): array
    {
        $matching = __NAMESPACE__.'\\Matching\\'.$matching;

        /** @var IMatching $matching */
        $matching = new $matching();

        return $matching->match($this, $request);
    }

    /**
     * 解析路由绑定.
     */
    protected function normalizeRouterBind(Request $request): callable|false // @phpstan-ignore-line
    {
        $this->completeRequest($request);

        return $this->parseMatchedBind($request);
    }

    /**
     * 运行路由.
     */
    protected function runRoute(Request $request, callable $bind): Response
    {
        return $this->throughMiddleware($request, function () use ($bind, $request): Response {
            $response = $this->container->call($bind, $this->matchedVars($request));
            if (!$response instanceof Response) {
                if (ShouldJson::handle($response)) {
                    $response = JsonResponse::fromJsonString(ConvertJson::handle($response, JSON_UNESCAPED_UNICODE));
                } else {
                    $response = new Response((string) $response);
                }
            }

            return $response;
        });
    }

    /**
     * 穿越中间件.
     */
    protected function throughMiddleware(Request $request, \Closure $then): Response
    {
        $middlewares = $this->matchedMiddlewares($request)['handle'] ?? [];
        if (empty($middlewares)) {
            return $then();
        }

        // @phpstan-ignore-next-line
        return new Pipeline($this->container)
            ->send([$request])
            ->through($middlewares)
            ->then($then)
        ;
    }

    /**
     * 取得 PathInfo.
     */
    protected function getPathInfo(Request $request): string
    {
        return rtrim($request->getPathInfo(), '/').'/';
    }

    /**
     * 匹配基础路径.
     */
    protected function matchBasePaths(string $pathInfo): array
    {
        $middlewaresKey = static::MIDDLEWARES;
        $result = [$middlewaresKey => []];
        foreach ($this->getBasePaths() as $path => $config) {
            if ('*' === $path || preg_match($path, $pathInfo, $matches)) {
                if (isset($config['middlewares'])) {
                    $result[$middlewaresKey] = $this->mergeMiddlewares($result[$middlewaresKey], $config['middlewares']);
                }
            }
        }

        return $result;
    }

    /**
     * 路由未找到异常.
     *
     * @throws RouterNotFoundException
     */
    protected function routerNotFound(Request $request): void
    {
        $message = sprintf('The router %s was not found.', $this->makeRouterNode($request));
        $this->clearMatchedData($request);

        throw new RouterNotFoundException($message);
    }

    /**
     * 生成路由节点资源.
     */
    protected function makeRouterNode(Request $request): string
    {
        if ($matchedBind = $this->matchedBind($request)) {
            return $matchedBind;
        }

        return $this->matchedApp($request).'\\'.
                $this->parseControllerDir($request).'\\'.
                $this->matchedController($request).'::'.
                $this->matchedAction($request).'()';
    }

    /**
     * 取得控制器命名空间目录.
     */
    protected function parseControllerDir(Request $request): string
    {
        $controllerDir = $this->getControllerDir();
        if ($prefix = $this->matchedPrefix($request)) {
            $controllerDir .= '\\'.$prefix;
        }

        return $controllerDir;
    }

    /**
     * 完成请求.
     */
    protected function completeRequest(Request $request): void
    {
        $this->pathinfoRestful($request);
        $this->container->instance('app_name', $this->matchedApp($request));
        $request->attributes->add($this->matchedAttributes($request));
    }

    /**
     * 智能 RESTFUL 解析.
     *
     * - 路由匹配如果没有匹配上方法则系统会进入 RESTFUL 解析.
     */
    protected function pathinfoRestful(Request $request): void
    {
        if (!empty($this->getMatchedData($request, static::ACTION))) {
            return;
        }

        switch ($request->getMethod()) {
            case 'POST':
                $this->setMatchedData($request, static::ACTION, static::RESTFUL_STORE);

                break;

            case 'PUT':
                $this->setMatchedData($request, static::ACTION, static::RESTFUL_UPDATE);

                break;

            case 'DELETE':
                $this->setMatchedData($request, static::ACTION, static::RESTFUL_DESTROY);

                break;

            case 'GET':
            default:
                $attributes = $this->matchedAttributes($request);
                if (isset($attributes[static::RESTFUL_ID])) {
                    $this->setMatchedData($request, static::ACTION, static::RESTFUL_SHOW);
                } else {
                    $this->setMatchedData($request, static::ACTION, static::RESTFUL_INDEX);
                }

                break;
        }
    }

    /**
     * 分析匹配路由绑定控制器.
     */
    protected function parseMatchedBind(Request $request): callable|false // @phpstan-ignore-line
    {
        if ($matchedBind = $this->matchedBind($request)) {
            return $this->normalizeControllerForBind($matchedBind);
        }

        return $this->normalizeControllerForDefault($request);
    }

    /**
     * 格式化基于注解路由的绑定控制器.
     */
    protected function normalizeControllerForBind(string $matchedBind): callable|false // @phpstan-ignore-line
    {
        if (str_contains($matchedBind, '@')) {
            [$bindClass, $method] = explode('@', $matchedBind);
        } else {
            $bindClass = $matchedBind;
            $method = 'handle';
        }

        if (!class_exists($bindClass)) {
            return false;
        }

        $controller = $this->container->make($bindClass);

        // @phpstan-ignore-next-line
        if (!method_exists($controller, $method)
            || !\is_callable([$controller, $method])) {
            return false;
        }

        // @phpstan-ignore-next-line
        return [$controller, $method];
    }

    /**
     * 格式化基于 pathInfo 的默认控制器.
     */
    protected function normalizeControllerForDefault(Request $request): callable|false // @phpstan-ignore-line
    {
        $matchedApp = $this->matchedApp($request);
        $matchedController = $this->matchedController($request);
        $matchedAction = $this->matchedAction($request);

        // 尝试直接读取方法控制器类
        $controllerClass = $matchedApp
            .'\\'.$this->parseControllerDir($request)
            .'\\'.$matchedController
            .'\\'.ucfirst($matchedAction);
        if (class_exists($controllerClass)) {
            $controller = $this->container->make($controllerClass);
            $method = 'handle';
        } else {
            // 尝试读取默认控制器
            $controllerClass = $matchedApp
                .'\\'.$this->parseControllerDir($request)
                .'\\'.$matchedController;
            if (!class_exists($controllerClass)) {
                return false;
            }
            $controller = $this->container->make($controllerClass);
            $method = $matchedAction;
        }

        // @phpstan-ignore-next-line
        if (!method_exists($controller, $method)
            || !\is_callable([$controller, $method])) {
            return false;
        }

        // @phpstan-ignore-next-line
        return [$controller, $method];
    }

    /**
     * 取回应用名.
     */
    protected function matchedApp(Request $request): string
    {
        $app = (string) $this->getMatchedData($request, static::APP, '');
        if (!$this->withDefaultAppNamespace || IRouter::DEFAULT_APP === $app) {
            return ucfirst($app);
        }

        return ucfirst(IRouter::DEFAULT_APP).'\\'.ucfirst($app);
    }

    /**
     * 取回控制器前缀.
     */
    protected function matchedPrefix(Request $request): ?string
    {
        $prefix = $this->getMatchedData($request, static::PREFIX, '');
        if (!$prefix || \is_scalar($prefix)) {
            // @phpstan-ignore-next-line
            return $prefix;
        }

        /** @phpstan-ignore-next-line */
        $prefix = array_map(function (string $item): string {
            return $this->convertMatched(ucfirst($item));
        }, (array) $prefix);

        $prefix = implode('\\', $prefix);
        $this->setMatchedData($request, static::PREFIX, $prefix);

        return $prefix;
    }

    /**
     * 取回控制器名.
     */
    protected function matchedController(Request $request): string
    {
        return $this->convertMatched(ucfirst((string) $this->getMatchedData($request, static::CONTROLLER, '')));
    }

    /**
     * 取回方法名.
     */
    protected function matchedAction(Request $request): string
    {
        return $this->convertMatched((string) $this->getMatchedData($request, static::ACTION, ''));
    }

    /**
     * 转换匹配资源.
     */
    protected function convertMatched(string $matched): string
    {
        if (str_contains($matched, '-')) {
            $matched = str_replace('-', '_', $matched);
        }

        if (str_contains($matched, '_')) {
            $matched = '_'.str_replace('_', ' ', $matched);
            $matched = ltrim(str_replace(' ', '', ucwords($matched)), '_');
        }

        return $matched;
    }

    /**
     * 取回绑定资源.
     */
    protected function matchedBind(Request $request): ?string
    {
        // @phpstan-ignore-next-line
        return $this->getMatchedData($request, static::BIND, null);
    }

    /**
     * 取回匹配参数.
     */
    protected function matchedAttributes(Request $request): array
    {
        return (array) $this->getMatchedData($request, static::ATTRIBUTES, []);
    }

    /**
     * 取回匹配中间件.
     */
    protected function matchedMiddlewares(Request $request): array
    {
        return (array) $this->getMatchedData($request, static::MIDDLEWARES, [
            'handle' => [],
            'terminate' => [],
        ]);
    }

    /**
     * 取回匹配变量.
     */
    protected function matchedVars(Request $request): array
    {
        return (array) $this->getMatchedData($request, static::VARS, []);
    }

    /**
     * 是否为 OPTIONS 请求.
     */
    protected function isOptionsRequest(Request $request): bool
    {
        return 'OPTIONS' === $request->getMethod();
    }

    protected function setMatchedData(Request $request, string $key, mixed $value): void
    {
        $this->matchedData[$this->getRequestObjectId($request)][$key] = $value;
    }

    protected function getMatchedData(Request $request, string $key, mixed $defaultValue = null): mixed
    {
        return $this->matchedData[$this->getRequestObjectId($request)][$key] ?? $defaultValue;
    }

    protected function clearMatchedData(Request $request): void
    {
        $requestObjectId = $this->getRequestObjectId($request);
        if (isset($this->matchedData[$requestObjectId])) {
            unset($this->matchedData[$requestObjectId]);
        }
    }

    protected function getRequestObjectId(Request $request): int
    {
        return spl_object_id($request);
    }
}
