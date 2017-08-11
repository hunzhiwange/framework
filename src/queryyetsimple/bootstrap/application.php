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

use Dotenv\Dotenv;
use queryyetsimple\psr4\psr4;
use queryyetsimple\helper\helper;
use queryyetsimple\option\option;
use queryyetsimple\http\response;
use queryyetsimple\assert\assert;
use queryyetsimple\filesystem\filesystem;
use queryyetsimple\i18n\tool as i18n_tool;
use queryyetsimple\option\tool as option_tool;

/**
 * 应用程序对象
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2016.11.18
 * @version 1.0
 */
class application {
    
    /**
     * 当前项目
     *
     * @var queryyetsimple\bootstrap\project
     */
    protected $objProject = null;
    
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
     * 配置命名空间
     *
     * @var array
     */
    protected $arrOptionNamespace = [ 
            'app',
            'cache',
            'console',
            'cookie',
            'database',
            'debug',
            'i18n',
            'log',
            'queue',
            'session',
            'url',
            'view',
            'router',
            'throttler' 
    ];
    
    /**
     * 执行事件流程
     *
     * @var array
     */
    protected $arrEvent = [ 
            'initialization',
            'loadBootstrap',
            'i18n',
            'response',
            'end' 
    ];
    
    /**
     * 构造函数
     *
     * @param \queryyetsimple\bootstrap\project $objProject            
     * @param string $sApp            
     * @param array $arrOption            
     * @return app
     */
    public function __construct(project $objProject, $sApp, $arrOption = []) {
        $this->objProject = $objProject;
        $this->strApp = $sApp;
        $this->arrOption = $arrOption;
    }
    
