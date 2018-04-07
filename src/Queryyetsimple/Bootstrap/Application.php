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
use Leevel\{
    Support\Psr4,
    Http\IResponse,
    Http\ApiResponse,
    Http\JsonResponse,
    Http\RedirectResponse,
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
     * @var \Leevel\Bootstrap\Project
     */
    protected $project;

    /**
     * 默认
     *
     * @var string
     */
    const INIT_APP = '_init';
    
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
     * @param \Leevel\Bootstrap\Project $project
     * @param string $sApp
     * @return void
     */
    public function __construct(project $project, $sApp)
    {
        $this->project = $project;
        $this->strApp = $sApp;
    }

    /**
     * 执行请求返回相应结果
     *
     * @return void
     */
    public function run()
    {
        $response = $this->project['router']->doBind();
        if (! ($response instanceof IResponse)) {
            $response = $this->project['response']->make($response);
        }

        // 穿越中间件
        $this->project['router']->throughMiddleware($this->project['request'], [
            $response
        ]);

        // 调试
        if ($this->project->debug()) {
            if (($response instanceof ApiResponse || $response instanceof JsonResponse || $response->isJson()) && 
                is_array(($data = $response->getData()))) {
                $data['_TRACE'] = Console::jsonTrace($this->project['log']->get());
                $response->setData($data);
            } elseif(! ($response instanceof RedirectResponse)) {
                $data = Console::trace($this->project->pathSystem('trace'), $this->project['log']->get());
                $response->appendContent($data);
            }
        }

        // 输出响应
        $response->send();
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
            date_default_timezone_set($this->project['option']['time_zone']);
        }
        
        if(PHP_SAPI == 'cli') {
            return;
        }

        if (function_exists('gz_handler') && $this->project['option']['start_gzip']) {
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
        if (is_file(($strBootstrap = env('app_bootstrap') ?  : $this->project->pathApplication() . '/' . $this->strApp . '/bootstrap.php'))) {
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
        if (! $this->project['option']['i18n\on']) {
            return;
        }

        if ($this->project['option']['i18n\develop'] == $this->project['option']['i18n\default']) {
            return;
        }

        $sI18nSet = $this->project['i18n']->getI18n();
        $this->project['request']->setLanguage($sI18nSet);

        $sCachePath = $this->getI18nCachePath($sI18nSet);

        if (! $this->project->development() && is_file($sCachePath)) {
            $this->project['i18n']->addText($sI18nSet, ( array ) include $sCachePath);
        } else {
            $this->project['i18n.load']->

            setI18n($sI18nSet)->

            setCachePath($sCachePath)->

            addDir($this->getI18nDir($sI18nSet));
            
            $this->project['i18n']->addText($sI18nSet, $this->project['i18n.load']->loadData());
        }
    }

    /**
     * 初始化命令行设置
     *
     * @return void
     */
    protected function consoleBootstrap()
    {
        if (! $this->project->console()) {
            return;
        }

        $sCachePath = $this->getConsoleCachePath();

        if (! $this->project->development() && is_file($sCachePath)) {
            $this->project['console.load']->setData(( array ) include $sCachePath);
        } else {
            $this->project['console.load']->setCachePath($sCachePath);
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
            if (! is_file($sCachePath) || !$this->project['option']->reset(( array ) include $sCachePath) || $this->project->development()) {
                $this->cacheOption($sCachePath);
            }
        } else {
            if (! $this->project->development() && is_file($sCachePath)) {
                $this->project['option']->reset(( array ) include $sCachePath);
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

        if (! $this->project['router']->checkExpired()) {
            return;
        }

        foreach ($this->project->routers() as $strRouter) {
            if (is_array($arrFoo = include $strRouter)) {
                $this->project['router']->importCache($arrFoo);
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
            $this->project->pathCommon() . '/ui/i18n',
            $this->project->pathApplicationDir('i18n')
        ];

        if ($this->project['option']['i18n\extend']) {
            if (is_array($this->project['option']['i18n\extend'])) {
                $arrDir = array_merge($arrDir, $this->project['option']['i18n\extend']);
            } else {
                $arrDir[] = $this->project['option']['i18n\extend'];
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
        return $this->project->pathApplicationCache('i18n') . '/' . $sI18nSet . '/default.php';
    }

    /**
     * 返回 console 缓存路径
     *
     * @return string
     */
    protected function getConsoleCachePath()
    {
        return $this->project->pathApplicationCache('console') . '/default.php';
    }

    /**
     * 返回配置目录
     *
     * @return array
     */
    protected function getOptionDir()
    {
        $arrDir = [];
        if (is_dir($this->project->pathCommon() . '/ui/option')) {
            $arrDir[] = $this->project->pathCommon() . '/ui/option';
        }

        if (! $this->isInitApp()) {
            $arrDir[] = $this->project->pathApplicationDir('option');
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
        return $this->project->pathApplicationCache('option') . '/' . $this->strApp . '.php';
    }

    /**
     * 设置路由缓存路径
     *
     * @return void
     */
    protected function setRouterCachePath()
    {
        $router = $this->project['router'];

        $this->project['router']->

        cachePath($this->project->pathApplicationCache('router') . '/router.php')->

        development($this->project->development());
    }

    /**
     * 缓存配置
     *
     * @param string $sCachePath
     * @return void
     */
    protected function cacheOption($sCachePath)
    {
        $this->project['option']->reset(
            $this->project['option.load']->

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
                '~apps~' => $this->project->apps(),
                '~envs~' => $this->project->envs(),
                '~routers~' => $this->project->routers()
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
