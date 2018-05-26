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
namespace Leevel\Bootstrap;

use Exception;
use RuntimeException;
use Leevel\{
    Di\Provider,
    Di\Container,
    Kernel\IProject,
    Log\Provider\Register as LogProvider,
    Event\Provider\Register as EventProvider,
    Router\Provider\Register as RouterProvider
};

/**
 * 项目管理
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.01.14
 * @version 1.0
 */
class Project extends Container implements IProject
{

    /**
     * 当前项目实例
     *
     * @var static
     */
    protected static $project;

    /**
     * 项目基础路径
     *
     * @var string
     */
    protected $path;

    /**
     * 应用路径
     *
     * @var string
     */
    protected $applicationPath;

    /**
     * 公共路径
     *
     * @var string
     */
    protected $commonPath;

    /**
     * 运行时路径
     *
     * @var string
     */
    protected $runtimePath;

    /**
     * 存储路径
     *
     * @var string
     */
    protected $storagePath;

    /**
     * 配置路径
     *
     * @var string
     */
    protected $optionPath;

    /**
     * 语言包路径
     *
     * @var string
     */
    protected $i18nPath;

    /**
     * 环境变量路径
     *
     * @var string
     */
    protected $envPath;

    /**
     * 环境变量文件
     *
     * @var string
     */
    protected $envFile;

    /**
     * 延迟载入服务提供者
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
     * 受保护的禁止外部通过 new 实例化，只能通过 singletons 生成单一实例
     *
     * @param string $path
     * @return void
     */
    protected function __construct(?string $path = null)
    {
        if ($path) {
            $this->setPath($path);
        }

        $this->registerBaseServices();

        $this->registerBaseProvider();
    }

    /**
     * 禁止克隆
     *
     * @return void
     */
    protected function __clone()
    {
        throw new RuntimeException('Project disallowed clone.');
    }

    /**
     * 返回项目
     *
     * @param string $path
     * @return static
     */
    public static function singletons(?string $path = null)
    {
        if (static::$project !== null) {
            return static::$project;
        } else {
            return static::$project = new static($path);
        }
    }

    /**
     * 程序版本
     *
     * @return string
     */
    public function version()
    {
        return static::VERSION;
    }

    /**
     * 是否以扩展方式运行
     *
     * @return boolean
     */
    public function runWithExtension()
    {
        return extension_loaded('leevel');
    }

    /**
     * {@inheritdoc}
     */
    public function make($name, ?array $args = null)
    {
        $name = $this->getAlias($name);

        if (isset($this->deferredProviders[$name])) {
            $this->registerDeferredProvider($name);
        }

        return parent::make($name, $args);
    }

    /**
     * 设置项目路径
     *
     * @param string $path
     * @return void
     */
    public function setPath(string $path)
    {
        $this->path = $path;

        if (! is_writeable($this->pathRuntime())) {
            throw new RuntimeException(sprintf('Runtime path %s is not writeable.', $this->pathRuntime()));
        }
    }

    /**
     * 基础路径
     *
     * @return string
     */
    public function path()
    {
        return $this->path;
    }

    /**
     * 应用路径
     *
     * @return string
     */
    public function pathApplication()
    {
        return $this->applicationPath ?? $this->path . DIRECTORY_SEPARATOR . 'application';
    }

    /**
     * 设置应用路径
     *
     * @param string $path
     * @return $this
     */
    public function setPathApplication(string $path)
    {
        $this->applicationPath = $path;

        return $this;
    }

    /**
     * 设置公共路径
     *
     * @param string $path
     * @return $this
     */
    public function setPathCommon(string $path)
    {
        $this->commonPath = $path;

        return $this;
    }

    /**
     * 公共路径
     *
     * @return string
     */
    public function pathCommon()
    {
        return $this->commonPath ?? $this->path . DIRECTORY_SEPARATOR . 'common';
    }

    /**
     * 设置运行时路径
     *
     * @param string $path
     * @return $this
     */
    public function setPathRuntime(string $path)
    {
        $this->runtimePath = $path;

        return $this;
    }

