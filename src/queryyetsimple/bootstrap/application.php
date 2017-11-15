<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\bootstrap;

<<<queryphp
##########################################################
#   ____                          ______  _   _ ______   #
#  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
# |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
#  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
#       \__   | \___ |_|    \__  || |    | | | || |      #
#     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
#                          |___ /  Since 2010.10.03      #
##########################################################
queryphp;

use Exception;
use queryyetsimple\support\psr4;
use queryyetsimple\http\response;
use queryyetsimple\support\assert;
use queryyetsimple\filesystem\fso;
use queryyetsimple\support\debug\console;
use queryyetsimple\option\tool as option_tool;

/**
 * 应用程序对象
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2016.11.18
 * @version 1.0
 */
class application
{

    /**
     * 当前项目
     *
     * @var \queryyetsimple\bootstrap\project
     */
    protected $objProject;

    /**
     * 默认
     *
     * @var string
     */
    const INIT_APP = '~_~';

    /**
     * 项目配置
     *
     * @var array
     */
    protected $arrOption = [ ];

    /**
     * app 名字
     *
     * @var array
     */
    protected $strApp;

    /**
     * 执行事件流程
     *
     * @var array
     */
    protected $arrEvent = [
            'initialization',
            'loadBootstrap',
            'i18n',
            'console',
            'response'
    ];

    /**
     * 构造函数
     *
     * @param \queryyetsimple\bootstrap\project $objProject
     * @param string $sApp
     * @param array $arrOption
     * @return app
     */
    public function __construct(project $objProject, $sApp, $arrOption = [])
    {
        $this->objProject = $objProject;
        $this->strApp = $sApp;
        $this->arrOption = $arrOption;
    }

    /**
     * 执行应用
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->arrEvent as $strEvent) {
            $strEvent = $strEvent . 'Run';
            $this->{$strEvent} ();
        }

        return $this;
    }

    /**
     * 初始化应用
     *
     * @param string $sApp
     * @return $this
     */
    public function bootstrap($sApp = null)
    {
        if (! is_null($sApp)) {
            $this->strApp = $sApp;
        }
        $this->loadOption();
        $this->loadRouter();
        return $this;
    }

    /**
     * 注册命名空间
     *
     * @return $this
     */
    public function namespaces()
    {
        foreach ($this->objProject ['option'] ['~apps~'] as $strApp) {
            $this->objProject ['psr4']->import($strApp, $this->objProject->pathApplication() . '/' . $strApp);
        }

        foreach ($this->objProject ['option'] ['namespace'] as $strNamespace => $strPath) {
            $this->objProject ['psr4']->import($strNamespace, $strPath);
        }

        return $this;
    }

    /**
     * 初始化处理
     *
     * @return void
     */
    protected function initializationRun()
    {
        if ($this->objProject->development()) {
            error_reporting(E_ALL);
        } else {
            error_reporting(E_ERROR | E_PARSE | E_STRICT);
        }

        ini_set('default_charset', 'utf8');

        if (function_exists('date_default_timezone_set')) {
            date_default_timezone_set($this->objProject ['option'] ['time_zone']);
        }

        if (function_exists('gz_handler') && $this->objProject ['option'] ['start_gzip']) {
            ob_start('gz_handler');
        } else {
            ob_start();
        }

        if ($this->objProject->development()) {
            assert::open(true);
        }
    }

    /**
     * 载入 app 引导文件
     *
     * @return void
     */
    protected function loadBootstrapRun()
    {
        if (is_file(($strBootstrap = env('app_bootstrap') ?  : $this->objProject->pathApplication() . '/' . $this->strApp . '/bootstrap.php'))) {
            require $strBootstrap;
        }
    }

    /**
     * 初始化国际语言包设置
     *
     * @return void
     */
    protected function i18nRun()
    {
        if (! $this->objProject ['option'] ['i18n\on']) {
            return;
        }

        $sI18nSet = $this->objProject ['i18n']->parseContext();
        $this->objProject ['request']->setLangset($sI18nSet);
        if ($this->objProject ['option'] ['i18n\develop'] == $sI18nSet) {
            return;
        }

        $sCachePath = $this->getI18nCachePath($sI18nSet);

        if (! $this->objProject->development() && is_file($sCachePath)) {
            $this->objProject ['i18n']->addI18n($sI18nSet, ( array ) include $sCachePath);
        } else {
            $this->objProject ['i18n.load']->setI18n($sI18nSet)->setCachePath($sCachePath)->addDir($this->getI18nDir($sI18nSet));
            $this->objProject ['i18n']->addI18n($sI18nSet, $this->objProject ['i18n.load']->loadData());
        }
    }

