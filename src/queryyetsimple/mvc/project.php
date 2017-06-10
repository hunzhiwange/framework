<?php
// [$QueryPHP] A PHP Framework Since 2010.10.03. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\mvc;

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

use queryyetsimple\psr4\psr4;
use queryyetsimple\helper\helper;
use queryyetsimple\support\container;

/**
 * 项目管理
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.01.14
 * @version 1.0
 */
class project extends container {
    
    /**
     * 当前项目实例
     *
     * @var queryyetsimple\mvc\project
     */
    private static $objProject = null;
    
    /**
     * 项目配置
     *
     * @var array
     */
    private $arrOption = [ ];
    
    /**
     * 项目基础路径
     *
     * @var string
     */
    private $strPath;
    
    /**
     * 基础服务提供者
     *
     * @var array
     */
    private static $arrBaseProvider = [ 
            'option',
            'http',
            'log',
            'provider',
            'session',
            'cookie',
            'i18n',
            'database',
            'event',
            'router',
            'pipeline',
            'cache',
            'view' 
    ];
    
    /**
     * 构造函数
     *
     * @param \Composer\Autoload\ClassLoader $objComposer            
     * @param array $arrOption            
     * @return void
     */
    public function __construct($objComposer, $arrOption = []) {
        // set composer
        $this->setComposer ( $objComposer )->
        
        // 项目基础配置
        setOption ( $arrOption )->
        
        // 初始化项目路径
        setPath ()->
        
        // 注册别名
        registerAlias ()->
        
        // 注册基础提供者 register
        registerBaseProvider ()->
        
        // 注册框架核心提供者
        registerMvcProvider ()->
        
        // 注册基础提供者 bootstrap
        registerBaseProviderBootstrap ();
    }
    
    /**
     * 执行项目
     *
     * @return void
     */
    public function run() {
        $this->make ( bootstrap::class )->run ();
    }
    
    /**
     * 返回项目
     *
     * @param \Composer\Autoload\ClassLoader $objComposer            
     * @param array $arrOption            
     * @return queryyetsimple\mvc\project
     */
    public static function bootstrap($objComposer = null, $arrOption = []) {
        if (static::$objProject !== null) {
            return static::$objProject;
        } else {
            return static::$objProject = new self ( $objComposer, $arrOption );
        }
    }
    
    /**
     * 注册应用提供者
     *
     * @param array $arrProvider            
     * @param array $arrProviderCache            
     * @return $this
     */
    public function registerAppProvider($arrProvider, $arrProviderCache) {
        return $this->runProvider ( $arrProvider, 'register' )->runProvider ( $arrProvider, 'bootstrap' )->runBaseProvider ( 'register', 'app', $arrProviderCache, false )->runBaseProvider ( 'bootstrap', 'app', $arrProviderCache, false );
    }
    
    /**
     * 基础路径
     *
     * @return string
     */
    public function path() {
        return $this->strPath;
    }
    
    /**
     * 应用路径
     *
     * @return string
     */
    public function pathApplication() {
        return isset ( $this->arrOption ['path_application'] ) ? $this->arrOption ['path_applicationp'] : $this->strPath . DIRECTORY_SEPARATOR . 'application';
    }
    
    /**
     * 公共路径
     *
     * @return string
     */
    public function pathCommon() {
        return isset ( $this->arrOption ['path_common'] ) ? $this->arrOption ['path_common'] : $this->strPath . DIRECTORY_SEPARATOR . 'common';
    }
    
    /**
     * 运行路径
     *
     * @return string
     */
    public function pathRuntime() {
        return isset ( $this->arrOption ['path_runtime'] ) ? $this->arrOption ['path_runtime'] : $this->strPath . DIRECTORY_SEPARATOR . '~@~';
    }
    
    /**
     * 资源路径
     *
     * @return string
     */
    public function pathPublic() {
        return isset ( $this->arrOption ['path_public'] ) ? $this->arrOption ['path_public'] : $this->strPath . DIRECTORY_SEPARATOR . 'public';
    }
    
    /**
     * public url
     *
     * @return string
     */
    public function urlPublic() {
        return $this->url_public;
    }
    
    /**
     * root url
     *
     * @return string
     */
    public function urlRoot() {
        return $this->url_root;
    }
    
    /**
     * enter url
     *
     * @return string
     */
    public function urlEnter() {
        return $this->url_enter;
    }
    
    /**
     * 设置 Composer
     *
     * @param \Composer\Autoload\ClassLoader $objComposer            
     * @return $this
     */
    private function setComposer($objComposer) {
        psr4::composer ( $objComposer );
        psr4::faces ( $this );
        spl_autoload_register ( [ 
                'queryyetsimple\psr4\psr4',
                'autoload' 
        ] );
        return $this;
    }
    
    /**
     * 设置项目基础配置
     *
     * @param array $arrOption            
     * @return $this
     */
    private function setOption($arrOption) {
        $this->arrOption = $arrOption;
        return $this;
    }
    
    /**
     * 框架基础提供者 register
     *
     * @return $this
     */
    private function registerBaseProvider() {
        return $this->runBaseProvider ( 'register' );
    }
    
    /**
     * 框架基础提供者 bootstrap
     *
     * @return $this
     */
    private function registerBaseProviderBootstrap() {
        return $this->runBaseProvider ( 'bootstrap' );
    }
    
