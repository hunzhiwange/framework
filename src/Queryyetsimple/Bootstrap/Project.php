<?php
/*
 * This file is part of the ************************ package.
 * ##########################################################
 * #   ____                          ______  _   _ ______   #
 * #  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
 * # |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
 * #  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
 * #       \__   | \___ |_|    \__  || |    | | | || |      #
 * #     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
 * #                          |___ /  Since 2010.10.03      #
 * ##########################################################
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Queryyetsimple\Bootstrap;

use Exception;
use Dotenv\Dotenv;
use RuntimeException;
use Composer\Autoload\ClassLoader;
use Queryyetsimple\{
    Psr4\Psr4,
    Di\Provider,
    Di\Container,
    Support\Facade,
    Filesystem\Fso,
    Bootstrap\Console\Provider\Register as ConsoleProvider
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
     * @var Queryyetsimple\Bootstrap\Project
     */
    protected static $objProject;

    /**
     * 项目配置
     *
     * @var array
     */
    protected $arrOption = [];

    /**
     * 项目框架路径
     *
     * @var string
     */
    protected $strFrameworkPath;

    /**
     * 项目基础路径
     *
     * @var string
     */
    protected $strPath;

    /**
     * 项目 APP 基本配置
     *
     * @var string
     */
    protected $arrAppOption = [];

    /**
     * 延迟载入服务提供者
     *
     * @var array
     */
    protected $arrDeferredProviders = [];

    /**
     * 服务提供者 bootstrap
     *
     * @var array
     */
    protected $arrProviderBootstrap = [];

    /**
     * 构造函数
     * 受保护的禁止外部通过 new 实例化，只能通过 singletons 生成单一实例
     *
     * @param \Composer\Autoload\ClassLoader $objComposer
     * @param array $arrOption
     * @return void
     */
    protected function __construct(ClassLoader $objComposer, $arrOption = [])
    {
        // 项目基础配置
        $this->setOption($arrOption)->

        // 初始化项目路径
        setPath()->

        // 注册别名
        registerAlias()->

        // 注册 psr4
        registerPsr4($objComposer)->

        // 载入 app 配置
        loadApp()->

        // 注册框架核心提供者
        registerMvcProvider()->

        // 注册基础提供者 register
        registerBaseProvider()->

        // 注册基础提供者 bootstrap
        registerBaseProviderBootstrap();
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
     * 执行项目
     *
     * @return $this
     */
    public function run()
    {
        (new bootstrap($this))->run();
        return $this;
    }

    /**
     * 返回项目
     *
     * @param \Composer\Autoload\ClassLoader|null $objComposer
     * @param array $arrOption
     * @param boolean $booRun
     * @return $this
     */
    public static function singletons($objComposer = null, $arrOption = [], $booRun = true)
    {
        if (static::$objProject !== null) {
            return static::$objProject;
        } else {
            static::$objProject = new static($objComposer, $arrOption);
            if ($booRun === true) {
                static::$objProject->run();
            }
            return static::$objProject;
        }
    }

    /**
     * 程序版本
     *
     * @return number
     */
    public function version()
    {
        return static::VERSION;
    }

    /**
     * {@inheritdoc}
     */
    public function make($strFactoryName, ?array $arrArgs = null)
    {
        $strFactoryName = $this->getAlias($strFactoryName);

        if (isset($this->arrDeferredProviders[$strFactoryName])) {
            $this->registerDeferredProvider($strFactoryName);
        }

        return parent::make($strFactoryName, $arrArgs);
    }

    /**
     * 基础路径
     *
     * @return string
     */
    public function path()
    {
        return $this->strPath;
    }

    /**
     * 框架路径
     *
     * @return string
     */
    public function pathFramework()
    {
        return $this->strFrameworkPath;
    }

    /**
     * 应用路径
     *
     * @return string
     */
    public function pathApplication()
    {
        return $this->arrOption['path_application'] ?? $this->strPath . DIRECTORY_SEPARATOR . 'application';
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
            'trace',
            'url'
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
     * 公共路径
     *
     * @return string
     */
    public function pathCommon()
    {
        return $this->arrOption['path_common'] ?? $this->strPath . DIRECTORY_SEPARATOR . 'common';
    }

    /**
     * 运行路径
     *
     * @return string
     */
    public function pathRuntime()
    {
        return $this->arrOption['path_runtime'] ?? $this->strPath . DIRECTORY_SEPARATOR . '~@~';
    }

    /**
     * 资源路径
     *
     * @return string
     */
    public function pathPublic()
    {
        return $this->arrOption['path_public'] ?? $this->strPath . DIRECTORY_SEPARATOR . 'public';
    }

    /**
     * 附件路径
     *
     * @return string
     */
    public function pathStorage()
    {
        return $this->arrOption['path_storage'] ?? $this->strPath . DIRECTORY_SEPARATOR . 'storage';
    }

    /**
     * 应用路径
     *
     * @return string
     */
    public function pathApplicationCurrent()
    {
        return $this->pathApplication() . '/' . $this['app_name'];
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
            'swoole',
            'aop'
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
            'option',
            'theme',
            'i18n'
        ];
        if (! in_array($strType, $arrType)) {
            throw new Exception(sprintf('Application dir type %s not support', $strType));
        }
        return $this->pathApplicationCurrent() . '/ui/' . $strType;
    }

    /**
     * 是否开启 debug
     *
     * @return boolean
     */
    public function debug()
    {
        return $this->arrAppOption['debug'] ?? false;
    }

    /**
     * 是否为开发环境
     *
     * @return string
     */
    public function development()
    {
        return $this->arrAppOption['environment'] == 'development';
    }

    /**
     * 运行环境
     *
     * @return boolean
     */
    public function environment()
    {
        return $this->arrAppOption['environment'];
    }

    /**
     * 是否为 API
     *
     * @return boolean
     */
    public function api()
    {
        return $this->arrAppOption['default_response'] == 'api';
    }

    /**
     * 是否为 Console
     *
     * @return boolean
     */
    public function console()
    {
        return env('app_name') == 'frameworkconsole';
    }

    /**
     * public url
     *
     * @return string
     */
    public function urlPublic()
    {
        return $this->url_public;
    }

    /**
     * root url
     *
     * @return string
     */
    public function urlRoot()
    {
        return $this->url_root;
    }

    /**
     * enter url
     *
     * @return string
     */
    public function urlEnter()
    {
        return $this->url_enter;
    }

    /**
     * 创建服务提供者
     *
     * @param string $strProvider
     * @return \queryyetsimple\Di\Provider
     */
    public function makeProvider($strProvider)
    {
        return new $strProvider($this);
    }

    /**
     * 执行 bootstrap
     *
     * @param \queryyetsimple\Di\Provider $objProvider
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
     * 设置项目基础配置
     *
     * @param array $arrOption
     * @return $this
     */
    protected function setOption($arrOption)
    {
        $this->arrOption = $arrOption;
        return $this;
    }

    /**
     * 注册 psr4
     *
     * @param \Composer\Autoload\ClassLoader $objComposer
     * @return $this
     */
    protected function registerPsr4(ClassLoader $objComposer)
    {
        $this->instance('psr4', new Psr4($objComposer, dirname(__DIR__) . '/bootstrap/sandbox', 'queryyetsimple', 'qys'));
        $this->alias('psr4', Psr4::class);

        // 优先载入 aop autoload，恢复 composer autoload
        $objComposer->unregister();
        spl_autoload_register(array($this, 'loadAopClass'));
        $objComposer->register();

        $this->registerAop();
        $this->doMakeAop();

        $this->instance('composer', $objComposer);
        $this->alias('composer', ClassLoader::class);

        Facade::setContainer($this);

        spl_autoload_register([
            $this['psr4'],
            'autoload'
        ]);

        return $this;
    }

    protected $aops = [];

    CONST AOP_DEFORE = 1;
    CONST AOP_AFTER = 2;

    public function registerAop() {
        // //aop_add_before('testClass1->testBeforAdd1()', $testpoint12);
        // $this->aops = [
        //     'home\app\controller\hello' => [
        //         'testBeforAdd1' => [
        //             self::AOP_DEFORE => function() {
        //                 echo 'before call';
        //             }
        //         ]
        //     ]
        // ];
    }

    public function getAops() {
        return $this->aops;
    }

    protected function doMakeAop() {
        // $aop = new \queryyetsimple\Support\aop($this);
        // foreach ($this->aops as $aopclass => $methods) {
        //     //echo $aopclass;
        //    //echo $this['psr4']->file($aopclass);;
        //    $file = $this['psr4']->file($aopclass);
        //    $aop->parse($aopclass,$file,$methods);
        // }
    }

    public function loadAopClass($class) {
        // //require_once $this['psr4']->file('queryyetsimple\Support\aop');
        // // $aop = new \queryyetsimple\Support\aop();
        // // //echo $class;
        // // //echo '<br/>';
        // if(isset($this->aops[$class])) {
        //    // echo $class;
        //     $file = $this->pathApplicationCache('aop') . '/' . str_replace('\\', '/', $class) . '.php';
        //     var_dump(is_file($file));
        //     include $file;
        //     //$file = ;
        //     //echo $file;
        //     //$file = $this['psr4']->file($class);

        //    // $aop->parse($file);

        //    // echo $class;
        //     //foreach() {

        //    // }
        // }
    }

    /**
     * 载入 APP 配置
     *
     * @return $this
     */
    protected function loadApp()
    {
        if (($strCache = $this->pathApplicationCache('option') . '/' . Application::INIT_APP . '.php') && is_file($strCache)) {
            if ($this->checkEnv($strCache)) {
                Fso::deleteDirectory(dirname($strCache), true);
                $this->loadAppOption();
            } else {
                $this->loadAppOption($strCache);
            }
        } else {
            $this->loadAppOption();
        }
        return $this;
    }

    /**
     * 框架基础提供者 register
     *
     * @return $this
     */
    protected function registerBaseProvider()
    {
        if (empty($this->arrAppOption['provider'])) {
            return $this;
        }

        $booCache = false;

        $strCachePath = $this->defferProviderCachePath();
        if (! $this->development() && is_file($strCachePath)) {
            list($this->arrDeferredProviders, $arrDeferredAlias) = include $strCachePath;
            $booCache = true;
        } else {
            $arrDeferredAlias = [];
        }

        foreach ($this->arrAppOption['provider'] as $strProvider) {
            $strProvider .= '\Provider\Register';

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
                    $this->arrDeferredProviders[$mixKey] = $strProvider;
                }
                $this->alias($arrProviders);
                $arrDeferredAlias[$strProvider] = $arrProviders;
                continue;
            }

            $objProvider = $this->makeProvider($strProvider);
            $objProvider->register();

            if (method_exists($objProvider, 'bootstrap')) {
                $this->arrProviderBootstrap[] = $objProvider;
            }
        }

        if (! is_dir(dirname($strCachePath))) {
            mkdir(dirname($strCachePath), 0777, true);
        }

        if ($this->development() || ! is_file($strCachePath)) {
            file_put_contents($strCachePath, '<?' . 'php /* ' . date('Y-m-d H:i:s') . ' */ ?' . '>' . PHP_EOL . '<?' . 'php return ' . var_export([
                $this->arrDeferredProviders,
                $arrDeferredAlias
            ], true) . '; ?' . '>');
        }

        return $this;
    }

    /**
     * 框架基础提供者 bootstrap
     *
     * @return $this
     */
    protected function registerBaseProviderBootstrap()
    {
        foreach ($this->arrProviderBootstrap as $obj) {
            $this->callProviderBootstrap($obj);
        }
        return $this;
    }

    /**
     * 框架 MVC 基础提供者
     *
     * @return $this
     */
    protected function registerMvcProvider()
    {
        // 注册本身
        $this->instance('project', $this);

        // 注册 app
        $this->singleton(Application::class, function (project $objProject, $sApp, $arrOption = [])
        {
            return new Application($objProject, $sApp, $arrOption);
        });

        // 注册 console
        if ($this->console()) {
            $this->makeProvider(ConsoleProvider::class)->register();
        }

        return $this;
    }

    /**
     * 注册别名
     *
     * @return void
     */
    protected function registerAlias()
    {
        $this->alias([
            'project' => [
                'Queryyetsimple\Bootstrap\Project',
                'Queryyetsimple\Di\IContainer',
                'Queryyetsimple\Bootstrap\IProject',
                'app'
            ]
        ]);
        return $this;
    }

    /**
     * 初始化项目路径
     *
     * @param string $strPath
     * @return $this
     */
    protected function setPath()
    {
        // 框架路径
        $this->strFrameworkPath = dirname(__DIR__);

        // 基础路径
        $this->strPath = dirname(__DIR__, 6);

        // 注册路径
        $this->registerPath();

        // 注册 url
        $this->registerUrl();

        return $this;
    }

    /**
     * 注册路径到容器
     *
     * @return void
     */
    protected function registerPath()
    {
        // 基础路径
        $this->instance('path', $this->path());

        // 其它路径
        foreach ([
            'application',
            'common',
            'runtime',
            'public'
        ] as $sKey => $sPath) {
            $this->instance('path_' . $sPath, $this->{'path' . ucwords($sPath)}());
        }
    }

    /**
     * 注册 url 到容器
     *
     * @return void
     */
    protected function registerUrl()
    {
        foreach ([
            'enter',
            'root',
            'public'
        ] as $sKey => $sUrl) {
            $sUrl = 'url_' . $sUrl;
            $this->instance($sUrl, $this->arrOption[$sUrl] ?? '');
        }
    }

    /**
     * 注册延迟载入服务提供者
     *
     * @param string $strProvider
     * @return void
     */
    protected function registerDeferredProvider($strProvider)
    {
        if (! isset($this->arrDeferredProviders[$strProvider])) {
            return;
        }

        $objProvider = $this->makeProvider($this->arrDeferredProviders[$strProvider]);
        $objProvider->register();

        if (method_exists($objProvider, 'bootstrap')) {
            $this->callProviderBootstrap($objProvider);
        }

        unset($this->arrDeferredProviders[$strProvider]);
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

    /**
     * 载入 APP 配置
     *
     * @param string $strCache
     * @return void
     */
    protected function loadAppOption($strCache = null)
    {
        if ($strCache && is_array($arrOption = include $strCache)) {
            $this->arrAppOption = $arrOption['app'];
            $this->setEnvironmentVariables($arrOption['env']);
        } else {
            $this->setEnvironmentVariables();
            $this->arrAppOption = ( array ) include $this->pathCommon() . '/ui/option/app.php';
        }
    }

    /**
     * 验证环境变量是否变动
     *
     * @param string $strCache
     * @return void
     */
    protected function checkEnv($strCache)
    {
        return filemtime($this->path() . '/.env') > filemtime($strCache);
    }

    /**
     * 设置环境变量
     *
     * @param array $arrEnv
     * @return void
     */
    protected function setEnvironmentVariables($arrEnv = [])
    {
        if ($arrEnv) {
            foreach ($arrEnv as $strName => $strValue) {
                $this->setEnvironmentVariable($strName, $strValue);
            }
        } else {
            $objDotenv = new Dotenv($this->path());
            $objDotenv->load();
        }
    }

    /**
     * 设置单个环境变量
     *
     * @param string $strName
     * @param string|null $mixValue
     * @return void
     */
    protected function setEnvironmentVariable($strName, $mixValue = null)
    {
        if (is_bool($mixValue)) {
            putenv($strName . '=' . ($mixValue ? '(true)' : '(false)'));
        } elseif (is_null($mixValue)) {
            putenv($strName . '(null)');
        } else {
            putenv($strName . '=' . $mixValue);
        }
        $_ENV [$strName] = $mixValue;
        $_SERVER [$strName] = $mixValue;
    }
}