    /**
     * 初始化命令行设置
     *
     * @return void
     */
    protected function consoleRun()
    {
        if (! $this->objProject->console()) {
            return;
        }

        $sCachePath = $this->getConsoleCachePath();

        if (! $this->objProject->development() && is_file($sCachePath)) {
            $this->objProject ['console.load']->setData(( array ) include $sCachePath);
        } else {
            $this->objProject ['console.load']->setCachePath($sCachePath);
        }
    }

    /**
     * 执行请求返回相应结果
     *
     * @return void
     */
    protected function responseRun()
    {
        $mixResponse = $this->objProject ['router']->doBind();
        if (! ($mixResponse instanceof response)) {
            $mixResponse = $this->objProject ['response']->make($mixResponse);
        }

        // 穿越中间件
        $this->objProject ['router']->throughMiddleware($this->objProject ['pipeline'], $this->objProject ['request'], [
                $mixResponse
        ]);

        // 调试
        if ($this->objProject->debug()) {
            $mixResponse->appendContent(console::trace());
        }

        // 输出响应
        $mixResponse->output();
    }

    /**
     * 分析配置文件
     *
     * @return void
     */
    protected function loadOption()
    {
        $sCachePath = $this->getOptionCachePath();

        if ($this->strApp == static::INIT_APP) {
            if (! is_file($sCachePath) || ! is_null($this->objProject ['option']->reset(( array ) include $sCachePath)) || $this->objProject->development()) {
                $this->cacheOption($sCachePath);
            }
        } else {
            if (! $this->objProject->development() && is_file($sCachePath)) {
                $this->objProject ['option']->reset(( array ) include $sCachePath);
            } else {
                $this->cacheOption($sCachePath);
            }
        }
    }

    /**
     * 分析路由
     *
     * @return void
     */
    protected function loadRouter()
    {
        if ($this->strApp != static::INIT_APP) {
            return;
        }

        $this->setRouterCachePath();

        if (! $this->objProject ['router']->checkExpired()) {
            return;
        }

        foreach ($this->objProject ['option'] ['app\~routers~'] as $strRouter) {
            if (is_array($arrFoo = include $strRouter)) {
                $this->objProject ['router']->importCache($arrFoo);
            }
        }
    }

    /**
     * 返回 i18n 目录
     *
     * @param string $sI18nSet
     * @return array
     */
    protected function getI18nDir()
    {
        $arrDir = [
                dirname(__DIR__) . '/bootstrap/i18n',
                $this->objProject->pathCommon() . '/ui/i18n',
                $this->objProject->pathApplicationDir('i18n')
        ];

        if ($this->objProject ['option'] ['i18n\extend']) {
            if (is_array($this->objProject ['option'] ['i18n\extend'])) {
                $arrDir = array_merge($arrDir, $this->objProject ['option'] ['i18n\extend']);
            } else {
                $arrDir [] = $this->objProject ['option'] ['i18n\extend'];
            }
        }

        return $arrDir;
    }

    /**
     * 返回 i18n 缓存路径
     *
     * @param string $sI18nSet
     * @return string
     */
    protected function getI18nCachePath($sI18nSet)
    {
        return $this->objProject->pathApplicationCache('i18n') . '/' . $sI18nSet . '/default.php';
    }

    /**
     * 返回 console 缓存路径
     *
     * @return string
     */
    protected function getConsoleCachePath()
    {
        return $this->objProject->pathApplicationCache('console') . '/default.php';
    }

    /**
     * 返回配置目录
     *
     * @return array
     */
    protected function getOptionDir()
    {
        $arrDir = [
                dirname(__DIR__) . '/bootstrap/option'
        ];
        if (is_dir($this->objProject->pathCommon() . '/ui/option')) {
            $arrDir [] = $this->objProject->pathCommon() . '/ui/option';
        }
        $arrDir [] = $this->objProject->pathApplicationDir('option');
        return $arrDir;
    }

    /**
     * 返回配置缓存路径
     *
     * @return string
     */
    protected function getOptionCachePath()
    {
        return $this->objProject->pathApplicationCache('option') . '/' . $this->strApp . '.php';
    }

    /**
     * 设置路由缓存路径
     *
     * @return void
     */
    protected function setRouterCachePath()
    {
        $this->objProject ['router']->cachePath($this->objProject->pathApplicationCache('router') . '/router.php')
            ->development($this->objProject->development());
    }

    /**
     * 缓存配置
     *
     * @param string $sCachePath
     * @return void
     */
    protected function cacheOption($sCachePath)
    {
        $this->objProject ['option']->reset(option_tool::saveToCache($this->getOptionDir(), $sCachePath, [
                'app' => [
                        '~apps~' => fso::lists($this->objProject->pathApplication())
                ],
                'env' => $_ENV
        ], $this->strApp == static::INIT_APP));
    }
}
