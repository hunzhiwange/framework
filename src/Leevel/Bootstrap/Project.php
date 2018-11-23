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

namespace Leevel\Bootstrap;

use Composer\Autoload\ClassLoader;
use Leevel\Di\Container;
use Leevel\Di\Provider;
use Leevel\Event\Provider\Register as EventProvider;
use Leevel\Kernel\IProject;
use Leevel\Log\Provider\Register as LogProvider;
use Leevel\Router\Provider\Register as RouterProvider;
use RuntimeException;

/**
 * 项目管理.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.01.14
 *
 * @version 1.0
 */
class Project extends Container implements IProject
{
    /**
     * 当前项目实例.
     *
     * @var static
     */
    protected static $project;

    /**
     * 项目基础路径.
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
     * 延迟载入服务提供者.
     *
     * @var array
     */
    protected $deferredProviders = [];

    /**
     * 服务提供者引导
     *
     * @var array
     */
    protected $providerBootstraps = [];

    /**
     * 是否已经初始化引导
     *
     * @var bool
     */
    protected $isBootstrap = false;

    /**
     * 构造函数
     * 项目中通过 singletons 生成单一实例.
     *
     * @param string $path
     */
    public function __construct(?string $path = null)
    {
        if ($path) {
            $this->setPath($path);
        }

        $this->registerBaseServices();

        $this->registerBaseProvider();
    }

    /**
     * 禁止克隆.
     */
    public function __clone()
    {
        throw new RuntimeException('Project disallowed clone.');
    }

    /**
     * 返回项目.
     *
     * @param string $path
     *
     * @return static
     * @codeCoverageIgnore
     */
    public static function singletons(?string $path = null)
    {
        if (null !== static::$project) {
            return static::$project;
        }

        return static::$project = new static($path);
    }

    /**
     * 程序版本.
     *
     * @return string
     */
    public function version()
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
        if (!is_object($this->make('request'))) {
            return PHP_SAPI === 'cli';
        }

