<?php

declare(strict_types=1);

namespace Leevel\Kernel;

use Composer\Autoload\ClassLoader;
use Leevel\Di\IContainer;
use Leevel\Event\Provider\Register as EventProvider;
use Leevel\Log\Provider\Register as LogProvider;
use Leevel\Router\Provider\Register as RouterProvider;
use RuntimeException;

/**
 * 应用.
 */
class App implements IApp
{
    /**
     * IOC 容器.
     */
    protected IContainer $container;

    /**
     * 应用基础路径.
    */
    protected string $path;

    /**
     * 应用路径.
    */
    protected string $appPath;

    /**
     * 公共路径.
    */
    protected string $commonPath;

    /**
     * 运行时路径.
    */
    protected string $runtimePath;

    /**
     * 存储路径.
    */
    protected string $storagePath;

    /**
     * 资源路径.
    */
    protected string $publicPath;

    /**
     * 主题路径.
    */
    protected string $themesPath;

    /**
     * 配置路径.
    */
    protected string $optionPath;

    /**
     * 语言包路径.
    */
    protected string $i18nPath;

    /**
     * 环境变量路径.
    */
    protected ?string $envPath = null;

    /**
     * 环境变量文件.
    */
    protected ?string $envFile = null;

    /**
     * 语言包缓存路径.
    */
    protected ?string $i18nCachedPath = null;

    /**
     * 配置缓存路径.
    */
    protected ?string $optionCachedPath = null;

    /**
     * 路由缓存路径.
    */
    protected ?string $routerCachedPath = null;

    /**
     * 构造函数.
     */
    public function __construct(IContainer $container, string $path)
    {
        $this->container = $container;
        $this->setPath($path);
        $this->registerBaseProvider();
    }

    /**
     * {@inheritDoc}
     */
    public function version(): string
    {
        return static::VERSION;
    }

    /**
     * {@inheritDoc}
     */
    public function isConsole(): bool
    {
        if (!is_object($this->container->make('request'))) {
            return \PHP_SAPI === 'cli';
        }

        /** @var \Leevel\Http\Request $request */
        $request = $this->container->make('request');

        return $request->isConsole();
    }

    /**
     * {@inheritDoc}
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * {@inheritDoc}
     */
    public function path(string $path = ''): string
    {
        return $this->path.$this->normalizePath($path);
    }

    /**
     * {@inheritDoc}
     */
    public function setAppPath(string $path): void
    {
        $this->appPath = $path;
    }

    /**
     * {@inheritDoc}
     */
    public function appPath(bool|string $app = false, string $path = ''): string
    {
        return ($this->appPath ?? $this->path.\DIRECTORY_SEPARATOR.'application').
            ($app ? \DIRECTORY_SEPARATOR.$this->normalizeApp($app) : $app).
            $this->normalizePath($path);
    }

    /**
     * {@inheritDoc}
     */
    public function themePath(bool|string $app = false): string
    {
        return $this->appPath($app).'/ui/theme';
    }

    /**
     * {@inheritDoc}
     */
    public function setCommonPath(string $path): void
    {
        $this->commonPath = $path;
    }

    /**
     * {@inheritDoc}
     */
    public function commonPath(string $path = ''): string
    {
        return ($this->commonPath ?? $this->path.\DIRECTORY_SEPARATOR.'common').
            $this->normalizePath($path);
    }

    /**
     * {@inheritDoc}
     */
    public function setRuntimePath(string $path): void
    {
        $this->runtimePath = $path;
    }

    /**
     * {@inheritDoc}
     */
    public function runtimePath(string $path = ''): string
    {
        return ($this->runtimePath ?? $this->path.\DIRECTORY_SEPARATOR.'runtime').
            $this->normalizePath($path);
    }

    /**
     * {@inheritDoc}
     */
    public function setStoragePath(string $path): void
    {
        $this->storagePath = $path;
    }

    /**
     * {@inheritDoc}
     */
    public function storagePath(string $path = ''): string
    {
        return ($this->storagePath ?? $this->path.\DIRECTORY_SEPARATOR.'storage').
            $this->normalizePath($path);
    }

    /**
     * {@inheritDoc}
     */
    public function setPublicPath(string $path): void
    {
        $this->publicPath = $path;
    }

    /**
     * {@inheritDoc}
     */
    public function publicPath(string $path = ''): string
    {
        return ($this->publicPath ?? $this->path.\DIRECTORY_SEPARATOR.'public').
            $this->normalizePath($path);
    }

    /**
     * {@inheritDoc}
     */
    public function setThemesPath(string $path): void
    {
        $this->themesPath = $path;
    }

    /**
     * {@inheritDoc}
     */
    public function themesPath(string $path = ''): string
    {
        return ($this->themesPath ?? $this->path.\DIRECTORY_SEPARATOR.'themes').
            $this->normalizePath($path);
    }

    /**
     * {@inheritDoc}
     */
    public function setOptionPath(string $path): void
    {
        $this->optionPath = $path;
    }

    /**
     * {@inheritDoc}
     */
    public function optionPath(string $path = ''): string
    {
        return ($this->optionPath ?? $this->path.\DIRECTORY_SEPARATOR.'option').
            $this->normalizePath($path);
    }

    /**
     * {@inheritDoc}
     */
    public function setI18nPath(string $path): void
    {
        $this->i18nPath = $path;
    }

