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

use RuntimeException;
use queryyetsimple\mvc\view;
use queryyetsimple\psr4\psr4;
use queryyetsimple\helper\helper;
use queryyetsimple\mvc\controller;
use Composer\Autoload\ClassLoader;
use queryyetsimple\support\container;
use queryyetsimple\bootstrap\interfaces\project as interfaces_project;

/**
 * 项目管理
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.01.14
 * @version 1.0
 */
class project extends container implements interfaces_project {
    
    /**
     * QueryPHP 版本
     *
     * @var string
     */
    const VERSION = '4.0.0';
    
    /**
     * 当前项目实例
     *
     * @var queryyetsimple\bootstrap\project
     */
    protected static $objProject = null;
    
    /**
     * 项目配置
     *
     * @var array
     */
    protected $arrOption = [ ];
    
    /**
     * 项目基础路径
     *
     * @var string
     */
    protected $strPath;
    
    /**
     * 基础服务提供者
     *
     * @var array
     */
    protected static $arrBaseProvider = [ 
            'queryyetsimple\mvc',
            'queryyetsimple\option',
            'queryyetsimple\http',
            'queryyetsimple\log',
            'queryyetsimple\provider',
            'queryyetsimple\session',
            'queryyetsimple\cookie',
            'queryyetsimple\i18n',
            'queryyetsimple\database',
            'queryyetsimple\event',
            'queryyetsimple\router',
            'queryyetsimple\pipeline',
            'queryyetsimple\cache',
            'queryyetsimple\validate',
            'queryyetsimple\throttler' 
    ];
    
    /**
     * 构造函数
     * 受保护的禁止外部通过 new 实例化，只能通过 singletons 生成单一实例
     *
     * @param \Composer\Autoload\ClassLoader $objComposer            
     * @param array $arrOption            
     * @return void
     */
    protected function __construct(ClassLoader $objComposer, $arrOption = []) {
        // 项目基础配置
        $this->setOption ( $arrOption )->
        
        // 初始化项目路径
        setPath ()->
        
        // 注册别名
        registerAlias ()->
        
        // 注册 psr4
        registerPsr4 ( $objComposer )->
        
        // 注册基础提供者 register
        registerBaseProvider ()->
        
        // 注册框架核心提供者
        registerMvcProvider ()->
        
        // 注册基础提供者 bootstrap
        registerBaseProviderBootstrap ();
    }
    
    /**
     * 禁止克隆
     *
     * @return void
     */
    protected function __clone() {
        throw new RuntimeException ( 'Project disallowed clone' );
    }
    
    /**
     * 执行项目
     *
     * @return $this
     */
    public function run() {
        (new bootstrap ( $this ))->run ();
        return $this;
    }
    
    /**
     * 返回项目
     *
     * @param \Composer\Autoload\ClassLoader $objComposer            
     * @param array $arrOption            
     * @param boolean $booRun            
     * @return $this
     */
    public static function singletons(ClassLoader $objComposer = null, $arrOption = [], $booRun = true) {
        if (static::$objProject !== null) {
            return static::$objProject;
        } else {
            static::$objProject = new static ( $objComposer, $arrOption );
            if ($booRun === true)
                static::$objProject->run ();
            return static::$objProject;
        }
    }
    
    /**
     * 程序版本
     *
     * @return number
     */
    public function version() {
        return static::VERSION;
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
     * 设置项目基础配置
     *
     * @param array $arrOption            
     * @return $this
     */
    protected function setOption($arrOption) {
        $this->arrOption = $arrOption;
        return $this;
    }
    
    /**
     * 注册 psr4
     *
     * @param \Composer\Autoload\ClassLoader $objComposer            
     * @return $this
     */
    protected function registerPsr4(ClassLoader $objComposer) {
        $this->instance ( 'psr4', new psr4 ( $this, $objComposer, dirname ( __DIR__ ) . '/bootstrap/sandbox' ) );
        $this->alias ( 'psr4', psr4::class );
        $this->instance ( 'composer', $objComposer );
        $this->alias ( 'composer', ClassLoader::class );
        
        spl_autoload_register ( [ 
                $this ['psr4'],
                'autoload' 
        ] );
        return $this;
    }
    
    /**
     * 框架基础提供者 register
     *
     * @return $this
     */
    protected function registerBaseProvider() {
        return $this->runBaseProvider ( 'register' );
    }
    
    /**
     * 框架基础提供者 bootstrap
     *
     * @return $this
     */
    protected function registerBaseProviderBootstrap() {
        return $this->runBaseProvider ( 'bootstrap' );
    }
    
    /**
     * 框架 MVC 基础提供者
     *
     * @return $this
     */
    protected function registerMvcProvider() {
        // 注册本身
        $this->instance ( 'project', $this );
        
        // 注册 app
        $this->singleton ( application::class, function (project $objProject, $sApp, $arrOption = []) {
            return new application ( $objProject, $sApp, $arrOption );
        } );
        
        return $this;
    }
    
    /**
     * 注册别名
     *
     * @return void
     */
    protected function registerAlias() {
        $this->alias ( [ 
                'project' => [ 
                        'queryyetsimple\bootstrap\project',
                        'queryyetsimple\support\interfaces\container',
                        'queryyetsimple\bootstrap\interfaces\project',
                        'app' 
                ] 
        ] );
        return $this;
    }
    
    /**
     * 初始化项目路径
     *
     * @param string $strPath            
     * @return $this
     */
    protected function setPath() {
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
    protected function registerPath() {
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
    protected function registerUrl() {
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
    protected function runProvider($arrProvider, $strType) {
        foreach ( $arrProvider as $strProvider ) {
            $objProvider = $this->make ( $strProvider, [ 
                    $this 
            ] );
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
    protected function runBaseProvider($strAction, $strType = 'base', $arrProvider = [], $booSystem = true) {
        return $this->registerProvider ( $this->providerCathPath ( $strType, $strAction ), array_map ( function ($strPackage) use($strAction) {
            return sprintf ( '%s\provider\%s', $strPackage, $strAction );
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
    protected function registerProvider($strCachePath, $arrFile = [], $booForce = false, $booParseNamespace = true) {
        $booForce = true;
        foreach ( helper::arrayMergeSource ( $this ['psr4'], $strCachePath, $arrFile, $booForce, $booParseNamespace ) as $strType => $mixProvider ) {
            
            if (strpos ( $strType, '@' ) !== false) {
                $arrRegisterArgs = explode ( '@', $strType );
            } else {
                if ($strType == 'bootstrap') {
                    $arrRegisterArgs = [ 
                            $strType 
                    ];
                } else {
                    $arrRegisterArgs = [ 
                            'register',
                            $strType 
                    ];
                }
            }
            
            switch ($arrRegisterArgs [0]) {
                case 'singleton' :
                case 'instance' :
                case 'register' :
                    $this->{$arrRegisterArgs [0]} ( $arrRegisterArgs [1], $mixProvider [1] );
                    if ($mixProvider [0]) {
                        $this->alias ( $arrRegisterArgs [1], $mixProvider [0] );
                    }
                    break;
                case 'bootstrap' :
                    if (! is_array ( $mixProvider )) {
                        $mixProvider = [ 
                                $mixProvider 
                        ];
                    }
                    foreach ( $mixProvider as $calVal ) {
                        call_user_func_array ( $calVal, [ 
                                $this 
                        ] );
                    }
                    break;
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
    protected function providerCathPath($strType, $strAction) {
        return $this->path_runtime . '/provider/' . $strType . '.' . $strAction . '.php';
    }
}
