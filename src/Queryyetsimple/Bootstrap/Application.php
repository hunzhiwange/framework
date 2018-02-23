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
use Queryyetsimple\{
    Support\Psr4,
    Http\Response, 
    Support\Debug\Console
};

/**
 * 应用程序对象
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2016.11.18
 * @version 1.0
 */
class Application
{

    /**
     * 当前项目
     *
     * @var \Queryyetsimple\Bootstrap\Project
     */
    protected $objProject;

    /**
     * 默认
     *
     * @var string
     */
    const INIT_APP = '~_~';
    
    /**
     * app 名字
     *
     * @var array
     */
    protected $strApp;

    /**
     * 初始执行事件流程
     *
     * @var array
     */
    protected $arrEvent = [
        'loadBootstrap',
        'i18n',
        'console'
    ];

    /**
     * 载入过的应用
     *
     * @var array
     */
    protected $loadedApp = [];

    /**
     * 构造函数
     *
     * @param \Queryyetsimple\Bootstrap\Project $objProject
     * @param string $sApp
     * @return void
     */
    public function __construct(project $objProject, $sApp)
    {
        $this->objProject = $objProject;
        $this->strApp = $sApp;
    }

    /**
     * 执行请求返回相应结果
     *
     * @return $this
     */
    public function run()
    {
        $mixResponse = $this->objProject['router']->doBind();
        if (! ($mixResponse instanceof Response)) {
            $mixResponse = $this->objProject['response']->make($mixResponse);
        }

        // 穿越中间件
        $this->objProject['router']->throughMiddleware($this->objProject['request'], [
            $mixResponse
        ]);

        // 调试
        if ($this->objProject->debug()) {
            Console::trace($this->objProject->pathSystem('trace'), $this->objProject['log']->get());
        }

        // 输出响应
        $mixResponse->output();

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

        if (in_array($this->strApp, $this->loadedApp)) {
            return $this;
        }

        $this->loadOption();

        if ($this->isInitApp()) {
            $this->initialization();
            $this->loadRouter();
        } else {
            foreach ($this->arrEvent as $strEvent) {
                $strEvent = $strEvent . 'Bootstrap';
                $this->{$strEvent}();
            }
        }

        $this->loadedApp[] = $this->strApp;
        
        return $this;
    }

    /**
     * 初始化处理
     *
     * @return void
     */
    protected function initialization()
    {
        if (function_exists('date_default_timezone_set')) {
            date_default_timezone_set($this->objProject['option']['time_zone']);
        }
        
        if(PHP_SAPI == 'cli') {
            return;
        }

        if (function_exists('gz_handler') && $this->objProject['option']['start_gzip']) {
            ob_start('gz_handler');
        } else {
            ob_start();
        }
    }

    /**
     * 载入 app 引导文件
     *
     * @return void
     */
    protected function loadBootstrapBootstrap()
    {
        if (is_file(($strBootstrap = env('app_bootstrap') ?  : $this->objProject->pathApplication() . '/' . $this->strApp . '/bootstrap.php'))) {
            require_once $strBootstrap;
        }
    }

    /**
     * 初始化国际语言包设置
     *
     * @return void
     */
    protected function i18nBootstrap()
    {
        if (! $this->objProject['option']['i18n\on']) {
            return;
        }

        if ($this->objProject['option']['i18n\develop'] == $this->objProject['option']['i18n\default']) {
            return;
        }

        $sI18nSet = $this->objProject['i18n']->getI18n();
        $this->objProject['request']->setLanguage($sI18nSet);

        $sCachePath = $this->getI18nCachePath($sI18nSet);

        if (! $this->objProject->development() && is_file($sCachePath)) {
            $this->objProject['i18n']->addText($sI18nSet, ( array ) include $sCachePath);
        } else {
            $this->objProject['i18n.load']->

            setI18n($sI18nSet)->

            setCachePath($sCachePath)->

            addDir($this->getI18nDir($sI18nSet));
            
            $this->objProject['i18n']->addText($sI18nSet, $this->objProject['i18n.load']->loadData());
        }
    }

    /**
     * 初始化命令行设置
     *
     * @return void
     */
    protected function consoleBootstrap()
    {
        if (! $this->objProject->console()) {
            return;
        }

        $sCachePath = $this->getConsoleCachePath();

        if (! $this->objProject->development() && is_file($sCachePath)) {
            $this->objProject['console.load']->setData(( array ) include $sCachePath);
        } else {
            $this->objProject['console.load']->setCachePath($sCachePath);
        }
    }

    /**
     * 分析配置文件
     *
     * @return void
     */
    protected function loadOption()
    {
        $sCachePath = $this->getOptionCachePath();

        if ($this->isInitApp()) {
            if (! is_file($sCachePath) || !$this->objProject['option']->reset(( array ) include $sCachePath) || $this->objProject->development()) {
                $this->cacheOption($sCachePath);
            }
        } else {
            if (! $this->objProject->development() && is_file($sCachePath)) {
                $this->objProject['option']->reset(( array ) include $sCachePath);
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
        $this->setRouterCachePath();

        if (! $this->objProject['router']->checkExpired()) {
            return;
        }

        foreach ($this->objProject->routers() as $strRouter) {
            if (is_array($arrFoo = include $strRouter)) {
                $this->objProject['router']->importCache($arrFoo);
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
            $this->objProject->pathCommon() . '/ui/i18n',
            $this->objProject->pathApplicationDir('i18n')
        ];

        if ($this->objProject['option']['i18n\extend']) {
            if (is_array($this->objProject['option']['i18n\extend'])) {
                $arrDir = array_merge($arrDir, $this->objProject['option']['i18n\extend']);
            } else {
                $arrDir[] = $this->objProject['option']['i18n\extend'];
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
        $arrDir = [];
        if (is_dir($this->objProject->pathCommon() . '/ui/option')) {
            $arrDir[] = $this->objProject->pathCommon() . '/ui/option';
        }

        if (! $this->isInitApp()) {
            $arrDir[] = $this->objProject->pathApplicationDir('option');
        }

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
        $router = $this->objProject['router'];

        $this->objProject['router']->

        cachePath($this->objProject->pathApplicationCache('router') . '/router.php')->

        development($this->objProject->development());
    }

    /**
     * 缓存配置
     *
     * @param string $sCachePath
     * @return void
     */
    protected function cacheOption($sCachePath)
    {
        $this->objProject['option']->reset(
            $this->objProject['option.load']->

            setCachePath($sCachePath)->

            setDir($this->getOptionDir())->

            loadData($this->extendOption())
        );
    }

    /**
     * 额外的系统缓存配置
     *
     * @return array
     */
    protected function extendOption()
    {
        return [
            'app' => [
                '~apps~' => $this->objProject->apps(),
                '~envs~' => $this->objProject->envs(),
                '~routers~' => $this->objProject->routers()
            ]
        ];
    }

    /**
     * 是否为初始化应用
     *
     * @return boolean
     */
    protected function isInitApp()
    {
        return $this->strApp === static::INIT_APP;
    }
}