    /**
     * 框架 MVC 基础提供者
     *
     * @return $this
     */
    private function registerMvcProvider() {
        // 注册启动程序
        $this->register ( new bootstrap ( $this, $this->arrOption ) );
        
        // 注册 app
        $this->singleton ( 'queryyetsimple\mvc\app', function (project $objProject, $sApp, $arrOption = []) {
            return new app ( $objProject, $sApp, $arrOption );
        } );
        
        // 注册 controller
        $this->singleton ( 'queryyetsimple\mvc\controller', function (project $objProject) {
            return (new controller ())->project ( $objProject );
        } );
        
        // 注册 view
        $this->singleton ( 'queryyetsimple\mvc\view', function (project $oProject) {
            $arrOption = [ ];
            foreach ( [ 
                    'cache_children',
                    'controlleraction_depr',
                    'suffix',
                    'switch',
                    'default',
                    'cookie_app' 
            ] as $strOption ) {
                $arrOption [$strOption] = $oProject ['option']->get ( 'view\\' . $strOption );
            }
            $oView = new view ( $oProject, $arrOption );
            return $oView;
        } );
        
        return $this;
    }
    
    /**
     * 注册别名
     *
     * @return void
     */
    private function registerAlias() {
        $this->alias ( [ 
                'view' => 'queryyetsimple\mvc\view',
                'controller' => 'queryyetsimple\mvc\controller' 
        ] );
        return $this;
    }
    
    /**
     * 初始化项目路径
     *
     * @param string $strPath            
     * @return $this
     */
    private function setPath() {
        // 基础路径
        $this->strPath = dirname ( dirname ( dirname ( dirname ( dirname ( dirname ( __DIR__ ) ) ) ) ) );
        
        // 注册路径
        $this->registerPath ();
        
        // 注册 url
        $this->registerUrl ();
        
        return $this;
    }
    
    /**
     * 注册路径到容器
     *
     * @return void
     */
    private function registerPath() {
        // 基础路径
        $this->instance ( 'path', $this->path () );
        
        // 其它路径
        foreach ( [ 
                'application',
                'common',
                'runtime',
                'public' 
        ] as $sKey => $sPath ) {
            $this->instance ( 'path_' . $sPath, $this->{'path' . ucwords ( $sPath )} () );
        }
    }
    
    /**
     * 注册 url 到容器
     *
     * @return void
     */
    private function registerUrl() {
        foreach ( [ 
                'enter',
                'root',
                'public' 
        ] as $sKey => $sUrl ) {
            $sUrl = 'url_' . $sUrl;
            $this->instance ( $sUrl, isset ( $this->arrOption [$sUrl] ) ? $this->arrOption [$sUrl] : '' );
        }
    }
    
    /**
     * 运行服务提供者
     *
     * @param array $arrProvider            
     * @param string $strType            
     * @return void
     */
    private function runProvider($arrProvider, $strType) {
        foreach ( $arrProvider as $strProvider ) {
            $objProvider = $this->make ( $strProvider, $this );
            if (method_exists ( $objProvider, $strType )) {
                $this->call ( [ 
                        $objProvider,
                        $strType 
                ] );
            }
        }
        return $this;
    }
    
    /**
     * 运行基础服务提供者
     *
     * @param string $strAction            
     * @param string $strType            
     * @param array $arrProvider            
     * @param boolean $booSystem            
     * @return $this
     */
    private function runBaseProvider($strAction, $strType = 'base', $arrProvider = [], $booSystem = true) {
        return $this->registerProvider ( $this->providerCathPath ( $strType, $strAction ), array_map ( function ($strPackage) use($strAction, $booSystem) {
            return ($booSystem ? 'queryyetsimple\\' : '') . sprintf ( '%s\provider\%s', $strPackage, $strAction );
        }, $booSystem ? static::$arrBaseProvider : $arrProvider ), env ( 'app_development' ) === 'development' );
    }
    
    /**
     * 注册缓存式服务提供者
     *
     * @param string $strCachePath            
     * @param array $arrFile            
     * @param boolean $booParseNamespace            
     * @param boolean $booForce            
     * @return array
     */
    private function registerProvider($strCachePath, $arrFile = [], $booForce = false, $booParseNamespace = true) {
        $booForce = true;
        foreach ( helper::arrayMergeSource ( $strCachePath, $arrFile, $booForce, $booParseNamespace ) as $strType => $mixProvider ) {
            if (is_string ( $strType ) && $strType) {
                if (strpos ( $strType, '@' ) !== false) {
                    $arrRegisterArgs = explode ( '@', $strType );
                } else {
                    $arrRegisterArgs = [ 
                            $strType,
                            '' 
                    ];
                }
            } else {
                $arrRegisterArgs = [ 
                        'register',
                        '' 
                ];
            }
            
            switch ($arrRegisterArgs [0]) {
                case 'singleton' :
                    $this->singleton ( $mixProvider [0], $mixProvider [1] );
                    break;
                case 'instance' :
                    $this->instance ( $mixProvider [0], $mixProvider [1] );
                    break;
                case 'register' :
                    $this->register ( $mixProvider [0], $mixProvider [1] );
                    break;
            }
            
            if ($arrRegisterArgs [1]) {
                $this->alias ( $arrRegisterArgs [1], $mixProvider [0] );
            }
        }
        return $this;
    }
    
    /**
     * 返回服务提供者路径
     *
     * @param string $strType            
     * @param string $strAction            
     * @return string
     */
    private function providerCathPath($strType, $strAction) {
        return $this->path_runtime . '/provider/' . $strType . '.' . $strAction . '.php';
    }
}
