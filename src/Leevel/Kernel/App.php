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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
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
 * 应用管理.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.01.14
 *
 * @version 1.0
 */
class App implements IApp
{
    /**
     * IOC 容器.
     *
     * @var \Leevel\Di\IContainer
     */
    protected $container;

    /**
     * 应用基础路径.
     *
     * @var string
     */
    protected $path;

    /**
     * 应用路径.
     *
     * @var string
     */
    protected $appPath;

    /**
     * 公共路径.
     *
     * @var string
     */
    protected $commonPath;

    /**
     * 运行时路径.
     *
     * @var string
     */
    protected $runtimePath;

    /**
     * 存储路径.
     *
     * @var string
     */
    protected $storagePath;

    /**
     * 资源路径.
     *
     * @var string
     */
    protected $publicPath;

    /**
     * 主题路径.
     *
     * @var string
     */
    protected $themesPath;

    /**
     * 配置路径.
     *
     * @var string
     */
    protected $optionPath;

    /**
     * 语言包路径.
     *
     * @var string
     */
    protected $i18nPath;

    /**
     * 环境变量路径.
     *
     * @var string
     */
    protected $envPath;

    /**
     * 环境变量文件.
     *
     * @var string
     */
    protected $envFile;

    /**
     * 语言包缓存路径.
     *
     * @var string
     */
    protected $i18nCachedPath;

    /**
     * 配置缓存路径.
     *
     * @var string
     */
    protected $optionCachedPath;

    /**
     * 路由缓存路径.
     *
     * @var string
     */
    protected $routerCachedPath;

    /**
     * 构造函数
     * 应用中通过 singletons 生成单一实例.
     *
     * @param \Leevel\Di\IContainer $container
     * @param string                $path
     */
    public function __construct(IContainer $container, string $path)
    {
        $this->container = $container;

        $this->setPath($path);

        $this->registerBaseProvider();
    }

    /**
     * 程序版本.
     *
     * @return string
     */
    public function version(): string
    {
        return static::VERSION;
    }

    /**
     * 是否以扩展方式运行.
     *
     * @return bool
     */
    public function runWithExtension(): bool
    {
        return extension_loaded('leevel');
    }

    /**
     * 是否为 Console.
     *
     * @return bool
     */
    public function console(): bool
    {
        if (!is_object($this->container->make('request'))) {
            return \PHP_SAPI === 'cli';
        }

        return $this->container->make('request')->isCli();
    }

    /**
     * 设置应用路径.
     *
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * 基础路径.
     *
     * @param string $path
     *
     * @return string
     */
    public function path(string $path = ''): string
    {
        return $this->path.$this->normalizePath($path);
    }

    /**
     * 设置应用路径.
     *
     * @param string $path
     */
    public function setAppPath(string $path): void
    {
        $this->appPath = $path;
    }

    /**
     * 应用路径.
     *
     * @param bool|string $app
     * @param string      $path
     *
     * @return string
     */
    public function appPath($app = false, string $path = ''): string
    {
        return ($this->appPath ?? $this->path.\DIRECTORY_SEPARATOR.'application').
            ($app ? \DIRECTORY_SEPARATOR.$this->normalizeApp($app) : $app).
            $this->normalizePath($path);
    }

    /**
     * 取得应用主题目录.
     *
     * @param bool|string $app
     *
     * @return string
     */
    public function themePath($app = false): string
    {
        return $this->appPath($app).'/ui/theme';
    }

    /**
     * 设置公共路径.
     *
     * @param string $path
     */
    public function setCommonPath(string $path): void
    {
        $this->commonPath = $path;
    }

    /**
     * 公共路径.
     *
     * @param string $path
     *
     * @return string
     */
    public function commonPath(string $path = ''): string
    {
        return ($this->commonPath ?? $this->path.\DIRECTORY_SEPARATOR.'common').
            $this->normalizePath($path);
    }

    /**
     * 设置运行时路径.
     *
     * @param string $path
     */
    public function setRuntimePath(string $path): void
    {
        $this->runtimePath = $path;
    }

    /**
     * 运行路径.
     *
     * @param string $path
     *
     * @return string
     */
    public function runtimePath(string $path = ''): string
    {
        return ($this->runtimePath ?? $this->path.\DIRECTORY_SEPARATOR.'runtime').
            $this->normalizePath($path);
    }

    /**
     * 设置存储路径.
     *
     * @param string $path
     */
    public function setStoragePath(string $path): void
    {
        $this->storagePath = $path;
    }

    /**
     * 附件路径.
     *
     * @param string $path
     *
     * @return string
     */
    public function storagePath(string $path = ''): string
    {
        return ($this->storagePath ?? $this->path.\DIRECTORY_SEPARATOR.'storage').
            $this->normalizePath($path);
    }

    /**
     * 设置资源路径.
     *
     * @param string $path
     */
    public function setPublicPath(string $path): void
    {
        $this->publicPath = $path;
    }

    /**
     * 资源路径.
     *
     * @param string $path
     *
     * @return string
     */
    public function publicPath(string $path = ''): string
    {
        return ($this->publicPath ?? $this->path.\DIRECTORY_SEPARATOR.'public').
            $this->normalizePath($path);
    }

    /**
     * 设置主题路径.
     *
     * @param string $path
     */
    public function setThemesPath(string $path): void
    {
        $this->themesPath = $path;
    }

