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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
     *
     * @var \Leevel\Di\IContainer
     */
    protected IContainer $container;

    /**
     * 应用基础路径.
     *
     * @var string
     */
    protected string $path;

    /**
     * 应用路径.
     *
     * @var string
     */
    protected string $appPath;

    /**
     * 公共路径.
     *
     * @var string
     */
    protected string $commonPath;

    /**
     * 运行时路径.
     *
     * @var string
     */
    protected string $runtimePath;

    /**
     * 存储路径.
     *
     * @var string
     */
    protected string $storagePath;

    /**
     * 资源路径.
     *
     * @var string
     */
    protected string $publicPath;

    /**
     * 主题路径.
     *
     * @var string
     */
    protected string $themesPath;

    /**
     * 配置路径.
     *
     * @var string
     */
    protected string $optionPath;

    /**
     * 语言包路径.
     *
     * @var string
     */
    protected string $i18nPath;

    /**
     * 环境变量路径.
     *
     * @var string
     */
    protected ?string $envPath = null;

    /**
     * 环境变量文件.
     *
     * @var string
     */
    protected ?string $envFile = null;

    /**
     * 语言包缓存路径.
     *
     * @var string
     */
    protected ?string $i18nCachedPath = null;

    /**
     * 配置缓存路径.
     *
     * @var string
     */
    protected ?string $optionCachedPath = null;

    /**
     * 路由缓存路径.
     *
     * @var string
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
     * 获取程序版本.
     */
    public function version(): string
    {
        return static::VERSION;
    }

    /**
     * 是否为 PHP 运行模式命令行.
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
     * 设置基础路径.
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * 获取基础路径.
     */
    public function path(string $path = ''): string
    {
        return $this->path.$this->normalizePath($path);
    }

    /**
     * 设置应用路径.
     */
    public function setAppPath(string $path): void
    {
        $this->appPath = $path;
    }

    /**
     * 获取应用路径.
     *
     * @param bool|string $app
     */
    public function appPath($app = false, string $path = ''): string
    {
        return ($this->appPath ?? $this->path.\DIRECTORY_SEPARATOR.'application').
            ($app ? \DIRECTORY_SEPARATOR.$this->normalizeApp($app) : $app).
            $this->normalizePath($path);
    }

    /**
     * 获取应用主题目录.
     *
     * @param bool|string $app
     */
    public function themePath($app = false): string
    {
        return $this->appPath($app).'/ui/theme';
    }

    /**
     * 设置公共路径.
     */
    public function setCommonPath(string $path): void
    {
        $this->commonPath = $path;
    }

    /**
     * 获取公共路径.
     */
    public function commonPath(string $path = ''): string
    {
        return ($this->commonPath ?? $this->path.\DIRECTORY_SEPARATOR.'common').
            $this->normalizePath($path);
    }

    /**
     * 设置运行时路径.
     */
    public function setRuntimePath(string $path): void
    {
        $this->runtimePath = $path;
    }

    /**
     * 获取运行路径.
     */
    public function runtimePath(string $path = ''): string
    {
        return ($this->runtimePath ?? $this->path.\DIRECTORY_SEPARATOR.'runtime').
            $this->normalizePath($path);
    }

    /**
     * 设置附件存储路径.
     */
    public function setStoragePath(string $path): void
    {
        $this->storagePath = $path;
    }

    /**
     * 获取附件存储路径.
     */
    public function storagePath(string $path = ''): string
    {
        return ($this->storagePath ?? $this->path.\DIRECTORY_SEPARATOR.'storage').
            $this->normalizePath($path);
    }

    /**
     * 设置资源路径.
     */
    public function setPublicPath(string $path): void
    {
        $this->publicPath = $path;
    }

    /**
     * 获取资源路径.
     */
    public function publicPath(string $path = ''): string
    {
        return ($this->publicPath ?? $this->path.\DIRECTORY_SEPARATOR.'public').
            $this->normalizePath($path);
    }

    /**
     * 设置主题路径.
     */
    public function setThemesPath(string $path): void
    {
        $this->themesPath = $path;
    }

    /**
     * 获取主题路径.
     */
    public function themesPath(string $path = ''): string
    {
        return ($this->themesPath ?? $this->path.\DIRECTORY_SEPARATOR.'themes').
            $this->normalizePath($path);
    }

    /**
     * 设置配置路径.
     */
    public function setOptionPath(string $path): void
    {
        $this->optionPath = $path;
    }

    /**
     * 获取配置路径.
     */
    public function optionPath(string $path = ''): string
    {
        return ($this->optionPath ?? $this->path.\DIRECTORY_SEPARATOR.'option').
            $this->normalizePath($path);
    }

    /**
     * 设置语言包路径.
     */
    public function setI18nPath(string $path): void
    {
        $this->i18nPath = $path;
    }

    /**
     * 获取语言包路径.
     */
    public function i18nPath(?string $path = null): string
    {
        return ($this->i18nPath ?? $this->path.\DIRECTORY_SEPARATOR.'i18n').
            $this->normalizePath($path ?: '');
    }

    /**
     * 设置环境变量路径.
     */
    public function setEnvPath(string $path): void
    {
        $this->envPath = $path;
    }

    /**
     * 获取环境变量路径.
     */
    public function envPath(): string
    {
        return $this->envPath ?: $this->path;
    }

    /**
     * 设置环境变量文件.
     */
    public function setEnvFile(string $file): void
    {
        $this->envFile = $file;
    }

    /**
     * 获取环境变量文件.
     */
    public function envFile(): string
    {
        return $this->envFile ?: static::DEFAULT_ENV;
    }

    /**
     * 获取环境变量完整路径.
     */
    public function fullEnvPath(): string
    {
        return $this->envPath().\DIRECTORY_SEPARATOR.$this->envFile();
    }

    /**
     * 设置语言包缓存路径.
     */
    public function setI18nCachedPath(string $i18nCachedPath): void
    {
        $this->i18nCachedPath = $i18nCachedPath;
    }

    /**
     * 获取语言包缓存路径.
     */
    public function i18nCachedPath(string $i18n): string
    {
        $basePath = $this->i18nCachedPath ?: $this->path().'/bootstrap/i18n';

        return $basePath.'/'.$i18n.'.php';
    }

    /**
     * 是否存在语言包缓存.
     */
    public function isCachedI18n(string $i18n): bool
    {
        return is_file($this->i18nCachedPath($i18n));
    }

    /**
     * 设置配置缓存路径.
     */
    public function setOptionCachedPath(string $optionCachedPath): void
    {
        $this->optionCachedPath = $optionCachedPath;
    }

    /**
     * 获取配置缓存路径.
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
     * 是否存在配置缓存.
     */
    public function isCachedOption(): bool
    {
        return is_file($this->optionCachedPath());
    }

    /**
     * 设置路由缓存路径.
     */
    public function setRouterCachedPath(string $routerCachedPath): void
    {
        $this->routerCachedPath = $routerCachedPath;
    }

    /**
     * 获取路由缓存路径.
     */
    public function routerCachedPath(): string
    {
        return $this->routerCachedPath ?: $this->path().'/bootstrap/router.php';
    }

    /**
     * 是否存在路由缓存.
     */
    public function isCachedRouter(): bool
    {
        return is_file($this->routerCachedPath());
    }

    /**
     * 获取命名空间目录真实路径.
     *
     * - 一般用于获取文件 PSR4 所在的命名空间，当然如果存在命名空间。
     * - 基于某个具体的类查询该类目录的真实路径。
     * - 为简化开发和提升性能，必须提供具体的存在的类才能够获取目录的真实路径。
     *
     * @throws \RuntimeException
     *
     * @codeCoverageIgnore
     */
    public function namespacePath(string $specificClass, bool $throwException = true): string
    {
        $composer = require $this->path.'/vendor/autoload.php';

        if (!$composer instanceof ClassLoader) {
            $e = 'Composer was not register to container.';

            throw new RuntimeException($e);
        }

        if (false === $path = $composer->findFile($specificClass)) {
            if (true === $throwException) {
                $e = sprintf('Specific class `%s` for finding namespaces was not found.', $specificClass);

                throw new RuntimeException($e);
            }

            return '';
        }

        return dirname($path);
    }

    /**
     * 是否开启调试.
     */
    public function isDebug(): bool
    {
        return 'production' !== $this->environment() &&
            $this->container->make('option')->get('debug');
    }

    /**
     * 是否为开发环境.
     */
    public function isDevelopment(): bool
    {
        return 'development' === $this->environment();
    }

    /**
     * 获取运行环境.
     */
    public function environment(): string
    {
        return $this->container
            ->make('option')
            ->get('environment');
    }

    /**
     * 获取应用的环境变量.
     *
     * - 环境变量支持 boolean, empty 和 null 值.
     *
     * @param null|mixed $defaults
     *
     * @return mixed
     */
    public function env(string $name, $defaults = null)
    {
        return env($name, $defaults);
    }

    /**
     * 初始化应用.
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
     * 注册应用服务提供者.
     */
    public function registerAppProviders(): void
    {
        list($deferredProviders, $deferredAlias) = $this->container
            ->make('option')
            ->get('_deferred_providers', [[], []]);

        $this->container->registerProviders(
            $this->container->make('option')->get('_composer.providers', []),
            $deferredProviders, $deferredAlias
        );
    }

    /**
     * 返回 IOC 容器.
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
     *
     * @param bool|string $app
     */
    protected function normalizeApp($app): string
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