    /**
     * {@inheritDoc}
     */
    public function i18nPath(?string $path = null): string
    {
        return ($this->i18nPath ?? $this->path.\DIRECTORY_SEPARATOR.'i18n').
            $this->normalizePath($path ?: '');
    }

    /**
     * {@inheritDoc}
     */
    public function setEnvPath(string $path): void
    {
        $this->envPath = $path;
    }

    /**
     * {@inheritDoc}
     */
    public function envPath(): string
    {
        return $this->envPath ?: $this->path;
    }

    /**
     * {@inheritDoc}
     */
    public function setEnvFile(string $file): void
    {
        $this->envFile = $file;
    }

    /**
     * {@inheritDoc}
     */
    public function envFile(): string
    {
        return $this->envFile ?: static::DEFAULT_ENV;
    }

    /**
     * {@inheritDoc}
     */
    public function fullEnvPath(): string
    {
        return $this->envPath().\DIRECTORY_SEPARATOR.$this->envFile();
    }

    /**
     * {@inheritDoc}
     */
    public function setI18nCachedPath(string $i18nCachedPath): void
    {
        $this->i18nCachedPath = $i18nCachedPath;
    }

    /**
     * {@inheritDoc}
     */
    public function i18nCachedPath(string $i18n): string
    {
        $basePath = $this->i18nCachedPath ?: $this->path().'/bootstrap/i18n';

        return $basePath.'/'.$i18n.'.php';
    }

    /**
     * {@inheritDoc}
     */
    public function isCachedI18n(string $i18n): bool
    {
        return is_file($this->i18nCachedPath($i18n));
    }

    /**
     * {@inheritDoc}
     */
    public function setOptionCachedPath(string $optionCachedPath): void
    {
        $this->optionCachedPath = $optionCachedPath;
    }

    /**
     * {@inheritDoc}
     * 
     * @since 2018.11.23 支持不同环境变量的缓存路径
     */
    public function optionCachedPath(): string
    {
        $basePath = $this->optionCachedPath ?: $this->path().'/bootstrap';
        $cache = getenv('RUNTIME_ENVIRONMENT') ?: 'option';

        return $basePath.'/'.$cache.'.php';
    }

    /**
     * {@inheritDoc}
     */
    public function isCachedOption(): bool
    {
        return is_file($this->optionCachedPath());
    }

    /**
     * {@inheritDoc}
     */
    public function setRouterCachedPath(string $routerCachedPath): void
    {
        $this->routerCachedPath = $routerCachedPath;
    }

    /**
     * {@inheritDoc}
     */
    public function routerCachedPath(): string
    {
        return $this->routerCachedPath ?: $this->path().'/bootstrap/router.php';
    }

    /**
     * {@inheritDoc}
     */
    public function isCachedRouter(): bool
    {
        return is_file($this->routerCachedPath());
    }

    /**
     * {@inheritDoc}
     * 
     * @throws \RuntimeException
     */
    public function namespacePath(string $specificClass): string
    {
        $composer = require $this->path.'/vendor/autoload.php';
        if (!$composer instanceof ClassLoader) {
            $e = 'Composer was not found.';

            throw new RuntimeException($e);
        }

        if (false === $path = $composer->findFile($specificClass)) {
            $e = sprintf('Specific class `%s` for finding namespaces was not found.', $specificClass);

            throw new RuntimeException($e);
        }

        return dirname($path);
    }

    /**
     * {@inheritDoc}
     */
    public function isDebug(): bool
    {
        return 'production' !== $this->environment() &&
            $this->container->make('option')->get('debug');
    }

    /**
     * {@inheritDoc}
     */
    public function isDevelopment(): bool
    {
        return 'development' === $this->environment();
    }

    /**
     * {@inheritDoc}
     */
    public function environment(): string
    {
        return $this->container
            ->make('option')
            ->get('environment');
    }

    /**
     * {@inheritDoc}
     */
    public function env(string $name, mixed $defaults = null): mixed
    {
        if (false === $value = getenv($name)) {
            $value = $defaults;
        }

        switch ($value) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }

        if (is_string($value) && strlen($value) > 1 &&
            str_starts_with($value, '"') && str_ends_with($value, '"')) {
            return substr($value, 1, -1);
        }

        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function bootstrap(array $bootstraps): void
    {
        if ($this->container->isBootstrap()) {
            return;
        }

        foreach ($bootstraps as $value) {
            (new $value())->handle($this);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function registerAppProviders(): void
    {
        list($deferredProviders, $deferredAlias) = $this->container
            ->make('option')
            ->get(':deferred_providers', [[], []]);

        $this->container->registerProviders(
            $this->container->make('option')->get(':composer.providers', []),
            $deferredProviders,
            $deferredAlias
        );
    }

    /**
     * {@inheritDoc}
     */
    public function container(): IContainer
    {
        return $this->container;
    }

    /**
     * 注册基础服务提供者.
     *
     * @codeCoverageIgnore
     */
    protected function registerBaseProvider(): void
    {
        $this->container->register(new EventProvider($this->container));
        $this->container->register(new LogProvider($this->container));
        $this->container->register(new RouterProvider($this->container));
    }

    /**
     * 格式化应用名字.
     */
    protected function normalizeApp(bool|string $app): string
    {
        return strtolower(true === $app ? ($this->container->make('app_name') ?: 'app') : $app);
    }

    /**
     * 格式化路径.
     */
    protected function normalizePath(string $path): string
    {
        return $path ? \DIRECTORY_SEPARATOR.$path : $path;
    }
}
