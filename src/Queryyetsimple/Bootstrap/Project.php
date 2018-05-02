<?php
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
    Psr4\Psr4,
    Di\Provider,
    Di\Container,
    Filesystem\Fso,
    Bootstrap\Console\Provider\Register as ConsoleProvider,
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
     * @var \Leevel\Bootstrap\Project
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
     * 服务提供者 bootstrap
     *
     * @var array
     */
    protected $providerBootstraps = [];

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
     * @return static
     */
    public static function singletons()
    {
        if (static::$project !== null) {
            return static::$project;
        } else {
            return static::$project = new static($arrOption);
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
        // 基础路径
        $this->path = $path;

        // 验证缓存路径
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
     * 系统错误、异常、调试和跳转模板路径
     *
     * @param string $type
     * @return string
     */
    public function pathSystem($type)
    {
        $types = [
            'error',
            'exception',
            'trace'
        ];

        if (! in_array($type, $types)) {
            throw new Exception(sprintf('System type %s not support', $type));
        }

        $path = $this->arrAppOption['system_path'] ?? '';
        $file = $this->arrAppOption['system_template'][$type] ?? $type . '.php';

        if ( !is_dir($path)) {
            $path = $this->path() . '/' . $path;
        }

        return $path . '/' . $file;
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
        return $this->pathRuntime ?? $this->path . DIRECTORY_SEPARATOR . 'runtime';
    }

    /**
     * 附件路径
     *
     * @return string
     */
    public function pathStorage()
    {
        return $this->arrOption['path_storage'] ?? $this->path . DIRECTORY_SEPARATOR . 'storage';
    }

    /**
     * 配置路径
     *
     * @return string
     */
    public function pathOption()
    {
        return $this->arrOption['path_option'] ?? $this->path . DIRECTORY_SEPARATOR . 'option';
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
     * @return string
     */
    public function pathApplicationCurrent()
    {
        return $this->pathApplication() . '/' . strtolower($this->request->app());
    }

    /**
     * 取得应用缓存目录
     *
     * @param string $strType
     * @return string
     */
    public function pathApplicationCache($strType)
    {
        $arrType = [
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

        if (! in_array($strType, $arrType)) {
            throw new Exception(sprintf('Application cache type %s not support', $strType));
        }

        return $this->pathRuntime() . '/' . $strType;
    }

    /**
     * 取得应用目录
     *
     * @param string $strType
     * @return string
     */
    public function pathApplicationDir($strType)
    {
        $arrType = [
            'theme',
            'i18n'
        ];

        if (! in_array($strType, $arrType)) {
            throw new Exception(sprintf('Application dir type %s not support', $strType));
        }

        return $this->pathApplicationCurrent() . '/ui/' . $strType;
    }

    /**
     * 返回缓存路径
     * 
     * @return 返回缓存路径
     */
    public function pathCacheOptionFile() {
        return $this->pathRuntime() . '/cache/option.php';
    }

    /**
     * 是否缓存配置
     *
     * @return boolean
     */
    public function isCachedOption() {
        return is_file($this->pathCacheOptionFile());
    }

    /**
     * 取得 composer
     *
     * @return \Composer\Autoload\ClassLoader
     */
    public function composer() {
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
     * @return string
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
     * @param string $strProvider
     * @return \Leevel\Di\Provider
     */
    public function makeProvider($strProvider)
    {
        return new $strProvider($this);
    }

    /**
     * 执行 bootstrap
     *
     * @param \Leevel\Di\Provider $objProvider
     * @return void
     */
    public function callProviderBootstrap(Provider $objProvider)
    {
        if (! method_exists($objProvider, 'bootstrap')) {
            return;
        }

        $this->call([
            $objProvider,
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
     * 框架基础提供者 register
     *
     * @return $this
     */
    public function registerProviders()
    {
        $booCache = false;

        $strCachePath = $this->defferProviderCachePath();
        if (! $this->development() && is_file($strCachePath)) {
            list($this->deferredProviders, $arrDeferredAlias) = include $strCachePath;
            $booCache = true;
        } else {
            $arrDeferredAlias = [];
        }

        foreach ($this->make('option')->get('provider', []) as $strProvider) {
            if ($booCache === true && isset($arrDeferredAlias[$strProvider])) {
                $this->alias($arrDeferredAlias[$strProvider]);
                continue;
            }

            if (! class_exists($strProvider)) {
                continue;
            }

            if ($strProvider::isDeferred()) {
                $arrProviders = $strProvider::providers();
                foreach ($arrProviders as $mixKey => $mixAlias) {
                    if (is_int($mixKey)) {
                        $mixKey = $mixAlias;
                    }
                    $this->deferredProviders[$mixKey] = $strProvider;
                }
                $this->alias($arrProviders);
                $arrDeferredAlias[$strProvider] = $arrProviders;
                continue;
            }

            $objProvider = $this->makeProvider($strProvider);
            $objProvider->register();

            if (method_exists($objProvider, 'bootstrap')) {
                $this->providerBootstraps[] = $objProvider;
            }
        }

        $cacheDir = dirname($strCachePath);
        if (! is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }

        if ($this->development() || ! is_file($strCachePath)) {
            file_put_contents($strCachePath, '<?' . 'php /* ' . date('Y-m-d H:i:s') . ' */ ?' . '>' . 
                PHP_EOL . '<?' . 'php return ' . var_export([
                $this->deferredProviders,
                $arrDeferredAlias
            ], true) . '; ?' . '>');

            chmod($strCachePath, 0777);
        }

        return $this;
    }

    /**
     * 执行框架基础提供者 bootstrap
     *
     * @return $this
     */
    public function bootstrapProviders()
    {
        foreach ($this->providerBootstraps as $obj) {
            $this->callProviderBootstrap($obj);
        }

        return $this;
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
                'Leevel\Bootstrap\IProject',
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

        $this->alias($provider::providers());

        // if ($this->booted) {
        //    $this->bootProvider($provider);
        // }

        return $provider;
    }

    /**
     * 注册延迟载入服务提供者
     *
     * @param string $strProvider
     * @return void
     */
    protected function registerDeferredProvider($strProvider)
    {
        if (! isset($this->deferredProviders[$strProvider])) {
            return;
        }

        $objProvider = $this->makeProvider($this->deferredProviders[$strProvider]);
        $objProvider->register();

        if (method_exists($objProvider, 'bootstrap')) {
            $this->callProviderBootstrap($objProvider);
        }

        unset($this->deferredProviders[$strProvider]);
    }

    /**
     * 返回延迟服务提供者缓存路径
     *
     * @return string
     */
    protected function defferProviderCachePath()
    {
        return $this->pathRuntime() . '/provider/deffer.php';
    }
}