    /**
     * 运行路径
     *
     * @return string
     */
    public function pathRuntime()
    {
        return $this->runtimePath ?? $this->path . DIRECTORY_SEPARATOR . 'runtime';
    }

    /**
     * 设置存储路径
     *
     * @param string $path
     * @return $this
     */
    public function setPathStorage(string $path)
    {
        $this->storagePath = $path;

        return $this;
    }

    /**
     * 附件路径
     *
     * @return string
     */
    public function pathStorage()
    {
        return $this->storagePath ?? $this->path . DIRECTORY_SEPARATOR . 'storage';
    }

    /**
     * 设置配置路径
     *
     * @param string $path
     * @return $this
     */
    public function setPathOption(string $path)
    {
        $this->optionPath = $path;

        return $this;
    }

    /**
     * 配置路径
     *
     * @return string
     */
    public function pathOption()
    {
        return $this->optionPath ?? $this->path . DIRECTORY_SEPARATOR . 'option';
    }

    /**
     * 设置语言包路径
     *
     * @param string $path
     * @return $this
     */
    public function setPathI18n(string $path)
    {
        $this->i18nPath = $path;

        return $this;
    }

    /**
     * 语言包路径
     *
     * @return string
     */
    public function pathI18n()
    {
        return $this->i18nPath ?? $this->path . DIRECTORY_SEPARATOR . 'i18n';
    }

    /**
     * 环境变量路径
     *
     * @return string
     */
    public function pathEnv()
    {
        return $this->envPath ?: $this->path;
    }

    /**
     * 设置环境变量路径
     *
     * @param string $path
     * @return $this
     */
    public function setPathEnv(string $path)
    {
        $this->envPath = $path;

        return $this;
    }

    /**
     * 设置环境变量文件
     *
     * @param string $file
     * @return $this
     */
    public function setEnvFile($file)
    {
        $this->envFile = $file;

        return $this;
    }

    /**
     * 取得环境变量文件
     *
     * @return string
     */
    public function envFile()
    {
        return $this->envFile ?: static::DEFAULT_ENV;
    }

    /**
     * 取得环境变量完整路径
     *
     * @return string
     */
    public function fullEnvPath()
    {
        return $this->pathEnv() . DIRECTORY_SEPARATOR . $this->envFile();
    }

    /**
     * 应用路径
     *
     * @param string $app
     * @return string
     */
    public function pathAnApplication(?string $app = null)
    {
        return $this->pathApplication() . '/' . strtolower($app ?: ($this->request->app() ?: 'App'));
    }

    /**
     * 取得应用缓存目录
     *
     * @param string $type
     * @return string
     */
    public function pathApplicationCache($type)
    {
        $types = [
            'file',
            'log',
            'table',
            'theme',
            'option',
            'i18n',
            'router',
            'console',
            'swoole'
        ];

        if (! in_array($type, $types)) {
            throw new Exception(sprintf('Application cache type %s not support', $type));
        }

        return $this->pathRuntime() . '/' . $type;
    }

    /**
     * 取得应用目录
     *
     * @param string $type
     * @return string
     */
    public function pathApplicationDir($type)
    {
        $types = [
            'theme',
            'i18n'
        ];

        if (! in_array($type, $types)) {
            throw new Exception(sprintf('Application dir type %s not support', $type));
        }

        return $this->pathAnApplication() . '/ui/' . $type;
    }

    /**
     * 返回语言包路径
     * 
     * @param string $i18n
     * @return string
     */
    public function pathCacheI18nFile(string $i18n)
    {
        return $this->pathRuntime() . '/cache/i18n/' . $i18n . '.php';
    }

    /**
     * 是否缓存语言包
     *
     * @param string $i18n
     * @return boolean
     */
    public function isCachedI18n(string $i18n): bool
    {
        return is_file($this->pathCacheI18nFile($i18n));
    }

    /**
     * 返回缓存路径
     * 
     * @return string
     */
    public function pathCacheOptionFile()
    {
        return $this->pathRuntime() . '/cache/option.php';
    }

    /**
     * 是否缓存配置
     *
     * @return boolean
     */
    public function isCachedOption(): bool
    {
        return is_file($this->pathCacheOptionFile());
    }

