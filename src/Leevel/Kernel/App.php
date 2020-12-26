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
     * 存储路径.
    */
    protected string $storagePath;

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
    public function appPath(string $path = ''): string
    {
        return ($this->appPath ?? $this->path.\DIRECTORY_SEPARATOR.'apps').
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
    public function setThemesPath(string $path): void
    {
        $this->themesPath = $path;
    }

    /**
     * {@inheritDoc}
     */
    public function themesPath(string $path = ''): string
    {
        return ($this->themesPath ?? $this->path.\DIRECTORY_SEPARATOR.'assets/themes').
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
        return ($this->i18nPath ?? $this->path.\DIRECTORY_SEPARATOR.'assets/i18n').
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
        $basePath = $this->i18nCachedPath ?: $this->storagePath().'/bootstrap/i18n';

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
     */
    public function optionCachedPath(): string
    {
        $basePath = $this->optionCachedPath ?: $this->storagePath().'/bootstrap';
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
        return $this->routerCachedPath ?: $this->storagePath().'/bootstrap/router.php';
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
     * 格式化路径.
     */
    protected function normalizePath(string $path): string
    {
        return $path ? \DIRECTORY_SEPARATOR.$path : $path;
    }
}
