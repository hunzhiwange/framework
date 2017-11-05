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
use Dotenv\Dotenv;
use RuntimeException;
use queryyetsimple\support\psr4;
use queryyetsimple\support\face;
use Composer\Autoload\ClassLoader;
use queryyetsimple\filesystem\fso;
use queryyetsimple\support\provider;
use queryyetsimple\support\container;

/**
 * 项目管理
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.01.14
 * @version 1.0
 */
class project extends container implements iproject {
    
    /**
     * 当前项目实例
     *
     * @var queryyetsimple\bootstrap\project
     */
    protected static $objProject;
    
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
     * 项目 APP 基本配置
     *
     * @var string
     */
    protected $arrAppOption = [ ];
    
    /**
     * 延迟载入服务提供者
     *
     * @var array
     */
    protected $arrDeferredProviders = [ ];
    
    /**
     * 服务提供者 bootstrap
     *
     * @var array
     */
    protected $arrProviderBootstrap = [ ];
    
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
        
        // 载入 app 配置
        loadApp ()->
        
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
     * @param \Composer\Autoload\ClassLoader|null $objComposer            
     * @param array $arrOption            
     * @param boolean $booRun            
     * @return $this
     */
    public static function singletons($objComposer = null, $arrOption = [], $booRun = true) {
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
     * (non-PHPdoc)
     *
     * @see \queryyetsimple\support\container::make()
     */
    public function make($strFactoryName, array $arrArgs = []) {
        $strFactoryName = $this->getAlias ( $strFactoryName );
        
        if (isset ( $this->arrDeferredProviders [$strFactoryName] )) {
            $this->registerDeferredProvider ( $strFactoryName );
        }
        
        return parent::make ( $strFactoryName, $arrArgs );
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
     * 附件路径
     *
     * @return string
     */
    public function pathStorage() {
        return isset ( $this->arrOption ['path_storage'] ) ? $this->arrOption ['path_storage'] : $this->strPath . DIRECTORY_SEPARATOR . 'storage';
    }
    
    /**
     * 应用路径
     *
     * @return string
     */
    public function pathApplicationCurrent() {
        return $this->pathApplication () . '/' . $this ['app_name'];
    }
    
    /**
     * 取得应用缓存目录
     *
     * @param string $strType            
     * @return string
     */
    public function pathApplicationCache($strType) {
        $arrType = [ 
                'file',
                'log',
                'table',
                'theme',
                'option',
                'i18n',
                'i18n_js',
                'router' 
        ];
        if (! in_array ( $strType, $arrType )) {
            throw new Exception ( sprintf ( 'Application cache type %s not support', $strType ) );
        }
        return $strType != 'i18n_js' ? $this->pathRuntime () . '/' . $strType : $this->pathPublic () . '/js/i18n';
    }
    
    /**
     * 取得应用目录
     *
     * @param string $strType            
     * @return string
     */
    public function pathApplicationDir($strType) {
        $arrType = [ 
                'option',
                'theme',
                'i18n' 
        ];
        if (! in_array ( $strType, $arrType )) {
            throw new Exception ( sprintf ( 'Application dir type %s not support', $strType ) );
        }
        return $this->pathApplicationCurrent () . '/ui/' . $strType;
    }
    
    /**
     * 是否开启 debug
     *
     * @return boolean
     */
    public function debug() {
        return isset ( $this->arrAppOption ['app_debug'] ) ? $this->arrAppOption ['app_debug'] : false;
    }
    
    /**
     * 是否为开发环境
     *
     * @return string
     */
    public function development() {
        return $this->arrAppOption ['app_environment'] == 'development';
    }
    
    /**
     * 运行环境
     *
     * @return boolean
     */
    public function environment() {
        return $this->arrAppOption ['app_environment'];
    }
    
    /**
     * 是否为 API
     *
     * @return boolean
     */
    public function api() {
        return $this->arrAppOption ['default_response'] == 'api';
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
     * 创建服务提供者
     *
     * @param string $strProvider            
     * @return \queryyetsimple\support\provider
     */
    public function makeProvider($strProvider) {
        return new $strProvider ( $this );
    }
    
    /**
     * 执行 bootstrap
     *
     * @param \queryyetsimple\support\provider $objProvider            
     * @return void
     */
    public function callProviderBootstrap(provider $objProvider) {
        if (! method_exists ( $objProvider, 'bootstrap' )) {
            return;
        }
        
        $this->call ( [ 
                $objProvider,
                'bootstrap' 
        ] );
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
        $this->instance ( 'psr4', new psr4 ( $objComposer, dirname ( __DIR__ ) . '/bootstrap/sandbox', [ 
                'queryyetsimple',
                'qys' 
        ] ) );
        $this->alias ( 'psr4', psr4::class );
        
        $this->instance ( 'composer', $objComposer );
        $this->alias ( 'composer', ClassLoader::class );
        
        face::setContainer ( $this );
        
        spl_autoload_register ( [ 
                $this ['psr4'],
                'autoload' 
        ] );
        return $this;
    }
    
    /**
     * 载入 APP 配置
     *
     * @return $this
     */
    protected function loadApp() {
        if (($strCache = $this->pathApplicationCache ( 'option' ) . '/' . application::INIT_APP . '.php') && is_file ( $strCache )) {
            if ($this->checkEnv ( $strCache )) {
                fso::deleteDirectory ( dirname ( $strCache ), true );
                $this->loadAppOption ();
            } else {
                $this->loadAppOption ( $strCache );
            }
        } else {
            $this->loadAppOption ();
        }
        return $this;
    }
    
    /**
     * 框架基础提供者 register
     *
     * @return $this
     */
    protected function registerBaseProvider() {
        if (empty ( $this->arrAppOption ['provider'] ))
            return $this;
        
        $booCache = false;
        
        $strCachePath = $this->defferProviderCachePath ();
        if (! $this->development () && is_file ( $strCachePath )) {
            list ( $this->arrDeferredProviders, $arrDeferredAlias ) = include $strCachePath;
            $booCache = true;
        } else {
            $arrDeferredAlias = [ ];
        }
        
        foreach ( $this->arrAppOption ['provider'] as $strProvider ) {
            $strProvider .= '\provider\register';
            
            if ($booCache === true && isset ( $arrDeferredAlias [$strProvider] )) {
                $this->alias ( $arrDeferredAlias [$strProvider] );
                continue;
            }
            
            if (! class_exists ( $strProvider ))
                continue;
            
            if ($strProvider::isDeferred ()) {
                $arrProviders = $strProvider::providers ();
                foreach ( $arrProviders as $mixKey => $mixAlias ) {
                    if (is_int ( $mixKey ))
                        $mixKey = $mixAlias;
                    $this->arrDeferredProviders [$mixKey] = $strProvider;
                }
                $this->alias ( $arrProviders );
                $arrDeferredAlias [$strProvider] = $arrProviders;
                continue;
            }
            
            $objProvider = $this->makeProvider ( $strProvider );
            $objProvider->register ();
            
            if (method_exists ( $objProvider, 'bootstrap' )) {
                $this->arrProviderBootstrap [] = $objProvider;
            }
        }
        
        if (! is_dir ( dirname ( $strCachePath ) )) {
            fso::createDirectory ( dirname ( $strCachePath ) );
        }
        
        if ($this->development () || ! is_file ( $strCachePath )) {
            file_put_contents ( $strCachePath, '<?php return ' . var_export ( [ 
                    $this->arrDeferredProviders,
                    $arrDeferredAlias 
            ], true ) . '; ?>' );
            file_put_contents ( $strCachePath, '<?php /* ' . date ( 'Y-m-d H:i:s' ) . ' */ ?>' . PHP_EOL . php_strip_whitespace ( $strCachePath ) );
        }
        
        return $this;
    }
    
    /**
     * 框架基础提供者 bootstrap
     *
     * @return $this
     */
    protected function registerBaseProviderBootstrap() {
        foreach ( $this->arrProviderBootstrap as $obj ) {
            $this->callProviderBootstrap ( $obj );
        }
        return $this;
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
                        'queryyetsimple\support\icontainer',
                        'queryyetsimple\bootstrap\iproject',
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
     * 注册延迟载入服务提供者
     *
     * @param string $strProvider            
     * @return void
     */
    protected function registerDeferredProvider($strProvider) {
        if (! isset ( $this->arrDeferredProviders [$strProvider] )) {
            return;
        }
        
        $objProvider = $this->makeProvider ( $this->arrDeferredProviders [$strProvider] );
        $objProvider->register ();
        
        if (method_exists ( $objProvider, 'bootstrap' )) {
            $this->callProviderBootstrap ( $objProvider );
        }
        
        unset ( $this->arrDeferredProviders [$strProvider] );
    }
    
    /**
     * 返回延迟服务提供者缓存路径
     *
     * @return string
     */
    protected function defferProviderCachePath() {
        return $this->pathRuntime () . '/provider/deffer.php';
    }
    
    /**
     * 载入 APP 配置
     *
     * @param string $strCache            
     * @return void
     */
    protected function loadAppOption($strCache = null) {
        if ($strCache && is_array ( $arrOption = include $strCache )) {
            $this->arrAppOption = $arrOption ['app'];
            $this->setEnvironmentVariables ( $arrOption ['env'] );
        } else {
            $this->setEnvironmentVariables ();
            $this->arrAppOption = ( array ) include $this->pathCommon () . '/ui/option/app.php';
        }
    }
    
    /**
     * 验证环境变量是否变动
     *
     * @param string $strCache            
     * @return void
     */
    protected function checkEnv($strCache) {
        return filemtime ( $this->path () . '/.env' ) > filemtime ( $strCache );
    }
    
    /**
     * 设置环境变量
     *
     * @param array $arrEnv            
     * @return void
     */
    protected function setEnvironmentVariables($arrEnv = []) {
        if ($arrEnv) {
            foreach ( $arrEnv as $strName => $strValue )
                $this->setEnvironmentVariable ( $strName, $strValue );
        } else {
            $objDotenv = new Dotenv ( $this->path () );
            $objDotenv->load ();
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