        return $this['request']->isCli();
    }

    /**
     * {@inheritdoc}
     */
    public function make($name, array $args = [])
    {
        $name = $this->getAlias($name);

        if (isset($this->deferredProviders[$name])) {
            $this->registerDeferredProvider($name);
        }

        return parent::make($name, $args);
    }

    /**
     * 设置项目路径.
     *
     * @param string $path
     */
    public function setPath(string $path)
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
    public function path(string $path = '')
    {
        return $this->path.$this->normalizePath($path);
    }

    /**
     * 设置应用路径.
     *
     * @param string $path
     */
    public function setAppPath(string $path)
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
    public function appPath($app = false, string $path = '')
    {
        return ($this->appPath ?? $this->path.DIRECTORY_SEPARATOR.'application').
            ($app ? DIRECTORY_SEPARATOR.$this->normalizeApp($app) : $app).
            $this->normalizePath($path);
    }

    /**
     * 取得应用主题目录.
     *
     * @param bool|string $app
     *
     * @return string
     */
    public function themePath($app = false)
    {
        return $this->appPath($app).'/ui/theme';
    }

    /**
     * 设置公共路径.
     *
     * @param string $path
     */
    public function setCommonPath(string $path)
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
    public function commonPath(string $path = '')
    {
        return ($this->commonPath ?? $this->path.DIRECTORY_SEPARATOR.'common').
            $this->normalizePath($path);
    }

    /**
     * 设置运行时路径.
     *
     * @param string $path
     */
    public function setRuntimePath(string $path)
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
    public function runtimePath(string $path = '')
    {
        return ($this->runtimePath ?? $this->path.DIRECTORY_SEPARATOR.'runtime').
            $this->normalizePath($path);
    }

    /**
     * 设置存储路径.
     *
     * @param string $path
     */
    public function setStoragePath(string $path)
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
    public function storagePath(string $path = '')
    {
        return ($this->storagePath ?? $this->path.DIRECTORY_SEPARATOR.'storage').
            $this->normalizePath($path);
    }

    /**
     * 设置资源路径.
     *
     * @param string $path
     */
    public function setPublicPath(string $path)
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
    public function publicPath(string $path = '')
    {
        return ($this->publicPath ?? $this->path.DIRECTORY_SEPARATOR.'public').
            $this->normalizePath($path);
    }

    /**
     * 设置主题路径.
     *
     * @param string $path
     */
    public function setThemesPath(string $path)
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
    public function themesPath(string $path = '')
    {
        return ($this->themesPath ?? $this->path.DIRECTORY_SEPARATOR.'themes').
            $this->normalizePath($path);
    }

    /**
     * 设置配置路径.
     *
     * @param string $path
     */
    public function setOptionPath(string $path)
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
    public function optionPath(string $path = '')
    {
        return ($this->optionPath ?? $this->path.DIRECTORY_SEPARATOR.'option').
            $this->normalizePath($path);
    }

    /**
     * 设置语言包路径.
     *
     * @param string $path
     */
    public function setI18nPath(string $path)
    {
        $this->i18nPath = $path;
    }

    /**
     * 语言包路径.
     *
     * @param string $path
     *
     * @return string
     */
    public function i18nPath($path = null)
    {
        return ($this->i18nPath ?? $this->path.DIRECTORY_SEPARATOR.'i18n').
            $this->normalizePath($path ?: '');
    }

    /**
     * 设置环境变量路径.
     *
     * @param string $path
     */
    public function setEnvPath(string $path)
    {
        $this->envPath = $path;
    }

    /**
     * 环境变量路径.
     *
     * @return string
     */
    public function envPath()
    {
        return $this->envPath ?: $this->path;
    }

    /**
     * 设置环境变量文件.
     *
     * @param string $file
     */
    public function setEnvFile($file)
    {
        $this->envFile = $file;
    }

    /**
     * 取得环境变量文件.
     *
     * @return string
     */
    public function envFile()
    {
        return $this->envFile ?: static::DEFAULT_ENV;
    }

    /**
     * 取得环境变量完整路径.
     *
     * @return string
     */
    public function fullEnvPath()
    {
        return $this->envPath().DIRECTORY_SEPARATOR.$this->envFile();
    }

    /**
     * 返回语言包缓存路径.
     *
     * @param string $i18n
     *
     * @return string
     */
    public function i18nCachedPath($i18n): string
    {
        return $this->runtimePath().'/i18n/'.$i18n.'.php';
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
     * 返回配置缓存路径.
     *
     * @since 2018.11.23 支持不同环境变量的缓存路径
     *
     * @return string
     */
    public function optionCachedPath(): string
    {
        $cache = getenv('ENVIRONMENT') ?: 'option';

        return $this->runtimePath().'/bootstrap/'.$cache.'.php';
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
     * 返回路由缓存路径.
     *
     * @return string
     */
    public function routerCachedPath(): string
    {
        return $this->runtimePath().'/bootstrap/router.php';
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
     * 取得 composer.
     *
     * @return \Composer\Autoload\ClassLoader
     * @codeCoverageIgnore
     */
    public function composer(): ClassLoader
    {
        return require $this->path.'/vendor/autoload.php';
    }

    /**
     * 获取命名空间路径.
     *
     * @param string $namespaces
     *
     * @return null|string
     * @codeCoverageIgnore
     */
    public function getPathByComposer($namespaces)
    {
        $namespaces = explode('\\', $namespaces);

        $prefix = $this->composer()->getPrefixesPsr4();

        if (!isset($prefix[$namespaces[0].'\\'])) {
            return;
        }

        $namespaces[0] = $prefix[$namespaces[0].'\\'][0];

        return implode('/', $namespaces);
    }

    /**
     * 是否开启 debug.
     *
     * @return bool
     */
    public function debug(): bool
    {
        return $this->make('option')->get('debug');
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
        return $this->make('option')->get('environment');
    }

    /**
     * 创建服务提供者.
     *
     * @param string $provider
     *
     * @return \Leevel\Di\Provider
     */
    public function makeProvider(string $provider): Provider
    {
        return new $provider($this);
    }

    /**
     * 执行 bootstrap.
     *
     * @param \Leevel\Di\Provider $provider
     */
    public function callProviderBootstrap(Provider $provider)
    {
        if (!method_exists($provider, 'bootstrap')) {
            return;
        }

        $this->call([
            $provider,
            'bootstrap',
        ]);
    }

    /**
     * 初始化项目.
     *
     * @param array $bootstraps
     */
    public function bootstrap(array $bootstraps)
    {
        if ($this->isBootstrap) {
            return;
        }

        foreach ($bootstraps as $value) {
            (new $value())->handle($this);
        }
    }

    /**
     * 是否已经初始化引导
     *
     * @return bool
     */
    public function isBootstrap(): bool
    {
        return $this->isBootstrap;
    }

    /**
     * 框架基础提供者 register.
     *
     * @return $this
     */
    public function registerProviders()
    {
        if ($this->isBootstrap) {
            return;
        }

        list($this->deferredProviders, $deferredAlias) = $this->make('option')->get('_deferred_providers', [[], []]);

        foreach ($deferredAlias as $alias) {
            $this->alias($alias);
        }

        $providers = $this->make('option')->get('_composer.providers', []);

        foreach ($providers as $provider) {
            $provider = $this->register($provider);

            if (method_exists($provider, 'bootstrap')) {
                $this->providerBootstraps[] = $provider;
            }
        }

        return $this;
    }

    /**
     * 执行框架基础提供者引导
     *
     * @return $this
     */
    public function bootstrapProviders()
    {
        if ($this->isBootstrap) {
            return;
        }

        foreach ($this->providerBootstraps as $item) {
            $this->callProviderBootstrap($item);
        }

        $this->isBootstrap = true;

        return $this;
    }

    /**
     * 注册服务提供者.
     *
     * @param \Leevel\Di\Provider|string $provider
     *
     * @return \Leevel\Di\Provider
     */
    public function register($provider): Provider
    {
        if (is_string($provider)) {
            $provider = $this->makeProvider($provider);
        }

        if (method_exists($provider, 'register')) {
            $provider->register();
        }

        if ($this->isBootstrap()) {
            $this->callProviderBootstrap($provider);
        }

        return $provider;
    }

    /**
     * 注册基础服务
     */
    protected function registerBaseServices()
    {
        $this->instance('project', $this);

        $this->alias([
            'project' => [
                'Leevel\\Bootstrap\\Project',
                'Leevel\\Di\\IContainer',
                'Leevel\\Kernel\\IProject',
                'app',
            ],
            'request' => [
                'Leevel\\Http\\IRequest',
                'Leevel\\Http\\Request',
            ],
            'option' => [
                'Leevel\\Option\\IOption',
                'Leevel\\Option\\Option',
            ],
            'i18n' => [
                'Leevel\\I18n\\I18n',
                'Leevel\\I18n\\II18n',
            ],
        ]);
    }

    /**
     * 注册基础服务提供者.
     *
     * @codeCoverageIgnore
     */
    protected function registerBaseProvider()
    {
        $this->register(new EventProvider($this));

        $this->register(new LogProvider($this));

        $this->register(new RouterProvider($this));
    }

    /**
     * 注册延迟载入服务提供者.
     *
     * @param string $provider
     */
    protected function registerDeferredProvider(string $provider)
    {
        if (!isset($this->deferredProviders[$provider])) {
            return;
        }

        $providerInstance = $this->register($this->deferredProviders[$provider]);

        $this->callProviderBootstrap($providerInstance);

        unset($this->deferredProviders[$provider]);
    }

    /**
     * 格式化应用名字.
     *
     * @param bool|string $app
     *
     * @return string
     */
    protected function normalizeApp($app)
    {
        return strtolower(true === $app ? ($this->make('app_name') ?: 'app') : $app);
    }

    /**
     * 格式化路径.
     *
     * @param string $path
     *
     * @return string
     */
    protected function normalizePath(string $path)
    {
        return $path ? DIRECTORY_SEPARATOR.$path : $path;
    }
}