    /**
     * 取得 composer
     *
     * @return \Composer\Autoload\ClassLoader
     */
    public function composer()
    {
        return require $this->path . '/vendor/autoload.php';
    }

    /**
     * 获取命名空间路径
     *
     * @param string $namespaces
     * @return string|null
     */
    public function getPathByNamespace($namespaces)
    {
        $namespaces = explode('\\', $namespaces);

        $prefix = $this->composer()->getPrefixesPsr4();
        if (! isset($prefix[$namespaces[0] . '\\'])) {
            return null;
        }

        $namespaces[0] = $prefix[$namespaces[0] . '\\'][0];
        
        return implode('/', $namespaces);
    }

    /**
     * 批量获取命名空间路径
     *
     * @param array $namespaces
     * @return array
     */
    public function getPathByNamespaces(array $namespaces): array
    {
        $result = [];

        foreach ($namespaces as $item) {
            $result[$item] = $this->getPathByNamespace($item);
        }
        
        return $result;
    }

    /**
     * 是否开启 debug
     *
     * @return boolean
     */
    public function debug()
    {
        return $this->make('option')->get('debug');
    }

    /**
     * 是否为开发环境
     *
     * @return boolean
     */
    public function development()
    {
        return $this->environment() === 'development';
    }

    /**
     * 运行环境
     *
     * @return boolean
     */
    public function environment()
    {
        return $this->make('option')->get('environment');
    }

    /**
     * 是否为 API
     *
     * @return boolean
     */
    public function api()
    {
        return $this['request']->isAcceptJson();;
    }

    /**
     * 是否为 Console
     *
     * @return boolean
     */
    public function console()
    {
        return $this['request']->isCli();
    }

    /**
     * 创建服务提供者
     *
     * @param string $provider
     * @return \Leevel\Di\Provider
     */
    public function makeProvider($provider)
    {
        return new $provider($this);
    }

    /**
     * 执行 bootstrap
     *
     * @param \Leevel\Di\Provider $provider
     * @return void
     */
    public function callProviderBootstrap(Provider $provider)
    {
        if (! method_exists($provider, 'bootstrap')) {
            return;
        }

        $this->call([
            $provider,
            'bootstrap'
        ]);
    }

    /**
     * 初始化项目
     * 
     * @param array $bootstraps
     * @return void
     */
    public function bootstrap(array $bootstraps) {
        foreach ($bootstraps as $value) {
            (new $value)->handle($this);
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
     * 框架基础提供者 register
     *
     * @return $this
     */
    public function registerProviders()
    {
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
        foreach ($this->providerBootstraps as $item) {
            $this->callProviderBootstrap($item);
        }

        $this->isBootstrap = true;

        return $this;
    }

    /**
     * 注册服务提供者
     *
     * @param \Leevel\Di\Provider|string $provider
     * @return \Leevel\Di\Provider
     */
    public function register($provider)
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
     *
     * @return void
     */
    protected function registerBaseServices()
    {
        $this->instance('project', $this);

        $this->alias([
            'project' => [
                'Leevel\Bootstrap\Project',
                'Leevel\Di\IContainer',
                'Leevel\Kernel\IProject',
                'app'
            ],
            'request' => [
                'Leevel\Http\IRequest',
                'Leevel\Http\Request'
            ],
            'option' => [
                'Leevel\Option\IOption',
                'Leevel\Option\Option'
            ],
            'i18n' => [
                'Leevel\I18n\I18n',
                'Leevel\I18n\II18n'  
            ]
        ]);
    }

    /**
     * 注册基础服务提供者
     *
     * @return void
     */
    protected function registerBaseProvider()
    {
        $this->register(new EventProvider($this));

        $this->register(new LogProvider($this));

        $this->register(new RouterProvider($this));
    }

    /**
     * 注册延迟载入服务提供者
     *
     * @param string $provider
     * @return void
     */
    protected function registerDeferredProvider(string $provider)
    {
        if (! isset($this->deferredProviders[$provider])) {
            return;
        }

        $providerInstance = $this->register($this->deferredProviders[$provider]);

        $this->callProviderBootstrap($providerInstance);

        unset($this->deferredProviders[$provider]);
    }
}
