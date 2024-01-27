<?php

declare(strict_types=1);

namespace Leevel\Kernel;

use Composer\Autoload\ClassLoader;
use Leevel\Config\IConfig;
use Leevel\Di\IContainer;
use Leevel\Event\Provider\Register as EventProvider;
use Leevel\Log\Provider\Register as LogProvider;
use Leevel\Router\Provider\Register as RouterProvider;

/**
 * 应用.
 */
class App implements IApp
{
    /**
     * IOC 容器.
     */
    protected IContainer $container; /** @phpstan-ignore-line */

    /**
     * 应用基础路径.
     */
    protected ?string $path = null;

    /**
     * 应用路径.
     */
    protected ?string $appPath = null;

    /**
     * 存储路径.
     */
    protected ?string $storagePath = null;

    /**
     * 主题路径.
     */
    protected ?string $themesPath = null;

    /**
     * 配置路径.
     */
    protected ?string $configPath = null;

    /**
     * 语言包路径.
     */
    protected ?string $i18nPath = null;

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
    protected ?string $configCachedPath = null;

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
        if (null === $this->container->make('request', throw: false)) {
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
        $this->path = $this->realpath($path);
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
        $this->appPath = $this->realpath($path);
    }

    /**
     * {@inheritDoc}
     */
    public function appPath(string $path = ''): string
    {
        return ($this->appPath ?? $this->path).
            $this->normalizePath($path);
    }

    /**
     * {@inheritDoc}
     */
    public function setStoragePath(string $path): void
    {
        $this->storagePath = $this->realpath($path);
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
        $this->themesPath = $this->realpath($path);
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
    public function setConfigPath(string $path): void
    {
        $this->configPath = $this->realpath($path);
    }

    /**
     * {@inheritDoc}
     */
    public function configPath(string $path = ''): string
    {
        return ($this->configPath ?? $this->path.\DIRECTORY_SEPARATOR.'config').
            $this->normalizePath($path);
    }

    /**
     * {@inheritDoc}
     */
    public function setI18nPath(string $path): void
    {
        $this->i18nPath = $this->realpath($path);
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
        $this->envPath = $this->realpath($path);
    }

    /**
     * {@inheritDoc}
     */
    public function envPath(): string
    {
        return $this->envPath ?: ($this->path ?: '');
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
        $this->i18nCachedPath = $this->realpath($i18nCachedPath);
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
    public function setConfigCachedPath(string $configCachedPath): void
    {
        $this->configCachedPath = $this->realpath($configCachedPath);
    }

    /**
     * {@inheritDoc}
     */
    public function configCachedPath(): string
    {
        $basePath = $this->configCachedPath ?: $this->storagePath().'/bootstrap';
        $cache = getenv('RUNTIME_ENVIRONMENT') ?: 'config';

        return $basePath.'/'.$cache.'.php';
    }

    /**
     * {@inheritDoc}
     */
    public function isCachedConfig(): bool
    {
        return is_file($this->configCachedPath());
    }

    /**
     * {@inheritDoc}
     */
    public function setRouterCachedPath(string $routerCachedPath): void
    {
        $this->routerCachedPath = $this->realpath($routerCachedPath);
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
    public function namespacePath(string $namespace): string
    {
        $composer = require $this->path.'/vendor/autoload.php';
        if (!$composer instanceof ClassLoader) {
            throw new \RuntimeException('Composer was not found.');
        }

        if (false === $path = $this->findNamespacePathByComposer($composer, $namespace.'\\')) {
            throw new \RuntimeException(sprintf('Namespaces `%s` for was not found.', $namespace));
        }

        return $this->realpath($path);
    }

    /**
     * {@inheritDoc}
     */
    public function isDebug(): bool
    {
        if (null === ($config = $this->container->make('config', throw: false))) {
            return true;
        }

        // @phpstan-ignore-next-line
        return AppEnvEnum::PRODUCTION->value !== $this->environment() && $config->get('debug');
    }

    /**
     * {@inheritDoc}
     */
    public function isDevelopment(): bool
    {
        return AppEnvEnum::DEVELOPMENT->value === $this->environment();
    }

    /**
     * {@inheritDoc}
     */
    public function environment(): string
    {
        if (null === ($config = $this->container->make('config', throw: false))) {
            return AppEnvEnum::DEVELOPMENT->value;
        }

        // @phpstan-ignore-next-line
        return $config->get('environment');
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

        if (\is_string($value) && \strlen($value) > 1
            && str_starts_with($value, '"') && str_ends_with($value, '"')) {
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
            // @phpstan-ignore-next-line
            (new $value())->handle($this);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function registerAppProviders(): void
    {
        /** @var IConfig $config */
        $config = $this->container->make('config');

        // @phpstan-ignore-next-line
        [$deferredProviders, $deferredAlias] = $config->get(':deferred_providers', [[], []]);

        $this->container->registerProviders(
            $config->get(':composer.providers', []),
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
     * @see 参考 \Composer\Autoload\ClassLoader::findFile
     */
    protected function findNamespacePathByComposer(ClassLoader $composer, string $namespace): string|false
    {
        // PSR-4 lookup
        $logicalPathPsr4 = strtr($namespace, '\\', \DIRECTORY_SEPARATOR);
        $prefixDirsPsr4 = $composer->getPrefixesPsr4();
        $subPath = $namespace;
        while (false !== $lastPos = strrpos($subPath, '\\')) {
            $subPath = substr($subPath, 0, $lastPos);
            $search = $subPath.'\\';
            if (isset($prefixDirsPsr4[$search])) {
                $pathEnd = \DIRECTORY_SEPARATOR.substr($logicalPathPsr4, $lastPos + 1);
                foreach ($prefixDirsPsr4[$search] as $dir) {
                    if (is_dir($file = $dir.$pathEnd)) {
                        return $file;
                    }
                }
            }
        }

        // PSR-4 fallback dirs
        foreach ($composer->getFallbackDirsPsr4() as $dir) {
            if (is_dir($file = $dir.\DIRECTORY_SEPARATOR.$logicalPathPsr4)) {
                return $file;
            }
        }

        // 移除掉尾巴的反斜杠
        $namespace = rtrim($namespace, '\\');

        // PSR-0 lookup
        if (false !== $pos = strrpos($namespace, '\\')) {
            // namespaced class name
            $logicalPathPsr0 = substr($logicalPathPsr4, 0, $pos + 1)
                .strtr(substr($logicalPathPsr4, $pos + 1), '_', \DIRECTORY_SEPARATOR);
        } else {
            // PEAR-like class name
            $logicalPathPsr0 = strtr($namespace, '_', \DIRECTORY_SEPARATOR);
        }

        foreach ($composer->getPrefixes() as $prefix => $dirs) {
            if (str_starts_with($namespace, $prefix)) {
                // @phpstan-ignore-next-line
                foreach ($dirs as $dir) {
                    if (is_dir($file = $dir.\DIRECTORY_SEPARATOR.$logicalPathPsr0)) {
                        return $file;
                    }
                }
            }
        }

        // PSR-0 fallback dirs
        foreach ($composer->getFallbackDirs() as $dir) {
            if (is_dir($file = $dir.\DIRECTORY_SEPARATOR.$logicalPathPsr0)) {
                return $file;
            }
        }

        return false;
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

    protected function realpath(string $path): string
    {
        return realpath($path) ?: $path;
    }
}