    /**
     * 执行应用
     *
     * @return void
     */
    public function run() {
        foreach ( $this->arrEvent as $strEvent ) {
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
    public function bootstrap($sApp = null) {
        if (! is_null ( $sApp ))
            $this->strApp = $sApp;
        $this->setPath ();
        $this->loadOption ();
        $this->loadRouter ();
        return $this;
    }
    
    /**
     * 注册命名空间
     *
     * @return $this
     */
    public function namespaces() {
        foreach ( $this->objProject ['option'] ['~apps~'] as $strApp ) {
            $this->objProject ['psr4']->import ( $strApp, $this->objProject->path_application . '/' . $strApp );
        }
        
        foreach ( $this->objProject ['option'] ['namespace'] as $strNamespace => $strPath ) {
            $this->objProject ['psr4']->import ( $strNamespace, $strPath );
        }
        
        return $this;
    }
    
    /**
     * 注册应用提供者
     *
     * @return $this
     */
    public function registerAppProvider() {
        $this->objProject->registerAppProvider ( $this->objProject ['option'] ['provider'], $this->objProject ['option'] ['provider_with_cache'] );
        return $this;
    }
    
    /**
     * 初始化处理
     *
     * @return void
     */
    protected function initializationRun() {
        if (env ( 'app_development' ) === 'development')
            error_reporting ( E_ALL );
        else
            error_reporting ( E_ERROR | E_PARSE | E_STRICT );
        
        ini_set ( 'default_charset', 'utf8' );
        
        if (function_exists ( 'date_default_timezone_set' ))
            date_default_timezone_set ( $this->objProject ['option'] ['time_zone'] );
        
        if (function_exists ( 'gz_handler' ) && $this->objProject ['option'] ['start_gzip'])
            ob_start ( 'gz_handler' );
        else
            ob_start ();
        
        if (env ( 'app_development' ) === 'development')
            assert::open ( true );
    }
    
    /**
     * 载入 app 引导文件
     *
     * @return void
     */
    protected function loadBootstrapRun() {
        if (is_file ( ($strBootstrap = env ( 'app_bootstrap' ) ?  : $this->objProject->path_application . '/' . $this->strApp . '/bootstrap.php') )) {
            require $strBootstrap;
        }
    }
    
    /**
     * 初始化国际语言包设置
     *
     * @return void
     */
    protected function i18nRun() {
        if (! $this->objProject ['option'] ['i18n\on'])
            return;
        
        $sI18nSet = $this->objProject ['i18n']->parseContext ();
        $this->objProject ['request']->setLangset ( $sI18nSet );
        if ($this->objProject ['option'] ['i18n\develop'] == $sI18nSet)
            return;
        
        $sCachePath = $this->getI18nCachePath ( $sI18nSet );
        $sCacheJsPath = $this->getI18nCacheJsPath ( $sI18nSet );
        
        if (env ( 'app_development' ) !== 'development' && is_file ( $sCachePath ) && is_file ( $sCacheJsPath )) {
            $this->objProject ['i18n']->addI18n ( $sI18nSet, ( array ) include $sCachePath );
        } else {
            $arrFiles = i18n_tool::findPoFile ( $this->getI18nDir ( $sI18nSet ) );
            $this->objProject ['i18n']->addI18n ( $sI18nSet, i18n_tool::saveToPhp ( $arrFiles ['php'], $sCachePath ) );
            i18n_tool::saveToJs ( $arrFiles ['js'], $sCacheJsPath, $sI18nSet );
            unset ( $sI18nSet, $arrFiles, $sCachePath, $sCacheJsPath );
        }
    }
    
    /**
     * 执行请求返回相应结果
     *
     * @return void
     */
    protected function responseRun() {
        $mixResponse = $this->objProject ['router']->doBind ();
        if (! ($mixResponse instanceof response)) {
            $mixResponse = $this->objProject ['response']->make ( $mixResponse );
        }
        
        // 穿越中间件
        if (($objResponse = $this->objProject ['router']->throughMidleware ( $this->objProject ['pipeline'], $mixResponse )) instanceof response) {
            $this->objContainer [response::class] = $objResponse;
        }
        
        // 输出响应
        $mixResponse->output ();
        unset ( $mixResponse, $objResponse );
    }
    
    /**
     * 结束处理
     *
     * @return void
     */
    protected function endRun() {
        // 记录日志
        if ($this->objProject ['option'] ['log\enabled'])
            $this->objProject ['log']->save ();
    }
    
    /**
     * 装配注册节点
     *
     * @param string $strController            
     * @param string $strAction            
     * @return string
     */
    protected function packControllerAndAction($strController, $strAction = '') {
        return $this->strApp . '://' . $strController . ($strAction ? '/' . $strAction : '');
    }
    
    /**
     * 分析配置文件
     *
     * @return void
     */
    protected function loadOption() {
        $sCachePath = $this->getOptionCachePath ();
        
        if ($this->strApp == static::INIT_APP) {
            if (! is_file ( $sCachePath ) || ! is_null ( $this->objProject ['option']->reset ( ( array ) include $sCachePath ) ) || $this->objProject ['option'] ['env\app_development'] === 'development') {
                $this->setEnvironmentVariables ();
                $this->cacheOption ( $sCachePath );
            }
        } else {
            if (env ( 'app_development' ) !== 'development' && is_file ( $sCachePath )) {
                $this->objProject ['option']->reset ( ( array ) include $sCachePath );
            } else {
                $this->cacheOption ( $sCachePath );
            }
        }
    }
    
    /**
     * 分析路由
     *
     * @return void
     */
    protected function loadRouter() {
        $this->setOptionRouterCachePath ();
        
        if ($this->strApp == static::INIT_APP) {
            if (is_file ( $this->getOptionCachePath () ) && $this->objProject ['option'] ['env\app_development'] !== 'development') {
                $this->cacheRouter ();
            } else {
                $this->importRouter ();
            }
        } elseif (env ( 'app_development' ) === 'development' || ! is_file ( $this->getOptionCachePath () )) {
            $this->importRouter ();
        }
    }
    
    /**
     * 设置应用路径
     *
     * @return void
     */
    protected function setPath() {
        $sAppName = $this->strApp;
        $sAppPath = $this->objProject->path_application . '/' . $sAppName;
        $sRuntime = $this->objProject->path_runtime;
        
        // 各种缓存组件路径
        foreach ( [ 
                'file',
                'log',
                'table',
                'theme',
                'option',
                'i18n' 
        ] as $sPath ) {
            $sPathName = 'path_cache_' . $sPath;
            $this->objProject->instance ( $sPathName, isset ( $this->arrOption [$sPathName] ) ? $this->arrOption [$sPathName] : $sRuntime . '/' . $sPath );
        }
        $this->objProject->instance ( 'path_cache_i18n_js', isset ( $this->arrOption ['path_cache_i18n_js'] ) ? $this->arrOption ['path_cache_i18n_js'] : $this->objProject->path_public . '/js/i18n/' . $sAppName ); // 默认 JS 语言包缓存目录
                                                                                                                                                                                                                      
        // 应用组件
        foreach ( [ 
                'option',
                'theme',
                'i18n' 
        ] as $sPath ) {
            $sPathName = 'path_app_' . $sPath;
            $this->objProject->instance ( $sPathName, isset ( $this->arrOption [$sPathName] ) ? $this->arrOption [$sPathName] : $sAppPath . '/interfaces/' . $sPath );
        }
        $this->objProject->instance ( 'path_app_theme_extend', isset ( $this->arrOption ['path_app_theme_extend'] ) ? $this->arrOption ['path_app_theme_extend'] : '' );
    }
    
    /**
     * 返回配置命名空间
     *
     * @return array
     */
    protected function getOptionNamespace() {
        return $this->arrOptionNamespace;
    }
    
    /**
     * 返回 i18n 目录
     *
     * @param string $sI18nSet            
     * @return array
     */
    protected function getI18nDir($sI18nSet) {
        $arrI18nDir = [ 
                dirname ( __DIR__ ) . '/bootstrap/i18n/' . $sI18nSet,
                $this->objProject->path_common . '/interfaces/i18n/' . $sI18nSet,
                $this->objProject->path_app_i18n . '/' . $sI18nSet 
        ];
        
        if ($this->objProject->path_app_i18n_extend) {
            if (is_array ( $this->objProject->path_app_i18n_extend )) {
                $arrI18nDir = array_merge ( $arrI18nDir, array_map ( function ($strDir) use($sI18nSet) {
                    return $strDir . '/' . $sI18nSet;
                }, $this->objProject->path_app_i18n_extend ) );
            } else {
                $arrI18nDir [] = $this->objProject->path_app_i18n_extend . '/' . $sI18nSet;
            }
        }
        
        return $arrI18nDir;
    }
    
    /**
     * 返回 i18n.php 缓存路径
     *
     * @param string $sI18nSet            
     * @return array
     */
    protected function getI18nCachePath($sI18nSet) {
        return $this->objProject ['path_cache_i18n'] . '/' . $sI18nSet . '/default.php';
    }
    
    /**
     * 返回 i18n.js 缓存路径
     *
     * @param string $sI18nSet            
     * @return array
     */
    protected function getI18nCacheJsPath($sI18nSet) {
        return $this->objProject ['path_cache_i18n_js'] . '/' . $sI18nSet . '/default.js';
    }
    
    /**
     * 返回配置目录
     *
     * @return array
     */
    protected function getOptionDir() {
        $arrOptionDir = [ 
                dirname ( __DIR__ ) . '/bootstrap/option' 
        ];
        if (is_dir ( $this->objProject->path_common . '/interfaces/option' ))
            $arrOptionDir [] = $this->objProject->path_common . '/interfaces/option';
        $arrOptionDir [] = $this->objProject->path_app_option;
        return $arrOptionDir;
    }
    
    /**
     * 返回配置缓存路径
     *
     * @return array
     */
    protected function getOptionCachePath() {
        return $this->objProject->path_cache_option . '/' . $this->strApp . '.php';
    }
    
    /**
     * 设置路由缓存路径
     *
     * @return array
     */
    protected function setOptionRouterCachePath() {
        $this->objProject ['router']->cachePath ( $this->objProject->path_cache_option . '/' . $this->strApp . '@router.php' )->development ( env ( 'app_development' ) === 'development' );
    }
    
    /**
     * 缓存配置
     *
     * @param string $sCachePath            
     * @return void
     */
    protected function cacheOption($sCachePath) {
        $this->objProject ['option']->reset ( option_tool::saveToCache ( $this->getOptionDir (), $this->getOptionNamespace (), $sCachePath, [ 
                'app' => [ 
                        '~apps~' => filesystem::lists ( $this->objProject->path_application ) 
                ],
                'env' => $_ENV 
        ], $this->strApp == static::INIT_APP ) );
    }
    
    /**
     * 路由缓存
     *
     * @return void
     */
    protected function cacheRouter() {
        if (! $this->objProject ['router']->checkExpired ())
            return;
        
        if (($arrRouter = $this->objProject ['option'] ['router\\'])) {
            $this->objProject ['router']->importCache ( $arrRouter );
        }
        
        if (($arrRouterType = $this->objProject ['option'] ['~routers~'])) {
            foreach ( $this->getOptionDir () as $sDir ) {
                foreach ( $arrRouterType as $sType ) {
                    if (! is_file ( $strFile = $sDir . '/' . $sType . '.php' ))
                        continue;
                    include $strFile;
                }
            }
        }
    }
    
    /**
     * 导入路由
     *
     * @return void
     */
    protected function importRouter() {
        if ($this->objProject ['option']->get ( 'router\\' ))
            $this->objProject ['router']->importCache ( $this->objProject ['option']->get ( 'router\\' ) );
    }
    
    /**
     * 设置环境变量
     *
     * @param boolean $booCache            
     * @return void
     */
    protected function setEnvironmentVariables($booCache = false) {
        if ($booCache === true) {
            foreach ( $this->objProject ['option'] ['env\\'] as $strName => $strValue )
                $this->setEnvironmentVariable ( $strName, $strValue );
        } else {
            $objDotenv = new Dotenv ( $this->objProject->path () );
            $objDotenv->load ();
            $this->defaultEnvironment ();
        }
    }
    
    /**
     * 设置默认环境变量
     *
     * @return void
     */
    protected function defaultEnvironment() {
        foreach ( [
                // 调试模式
                'app_debug' => false,
                
                // 项目运行环境 production : 生成环境 testing : 测试环境 development : 开发环境
                'app_development' => 'production',
                
                // app 引导文件
                'app_bootstrap' => '',
                
                // 绑定 app_name
                'app_name' => '',
                
                // 绑定 controller_name
                'controller_name' => '',
                
                // 绑定 action_name
                'action_name' => '' 
        ] as $strName => $mixValue ) {
            if (is_null ( env ( $strName ) ))
                $this->setEnvironmentVariable ( $strName, $mixValue );
        }
    }
    
    /**
     * 设置单个环境变量
     *
     * @param string $strName            
     * @param string|null $mixValue            
     * @return void
     */
    protected function setEnvironmentVariable($strName, $mixValue = null) {
        if (is_bool ( $mixValue )) {
            putenv ( $strName . '=' . ($mixValue ? '(true)' : '(false)') );
        } elseif (is_null ( $mixValue )) {
            putenv ( $strName . '(null)' );
        } else {
            putenv ( $strName . '=' . $mixValue );
        }
        $_ENV [$strName] = $mixValue;
        $_SERVER [$strName] = $mixValue;
    }
}