    /**
     * 主题路径.
     *
     * @param string $path
     *
     * @return string
     */
    public function themesPath(string $path = ''): string
    {
        return ($this->themesPath ?? $this->path.\DIRECTORY_SEPARATOR.'themes').
            $this->normalizePath($path);
    }

    /**
     * 设置配置路径.
     *
     * @param string $path
     */
    public function setOptionPath(string $path): void
    {
        $this->optionPath = $path;
    }

    /**
     * 配置路径.
     *
     * @param string $path
     *
     * @return string
     */
    public function optionPath(string $path = ''): string
    {
        return ($this->optionPath ?? $this->path.\DIRECTORY_SEPARATOR.'option').
            $this->normalizePath($path);
    }

    /**
     * 设置语言包路径.
     *
     * @param string $path
     */
    public function setI18nPath(string $path): void
    {
        $this->i18nPath = $path;
    }

    /**
     * 语言包路径.
     *
     * @param null|string $path
     *
     * @return string
     */
    public function i18nPath(?string $path = null): string
    {
        return ($this->i18nPath ?? $this->path.\DIRECTORY_SEPARATOR.'i18n').
            $this->normalizePath($path ?: '');
    }

    /**
     * 设置环境变量路径.
     *
     * @param string $path
     */
    public function setEnvPath(string $path): void
    {
        $this->envPath = $path;
    }

    /**
     * 环境变量路径.
     *
     * @return string
     */
    public function envPath(): string
    {
        return $this->envPath ?: $this->path;
    }

    /**
     * 设置环境变量文件.
     *
     * @param string $file
     */
    public function setEnvFile(string $file): void
    {
        $this->envFile = $file;
    }

    /**
     * 取得环境变量文件.
     *
     * @return string
     */
    public function envFile(): string
    {
        return $this->envFile ?: static::DEFAULT_ENV;
    }

    /**
     * 取得环境变量完整路径.
     *
     * @return string
     */
    public function fullEnvPath(): string
    {
        return $this->envPath().\DIRECTORY_SEPARATOR.$this->envFile();
    }

    /**
     * 设置语言包缓存路径.
     *
     * @param string $i18nCachedPath
     */
    public function setI18nCachedPath(string $i18nCachedPath): void
    {
        $this->i18nCachedPath = $i18nCachedPath;
    }

    /**
     * 返回语言包缓存路径.
     *
     * @param string $i18n
     *
     * @return string
     */
    public function i18nCachedPath(string $i18n): string
    {
        $basePath = $this->i18nCachedPath ?: $this->path().'/bootstrap/i18n';

        return $basePath.'/'.$i18n.'.php';
    }

    /**
     * 是否存在语言包缓存.
     *
     * @param string $i18n
     *
     * @return bool
     */
    public function isCachedI18n(string $i18n): bool
    {
        return is_file($this->i18nCachedPath($i18n));
    }

    /**
     * 设置配置缓存路径.
     *
     * @param string $optionCachedPath
     */
    public function setOptionCachedPath(string $optionCachedPath): void
    {
        $this->optionCachedPath = $optionCachedPath;
    }

    /**
     * 返回配置缓存路径.
     *
     * @since 2018.11.23 支持不同环境变量的缓存路径
     *
     * @return string
     */
    public function optionCachedPath(): string
    {
        $basePath = $this->optionCachedPath ?: $this->path().'/bootstrap';

        $cache = getenv('RUNTIME_ENVIRONMENT') ?: 'option';

        return $basePath.'/'.$cache.'.php';
    }

    /**
     * 是否存在配置缓存.
     *
     * @return bool
     */
    public function isCachedOption(): bool
    {
        return is_file($this->optionCachedPath());
    }

    /**
     * 设置路由缓存路径.
     *
     * @param string $routerCachedPath
     */
    public function setRouterCachedPath(string $routerCachedPath): void
    {
        $this->routerCachedPath = $routerCachedPath;
    }

    /**
     * 返回路由缓存路径.
     *
     * @return string
     */
    public function routerCachedPath(): string
    {
        return $this->routerCachedPath ?: $this->path().'/bootstrap/router.php';
    }

    /**
     * 是否存在路由缓存.
     *
     * @return bool
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
     * @param string $specificClass
     * @param bool   $throwException
     *
     * @throws \RuntimeException
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function namespacePath(string $specificClass, bool $throwException = true): string
    {
        $composer = $this->container->make('composer');

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
     * 是否开启 debug.
     *
     * @return bool
     */
    public function debug(): bool
    {
        return 'production' !== $this->environment() &&
            $this->container->make('option')->get('debug');
    }

    /**
     * 是否为开发环境.
     *
     * @return bool
     */
    public function development(): bool
    {
        return 'development' === $this->environment();
    }

    /**
     * 运行环境.
     *
     * @return string
     */
    public function environment(): string
    {
        return $this->container
            ->make('option')
            ->get('environment');
    }

    /**
     * 初始化应用.
     *
     * @param array $bootstraps
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
     * 框架基础提供者 register.
     */
    public function registerProviders(): void
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
     *
     * @return \Leevel\Di\IContainer
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
     *
     * @return string
     */
    protected function normalizeApp($app): string
    {
        return strtolower(true === $app ? ($this->container->make('app_name') ?: 'app') : $app);
    }

    /**
     * 格式化路径.
     *
     * @param string $path
     *
     * @return string
     */
    protected function normalizePath(string $path): string
    {
        return $path ? \DIRECTORY_SEPARATOR.$path : $path;
    }
}
