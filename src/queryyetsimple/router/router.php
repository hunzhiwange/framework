<?php
// [$QueryPHP] A PHP Framework Since 2010.10.03. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\router;

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
use queryyetsimple\classs\faces as classs_faces;
use queryyetsimple\http\request;
use queryyetsimple\helper\helper;
use queryyetsimple\filesystem\directory;
use queryyetsimple\psr4\psr4;

/**
 * 路由解析
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.01.10
 * @version 1.0
 */
class router {
    
    use classs_faces;
    
    /**
     * 注册域名
     *
     * @var array
     */
    private $arrDomains = [ ];
    
    /**
     * 注册路由
     *
     * @var array
     */
    private $arrRouters = [ ];
    
    /**
     * 参数正则
     *
     * @var array
     */
    private $arrWheres = [ ];
    
    /**
     * 域名正则
     *
     * @var array
     */
    private $arrDomainWheres = [ ];
    
    /**
     * 分组传递参数
     *
     * @var array
     */
    private $arrGroupArgs = [ ];
    
    /**
     * 路由绑定资源
     *
     * @var string
     */
    private $arrBinds = [ ];
    
    /**
     * 域名匹配数据
     *
     * @var array
     */
    private $arrDomainData = [ ];
    
    /**
     * 路由缓存路径
     *
     * @var string
     */
    private $strCachePath;
    
    /**
     * 路由 debug
     *
     * @var boolean
     */
    private $booDebug = false;
    
    /**
     * 路由 development
     *
     * @var boolean
     */
    private $booDevelopment = false;
    
    /**
     * 应用名字
     *
     * @var string
     */
    private $strApp = null;
    
    /**
     * 控制器名字
     *
     * @var string
     */
    private $strController = null;
    
    /**
     * 方法名字
     *
     * @var string
     */
    private $strAction = null;
    
    /**
     * 默认替换参数[字符串]
     *
     * @var string
     */
    const DEFAULT_REGEX = '\S+';
    
    /**
     * 应用参数名
     *
     * @var string
     */
    const APP = 'app';
    
    /**
     * 控制器参数名
     *
     * @var string
     */
    const CONTROLLER = 'c';
    
    /**
     * 方法参数名
     *
     * @var string
     */
    const ACTION = 'a';
    
    /**
     * 配置
     *
     * @var array
     */
    protected $arrClasssFacesOption = [ 
            '~apps~' => [ 
                    '~_~',
                    'home' 
            ],
            'default_app' => 'home',
            'default_controller' => 'index',
            'default_action' => 'index',
            'url\router_cache' => true,
            'url\model' => 'pathinfo',
            'url\router_domain_on' => true,
            'url\html_suffix' => '.html',
            'url\router_domain_top' => '',
            'url\make_subdomain_on' => true,
            'url\pathinfo_depr' => '/',
            'url\rewrite' => false,
            'url\public' => 'http://public.foo.bar' 
    ];
    
    /**
     * 构造函数
     *
     * @return void
     */
    public function __construct() {
    }
    
    /**
     * 执行请求
     *
     * @return \queryyetsimple\mvc\request
     */
    public function run() {
        // 非命令行模式
        if (! request::isClis ()) {
            $this->parseWeb ();
        } else {
            $this->parseCli ();
        }
        
        // 解析URL
        $this->app ();
        $this->controller ();
        $this->action ();
        
        // 合并到 REQUEST
        $_REQUEST = array_merge ( $_POST, $_GET );
        
        // 解析项目公共 url 地址
        $this->parsePublicAndRoot ();
        
        return $this;
    }
    
    /**
     * 匹配路由
     *
     * @return void
     */
    public function parse() {
        // 读取缓存
        $this->readCache ();
        
        $arrNextParse = [ ];
        
        // 解析域名
        if ($this->classsFacesOption ( 'url\router_domain_on' ) === true) {
            if (($arrParseData = $this->parseDomain ( $arrNextParse )) !== false) {
                return $arrParseData;
            }
        }
        
        // 解析路由
        $arrNextParse = $arrNextParse ? array_column ( $arrNextParse, 'url' ) : [ ];
        return $this->parseRouter ( $arrNextParse );
    }
    
    /**
     * 取回应用名
     *
     * @return string
     */
    public function app() {
        if ($this->strApp) {
            return $this->strApp;
        } else {
            $sVar = static::APP;
            return $this->strApp = $_GET [$sVar] = ! empty ( $_POST [$sVar] ) ? $_POST [$sVar] : (! empty ( $_GET [$sVar] ) ? $_GET [$sVar] : $this->classsFacesOption ( 'default_app' ));
        }
    }
    
    /**
     * 取回控制器名
     *
     * @return string
     */
    public function controller() {
        if ($this->strController) {
            return $this->strController;
        } else {
            $sVar = static::CONTROLLER;
            return $this->strController = $_GET [$sVar] = ! empty ( $_GET [$sVar] ) ? $_GET [$sVar] : $this->classsFacesOption ( 'default_controller' );
        }
    }
    
    /**
     * 取回方法名
     *
     * @return string
     */
    public function action() {
        if ($this->strAction) {
            return $this->strAction;
        } else {
            $sVar = static::ACTION;
            return $this->strAction = $_GET [$sVar] = ! empty ( $_POST [$sVar] ) ? $_POST [$sVar] : (! empty ( $_GET [$sVar] ) ? $_GET [$sVar] : $this->classsFacesOption ( 'default_action' ));
        }
    }
    
    /**
     * 生成路由地址
     *
     * @param string $sUrl            
     * @param array $arrParams            
     * @param array $in
     *            suffix boolean 是否包含后缀
     *            normal boolean 是否为普通 url
     *            subdomain string 子域名
     * @return string
     */
    public function url($sUrl, $arrParams = [], $in = []) {
        $in = array_merge ( [ 
                'suffix' => true,
                'normal' => false,
                'subdomain' => 'www' 
        ], $in );
        
        $in ['args_app'] = static::APP;
        $in ['args_controller'] = static::CONTROLLER;
        $in ['args_action'] = static::ACTION;
        $in ['url_enter'] = static::$objProjectContainer->url_enter;
        
        // 以 “/” 开头的为自定义URL
        $in ['custom'] = false;
        if (0 === strpos ( $sUrl, '/' )) {
            $in ['custom'] = true;
        }         

        // 普通 url
        else {
            if ($sUrl != '') {
                if (! strpos ( $sUrl, '://' )) {
                    $sUrl = $_GET [$in ['args_app']] . '://' . $sUrl;
                }
                
                // 解析 url
                $arrArray = parse_url ( $sUrl );
            } else {
                $arrArray = [ ];
            }
            
            $in ['app'] = isset ( $arrArray ['scheme'] ) ? $arrArray ['scheme'] : $_GET [$in ['args_app']]; // APP
                                                                                                            
            // 分析获取模块和操作(应用)
            if (! empty ( $arrParams [$in ['args_app']] )) {
                $in ['app'] = $arrParams [$in ['args_app']];
                unset ( $arrParams [$in ['args_app']] );
            }
            if (! empty ( $arrParams [$in ['args_controller']] )) {
                $in ['controller'] = $arrParams [$in ['args_controller']];
                unset ( $arrParams [$in ['args_controller']] );
            }
            if (! empty ( $arrParams [$in ['args_action']] )) {
                $in ['action'] = $arrParams [$in ['args_action']];
                unset ( $arrParams [$in ['args_action']] );
            }
            if (isset ( $arrArray ['path'] )) {
                if (! isset ( $in ['controller'] )) {
                    if (! isset ( $arrArray ['host'] )) {
                        $in ['controller'] = $_GET [static::CONTROLLER];
                    } else {
                        $in ['controller'] = $arrArray ['host'];
                    }
                }
                
                if (! isset ( $in ['action'] )) {
                    $in ['action'] = substr ( $arrArray ['path'], 1 );
                }
            } else {
                if (! isset ( $in ['controller'] )) {
                    $in ['controller'] = $_GET [static::CONTROLLER];
                }
                if (! isset ( $in ['action'] )) {
                    $in ['action'] = $arrArray ['host'];
                }
            }
            
            // 如果指定了查询参数
            if (isset ( $arrArray ['query'] )) {
                $arrQuery = [ ];
                parse_str ( $arrArray ['query'], $arrQuery );
                $arrParams = array_merge ( $arrQuery, $arrParams );
            }
        }
        
        // 如果开启了URL解析，则URL模式为非普通模式
        if (($this->classsFacesOption ( 'url\model' ) == 'pathinfo' && $in ['normal'] === false) || $in ['custom'] === true) {
            // 非自定义 url
            if ($in ['custom'] === false) {
                // 额外参数
                $sStr = '/';
                foreach ( $arrParams as $sVar => $sVal ) {
                    $sStr .= $sVar . '/' . urlencode ( $sVal ) . '/';
                }
                $sStr = substr ( $sStr, 0, - 1 );
                
                // 分析 url
                $sUrl = ($in ['url_enter'] !== '/' ? $in ['url_enter'] : '') . ($this->classsFacesOption ( 'default_app' ) != $in ['app'] ? '/' . $in ['app'] . '/' : '/');
                
                if ($sStr) {
                    $sUrl .= $in ['controller'] . '/' . $in ['action'] . $sStr;
                } else {
                    $sTemp = '';
                    if ($this->classsFacesOption ( 'default_controller' ) != $in ['controller'] || $this->classsFacesOption ( 'default_action' ) != $in ['action']) {
                        $sTemp .= $in ['controller'];
                    }
                    if ($this->classsFacesOption ( 'default_action' ) != $in ['action']) {
                        $sTemp .= '/' . $in ['action'];
                    }
                    
                    if ($sTemp == '') {
                        $sUrl = rtrim ( $sUrl, '/' . '/' );
                    } else {
                        $sUrl .= $sTemp;
                    }
                    unset ( $sTemp );
                }
            }             

            // 自定义 url
            else {
                // 自定义支持参数变量替换
                if (strpos ( $sUrl, '{' ) !== false) {
                    $sUrl = preg_replace_callback ( "/{(.+?)}/", function ($arrMatches) use(&$arrParams) {
                        if (isset ( $arrParams [$arrMatches [1]] )) {
                            $sReturn = $arrParams [$arrMatches [1]];
                            unset ( $arrParams [$arrMatches [1]] );
                        } else {
                            $sReturn = $arrMatches [1];
                        }
                        return $sReturn;
                    }, $sUrl );
                }
                
                // 额外参数
                $sStr = '/';
                foreach ( $arrParams as $sVar => $sVal ) {
                    $sStr .= $sVar . '/' . urlencode ( $sVal ) . '/';
                }
                $sStr = substr ( $sStr, 0, - 1 );
                
                $sUrl .= $sStr;
            }
            
            if ($in ['suffix'] && $sUrl) {
                $sUrl .= $in ['suffix'] === true ? $this->classsFacesOption ( 'url\html_suffix' ) : $in ['suffix'];
            }
        }         

        // 普通url模式
        else {
            $sStr = '';
            foreach ( $arrParams as $sVar => $sVal ) {
                $sStr .= $sVar . '=' . urlencode ( $sVal ) . '&';
            }
            $sStr = rtrim ( $sStr, '&' );
            
            $sTemp = '';
            if ($in ['normal'] === true || $this->classsFacesOption ( 'default_app' ) != $in ['app']) {
                $sTemp [] = $in ['args_app'] . '=' . $in ['app'];
            }
            if ($this->classsFacesOption ( 'default_controller' ) != $in ['controller']) {
                $sTemp [] = $in ['args_controller'] . '=' . $in ['controller'];
            }
            if ($this->classsFacesOption ( 'default_action' ) != $in ['action']) {
                $sTemp [] = $in ['args_action'] . '=' . $in ['action'];
            }
            if ($sStr) {
                $sTemp [] = $sStr;
            }
            if (! empty ( $sTemp )) {
                $sTemp = '?' . implode ( '&', $sTemp );
            }
            $sUrl = ($in ['normal'] === true || $in ['url_enter'] !== '/' ? $in ['url_enter'] : '') . $sTemp;
            unset ( $sTemp );
        }
        
        // 子域名支持
        if ($this->classsFacesOption ( 'url\make_subdomain_on' ) === true) {
            if ($in ['subdomain']) {
                $sUrl = $this->urlWithDomain ( $in ['subdomain'] ) . $sUrl;
            }
        }
        
        return $sUrl;
    }
    
    /**
     * 导入路由规则
     *
     * @param mixed $mixRouter            
     * @param string $strUrl            
     * @param arra $in
     *            domain 域名
     *            params 参数
     *            where 参数正则
     *            prepend 插入顺序
     *            strict 严格模式，启用将在匹配正则 $
     *            prefix 前缀
     * @return void
     */
    public function import($mixRouter, $strUrl = '', $in = []) {
        if (! $this->checkExpired ())
            return;
            
            // 默认参数
        $in = $this->mergeIn ( [ 
                'prepend' => false,
                'where' => [ ],
                'params' => [ ],
                'domain' => '',
                'prefix' => '' 
        ], $this->mergeIn ( $this->arrGroupArgs, $in ) );
        
        // 支持数组传入
        if (! is_array ( $mixRouter ) || count ( $mixRouter ) == count ( $mixRouter, 1 )) {
            $strTemp = $mixRouter;
            $mixRouter = [ ];
            if (is_string ( $strTemp )) {
                $mixRouter [] = [ 
                        $strTemp,
                        $strUrl,
                        $in 
                ];
            } else {
                if ($strUrl || $strTemp [1]) {
                    $mixRouter [] = [ 
                            $strTemp [0],
                            (! empty ( $strTemp [1] ) ? $strTemp [1] : $strUrl),
                            $in 
                    ];
                }
            }
        } else {
            foreach ( $mixRouter as $intKey => $arrRouter ) {
                if (! is_array ( $arrRouter ) || count ( $arrRouter ) < 2) {
                    continue;
                }
                if (! isset ( $arrRouter [2] )) {
                    $arrRouter [2] = [ ];
                }
                if (! $arrRouter [1]) {
                    $arrRouter [1] = $strUrl;
                }
                $arrRouter [2] = $this->mergeIn ( $in, $arrRouter [2] );
                $mixRouter [$intKey] = $arrRouter;
            }
        }
        
        foreach ( $mixRouter as $arrArgs ) {
            $strPrefix = ! empty ( $arrArgs [2] ['prefix'] ) ? $arrArgs [2] ['prefix'] : '';
            $arrArgs [0] = $strPrefix . $arrArgs [0];
            
            $arrRouter = [ 
                    'url' => $arrArgs [1],
                    'regex' => $arrArgs [0],
                    'params' => $arrArgs [2] ['params'],
                    'where' => $this->arrWheres,
                    'domain' => $arrArgs [2] ['domain'] 
            ];
            
            if (isset ( $arrArgs [2] ['strict'] )) {
                $arrRouter ['strict'] = $arrArgs [2] ['strict'];
            }
            
            // 合并参数正则
            if (! empty ( $arrArgs [2] ['where'] ) && is_array ( $arrArgs [2] ['where'] )) {
                $arrRouter ['where'] = $this->mergeWhere ( $arrRouter ['where'], $arrArgs [2] ['where'] );
            }
            
            if (! isset ( $this->arrRouters [$arrArgs [0]] )) {
                $this->arrRouters [$arrArgs [0]] = [ ];
            }
            
            // 优先插入
            if ($arrArgs [2] ['prepend'] === true) {
                array_unshift ( $this->arrRouters [$arrArgs [0]], $arrRouter );
            } else {
                array_push ( $this->arrRouters [$arrArgs [0]], $arrRouter );
            }
            
            // 域名支持
            if (! empty ( $arrRouter ['domain'] )) {
                $in ['router'] = true;
                $this->domain ( $arrRouter ['domain'], $arrArgs [0], $in );
            }
        }
    }
    
    /**
     * 注册全局参数正则
     *
     * @param mixed $mixRegex            
     * @param string $strValue            
     * @return void
     */
    public function regex($mixRegex, $strValue = '') {
        if (! $this->checkExpired ())
            return;
        
        if (is_string ( $mixRegex )) {
            $this->arrWheres [$mixRegex] = $strValue;
        } else {
            $this->arrWheres = $this->mergeWhere ( $this->arrWheres, $mixRegex );
        }
    }
    
    /**
     * 注册全局域名参数正则
     *
     * @param mixed $mixRegex            
     * @param string $strValue            
     * @return void
     */
    public function regexDomain($mixRegex, $strValue = '') {
        if (! $this->checkExpired ())
            return;
        
        if (is_string ( $mixRegex )) {
            $this->arrDomainWheres [$mixRegex] = $strValue;
        } else {
            $this->arrDomainWheres = $this->mergeWhere ( $this->arrDomainWheres, $mixRegex );
        }
    }
    
    /**
     * 注册域名
     *
     * @param string $strDomain            
     * @param mixed $mixUrl            
     * @param array $in
     *            params 扩展参数
     *            domain_where 域名参数
     *            prepend 插入顺序
     *            router 对应路由规则
     * @return void
     */
    public function domain($strDomain, $mixUrl, $in = []) {
        if (! $this->checkExpired ())
            return;
        
        $in = $this->mergeIn ( [ 
                'prepend' => false,
                'params' => [ ],
                'domain_where' => [ ],
                'router' => false 
        ], $in );
        
        // 闭包直接转接到分组
        if ($mixUrl instanceof \Closure) {
            $in ['domain'] = $strDomain;
            $this->group ( $in, $mixUrl );
        }         

        // 注册域名
        else {
            $arrDomain = [ 
                    'url' => $mixUrl,
                    'params' => $in ['params'],
                    'router' => $in ['router'] 
            ];
            
            // 合并参数正则
            $arrDomainWheres = $this->arrDomainWheres;
            if (! empty ( $in ['domain_where'] ) && is_array ( $in ['domain_where'] )) {
                $arrDomainWheres = $this->mergeWhere ( $in ['domain_where'], $arrDomainWheres );
            }
            
            // 主域名只有一个，路由可以有多个
            $strDomainBox = $arrDomain ['router'] === false ? 'main' : 'rule';
            if (! isset ( $this->arrDomains [$strDomain] )) {
                $this->arrDomains [$strDomain] = [ ];
            }
            $this->arrDomains [$strDomain] ['domain_where'] = $arrDomainWheres;
            if (! isset ( $this->arrDomains [$strDomain] [$strDomainBox] )) {
                $this->arrDomains [$strDomain] [$strDomainBox] = [ ];
            }
            
            // 纯域名绑定只支持一个，可以被覆盖
            if ($arrDomain ['router'] === false) {
                $this->arrDomains [$strDomain] [$strDomainBox] = $arrDomain;
            } else {
                // 优先插入
                if ($in ['prepend'] === true) {
                    array_unshift ( $this->arrDomains [$strDomain] [$strDomainBox], $arrDomain );
                } else {
                    array_push ( $this->arrDomains [$strDomain] [$strDomainBox], $arrDomain );
                }
            }
        }
    }
    
    /**
     * 注册分组路由
     *
     * @param array $in
     *            prefix 前缀
     *            domain 域名
     *            params 参数
     *            where 参数正则
     *            prepend 插入顺序
     *            strict 严格模式，启用将在匹配正则 $
     * @param mixed $mixRouter            
     * @return void
     */
    public function group(array $in, $mixRouter) {
        if (! $this->checkExpired ())
            return;
            
            // 分组参数叠加
        $this->arrGroupArgs = $in = $this->mergeIn ( $this->arrGroupArgs, $in );
        
        if ($mixRouter instanceof \Closure) {
            call_user_func_array ( $mixRouter, [ ] );
        } else {
            if (! is_array ( current ( $mixRouter ) )) {
                $mixRouter = [ 
                        $mixRouter 
                ];
            }
            foreach ( $mixRouter as $arrVal ) {
                if (! is_array ( $arrVal ) || count ( $arrVal ) < 2) {
                    continue;
                }
                
                if (! isset ( $arrVal [2] )) {
                    $arrVal [2] = [ ];
                }
                
                $strPrefix = ! empty ( $arrArgs [2] ['prefix'] ) ? $arrArgs [2] ['prefix'] : (! empty ( $this->arrGroupArgs ['prefix'] ) ? $this->arrGroupArgs ['prefix'] : '');
                
                $this->import ( $strPrefix . $arrVal [0], $arrVal [1], $this->mergeIn ( $in, $arrVal [2] ) );
            }
        }
        
        $this->arrGroupArgs = [ ];
    }
    
    /**
     * 导入路由配置数据
     *
     * @param array $arrData            
     * @return void
     */
    public function importCache($arrData) {
        if (! $this->checkExpired ())
            return;
        
        if (isset ( $arrData ['~domains~'] )) {
            foreach ( $arrData ['~domains~'] as $arrVal ) {
                if (is_array ( $arrVal ) && isset ( $arrVal [1] )) {
                    empty ( $arrVal [2] ) && $arrVal [2] = [ ];
                    $this->domain ( $arrVal [0], $arrVal [1], $arrVal [2] );
                }
            }
            unset ( $arrData ['~domains~'] );
        }
        
        if ($arrData) {
            $this->import ( $arrData );
        }
    }
    
    /**
     * 获取绑定资源
     *
     * @param string $sBindName            
     * @return mixed
     */
    public function getBind($sBindName) {
        return isset ( $this->arrBinds [$sBindName] ) ? $this->arrBinds [$sBindName] : null;
    }
    
    /**
     * 判断是否绑定资源
     *
     * @param string $sBindName            
     * @return boolean
     */
    public function hasBind($sBindName) {
        return isset ( $this->arrBinds [$sBindName] ) ? true : false;
    }
    
    /**
     * 注册绑定资源
     *
     * 注册控制器：router::bind( 'group://topic', $mixBind )
     * 注册方法：router::bind( 'group://topic/index', $mixBind )
     *
     * @param string $sBindName            
     * @param mixed $mixBind            
     * @return void
     */
    public function bind($sBindName, $mixBind) {
        $this->arrBinds [$sBindName] = $mixBind;
    }
    
    /**
     * web 分析 url 参数
     *
     * @return void
     */
    private function parseWeb() {
        // 分析 pathinfo
        if ($this->classsFacesOption ( 'url\model' ) == 'pathinfo') {
            // 分析 pathinfo
            $this->pathInfo ();
            
            // 解析结果
            $_GET = array_merge ( $_GET, ($arrRouter = $this->parse ()) ? $arrRouter : $this->parsePathInfo () );
        }
    }
    
    /**
     * pathinfo 解析入口
     *
     * @return void
     */
    private function pathInfo() {
        $sPathInfo = $this->clearHtmlSuffix ( request::pathinfos () );
        $sPathInfo = empty ( $sPathInfo ) ? '/' : $sPathInfo;
        $_SERVER ['PATH_INFO'] = $sPathInfo;
    }
    
    /**
     * 分析 cli 参数
     *
     * @return void
     */
    private function parseCli() {
        switch (true) {
            // console 命令行
            case env ( 'queryphp_console' ) :
                $this->parseCliConsole ();
                break;
            // phpunit 命令行
            case env ( 'queryphp_phpunit' ) :
                $this->parseCliPhpunit ();
                break;
            // phpunit system 命令行
            case env ( 'queryphp_phpunit_system' ) :
                $this->parseCliPhpunitSystem ();
                break;
            default :
                $this->parseCliDefault ();
                break;
        }
    }
    
    /**
     * 注册 console 引导入口
     *
     * @return void
     */
    private function parseCliConsole() {
        define ( 'PATH_APP_BOOTSTRAP', dirname ( __DIR__ ) . '/bootstrap/console/bootstrap.php' );
        
        // 注册默认应用程序
        $_GET [static::APP] = '~_~@console';
        $_GET [static::CONTROLLER] = 'bootstrap';
        $_GET [static::ACTION] = 'index';
    }
    
    /**
     * 注册 phpunit 引导入口
     *
     * @return void
     */
    private function parseCliPhpunit() {
        define ( 'PATH_APP_BOOTSTRAP', dirname ( __DIR__ ) . '/bootstrap/testing/bootstrap.php' );
        
        // 注册默认应用程序
        $_GET [static::APP] = '~_~@testing';
        $_GET [static::CONTROLLER] = 'bootstrap';
        $_GET [static::ACTION] = 'index';
    }
    
    /**
     * 注册 phpunit 内部引导入口
     *
     * @return void
     */
    private function parseCliPhpunitSystem() {
        define ( 'PATH_APP_BOOTSTRAP', dirname ( dirname ( dirname ( __DIR__ ) ) ) . '/tests/bootstrap.php' );
        
        // 注册默认应用程序
        $_GET [static::APP] = '~_~@tests';
        $_GET [static::CONTROLLER] = 'bootstrap';
        $_GET [static::ACTION] = 'index';
        
        // 导入 tests 命名空间
        psr4::import ( 'tests', dirname ( PATH_APP_BOOTSTRAP ) );
    }
    
    /**
     * 分析默认 cli 参数
     *
     * @return void
     */
    private function parseCliDefault() {
        $arrArgv = isset ( $GLOBALS ['argv'] ) ? $GLOBALS ['argv'] : [ ];
        
        if (! isset ( $arrArgv ) || empty ( $arrArgv )) {
            return;
        }
        
        // 第一个为脚本自身
        array_shift ( $arrArgv );
        
        // 继续分析
        if ($arrArgv) {
            
            // app
            if (in_array ( $arrArgv [0], $this->classsFacesOption ( '~apps~' ) )) {
                $_GET [static::APP] = array_shift ( $arrArgv );
            }
            
            // controller
            if ($arrArgv) {
                $_GET [static::CONTROLLER] = array_shift ( $arrArgv );
            }
            
            // 方法
            if ($arrArgv) {
                $_GET [static::ACTION] = array_shift ( $arrArgv );
            }
            
            // 剩余参数
            if ($arrArgv) {
                for($nI = 0, $nCnt = count ( $arrArgv ); $nI < $nCnt; $nI ++) {
                    if (isset ( $arrArgv [$nI + 1] )) {
                        $_GET [$arrArgv [$nI]] = ( string ) $arrArgv [++ $nI];
                    } elseif ($nI == 0) {
                        $_GET [$_GET [static::ACTION]] = ( string ) $arrArgv [$nI];
                    }
                }
            }
        }
    }
    
    /**
     * 解析 pathinfo 参数
     *
     * @return array
     */
    private function parsePathInfo() {
        $arrPathInfo = [ ];
        $sPathInfo = $_SERVER ['PATH_INFO'];
        
        $arrPaths = explode ( $this->classsFacesOption ( 'url\pathinfo_depr' ), trim ( $sPathInfo, '/' ) );
        
        if (in_array ( $arrPaths [0], $this->classsFacesOption ( '~apps~' ) )) {
            $arrPathInfo [static::APP] = array_shift ( $arrPaths );
        }
        
        if (! isset ( $_GET [static::CONTROLLER] )) { // 还没有定义控制器名称
            $arrPathInfo [static::CONTROLLER] = array_shift ( $arrPaths );
        }
        
        if (! isset ( $_GET [static::ACTION] )) { // 还没有定义方法名称
            $arrPathInfo [static::ACTION] = array_shift ( $arrPaths );
        }
        
        for($nI = 0, $nCnt = count ( $arrPaths ); $nI < $nCnt; $nI ++) {
            if (isset ( $arrPaths [$nI + 1] )) {
                $arrPathInfo [$arrPaths [$nI]] = ( string ) $arrPaths [++ $nI];
            }
        }
        
        return $arrPathInfo;
    }
    
    /**
     * 解析域名路由
     *
     * @param array $arrNextParse            
     * @return void
     */
    private function parseDomain(&$arrNextParse) {
        if (! $this->arrDomains || ! $this->classsFacesOption ( 'url\router_domain_top' ))
            return;
        $strHost = request::getHosts ();
        
        $booFindDomain = false;
        foreach ( $this->arrDomains as $sKey => $arrDomains ) {
            
            // 直接匹配成功
            if ($strHost === $sKey || $strHost === $sKey . '.' . $this->classsFacesOption ( 'url\router_domain_top' )) {
                $booFindDomain = true;
            }            

            // 域名参数支持
            elseif (strpos ( $sKey, '{' ) !== false) {
                if (strpos ( $sKey, $this->classsFacesOption ( 'url\router_domain_top' ) ) === false) {
                    $sKey = $sKey . '.' . $this->classsFacesOption ( 'url\router_domain_top' );
                }
                
                // 解析匹配正则
                $sKey = $this->formatRegex ( $sKey );
                $sKey = preg_replace_callback ( "/{(.+?)}/", function ($arrMatches) use(&$arrDomains) {
                    $arrDomains ['args'] [] = $arrMatches [1];
                    return '(' . (isset ( $arrDomains ['domain_where'] [$arrMatches [1]] ) ? $arrDomains ['domain_where'] [$arrMatches [1]] : static::DEFAULT_REGEX) . ')';
                }, $sKey );
                $sKey = '/^' . $sKey . '$/';
                
                // 匹配结果
                if (preg_match ( $sKey, $strHost, $arrRes )) {
                    // 变量解析
                    if (isset ( $arrDomains ['args'] )) {
                        array_shift ( $arrRes );
                        foreach ( $arrDomains ['args'] as $intArgsKey => $strArgs ) {
                            $this->arrDomainData [$strArgs] = $arrRes [$intArgsKey];
                        }
                    }
                    
                    $booFindDomain = true;
                }
            }
            
            // 分析结果
            if ($booFindDomain === true) {
                if (isset ( $arrDomains ['rule'] )) {
                    $arrNextParse = $arrDomains ['rule'];
                    return false;
                } else {
                    $arrData = $this->parseNodeUrl ( $arrDomains ['main'] ['url'] );
                    
                    // 额外参数[放入 GET]
                    if (is_array ( $arrDomains ['main'] ['params'] ) && $arrDomains ['main'] ['params']) {
                        $arrData = array_merge ( $arrData, $arrDomains ['main'] ['params'] );
                    }
                    
                    // 合并域名匹配数据
                    $arrData = array_merge ( $this->arrDomainData, $arrData );
                    
                    return $arrData;
                }
            }
        }
    }
    
    /**
     * 解析路由规格
     *
     * @param array $arrNextParse            
     * @return array
     */
    private function parseRouter($arrNextParse = []) {
        if (! $this->arrRouters)
            return;
        $arrData = [ ];
        $sPathinfo = $_SERVER ['PATH_INFO'];
        
        // 匹配路由
        foreach ( $this->arrRouters as $sKey => $arrRouters ) {
            // 域名过滤掉无关路由
            if ($arrNextParse && ! in_array ( $sKey, $arrNextParse )) {
                continue;
            }
            
            foreach ( $arrRouters as $arrRouter ) {
                // 尝试匹配
                $booFindFouter = false;
                if ($arrRouter ['regex'] == $sPathinfo) {
                    $booFindFouter = true;
                } else {
                    // 解析匹配正则
                    $arrRouter ['regex'] = $this->formatRegex ( $arrRouter ['regex'] );
                    $arrRouter ['regex'] = preg_replace_callback ( "/{(.+?)}/", function ($arrMatches) use(&$arrRouter) {
                        $arrRouter ['args'] [] = $arrMatches [1];
                        return '(' . (isset ( $arrRouter ['where'] [$arrMatches [1]] ) ? $arrRouter ['where'] [$arrMatches [1]] : static::DEFAULT_REGEX) . ')';
                    }, $arrRouter ['regex'] );
                    $arrRouter ['regex'] = '/^\/' . $arrRouter ['regex'] . ((isset ( $arrRouter ['strict'] ) ? $arrRouter ['strict'] : $this->classsFacesOption ( 'url\router_strict' )) ? '$' : '') . '/';
                    
                    // 匹配结果
                    if (preg_match ( $arrRouter ['regex'], $sPathinfo, $arrRes )) {
                        $booFindFouter = true;
                    }
                }
                
                // 分析结果
                if ($booFindFouter === true) {
                    $arrData = $this->parseNodeUrl ( $arrRouter ['url'] );
                    
                    // 额外参数
                    if (is_array ( $arrRouter ['params'] ) && $arrRouter ['params']) {
                        $arrData = array_merge ( $arrData, $arrRouter ['params'] );
                    }
                    
                    // 变量解析
                    if (isset ( $arrRouter ['args'] )) {
                        array_shift ( $arrRes );
                        foreach ( $arrRouter ['args'] as $intArgsKey => $strArgs ) {
                            $arrData [$strArgs] = $arrRes [$intArgsKey];
                        }
                    }
                    break 2;
                }
            }
        }
        
        // 合并域名匹配数据
        $arrData = array_merge ( $this->arrDomainData, $arrData );
        
        return $arrData;
    }
    
    /**
     * 设置路由缓存地址
     *
     * @param string $strCachePath            
     * @return $this
     */
    public function cachePath($strCachePath) {
        $this->strCachePath = $strCachePath;
        return $this;
    }
    
    /**
     * 设置 debug
     *
     * @param boolean $booDebug            
     * @return $this
     */
    public function debug($booDebug) {
        $this->booDebug = $booDebug;
        return $this;
    }
    
    /**
     * 设置 development
     *
     * @param boolean $booDevelopment            
     * @return $this
     */
    public function development($booDevelopment) {
        $this->booDevelopment = $booDevelopment;
        return $this;
    }
    
    /**
     * 检查路由缓存是否过期
     *
     * @return boolean
     */
    public function checkExpired() {
        return $this->booDevelopment === true || ! $this->checkOpen () || ! is_file ( $this->strCachePath );
    }
    
    /**
     * 检查路由缓存是否开启
     *
     * @return boolean
     */
    private function checkOpen() {
        return $this->classsFacesOption ( 'url\router_cache' );
    }
    
    /**
     * 路由缓存
     *
     * @return void
     */
    private function readCache() {
        if (! $this->checkOpen ())
            return;
        
        if ($this->booDevelopment === false && is_file ( $this->strCachePath )) {
            $arrCacheData = ( array ) include $this->strCachePath;
            $this->arrDomains = $arrCacheData ['domains'];
            $this->arrRouters = $arrCacheData ['routers'];
            $this->arrDomainWheres = $arrCacheData ['domain_wheres'];
            $this->arrWheres = $arrCacheData ['wheres'];
            unset ( $arrCacheData );
            return;
        }
        
        $arrCacheData = [ 
                'domains' => $this->arrDomains,
                'routers' => $this->arrRouters,
                'domain_wheres' => $this->arrDomainWheres,
                'wheres' => $this->arrWheres 
        ];
        
        if (! is_dir ( dirname ( $this->strCachePath ) )) {
            directory::create ( dirname ( $this->strCachePath ) );
        }
        
        if (! file_put_contents ( $this->strCachePath, "<?php\n /* router cache */ \n return " . var_export ( $arrCacheData, true ) . "\n?>" )) {
            throw new RuntimeException ( sprintf ( 'Dir %s do not have permission.', $this->strCachePath ) );
        }
        
        if ($this->booDebug === false && ! file_put_contents ( $this->strCachePath, php_strip_whitespace ( $this->strCachePath ) )) {
            throw new RuntimeException ( sprintf ( 'Dir %s do not have permission.', $this->strCachePath ) );
        }
        
        unset ( $arrCacheData );
    }
    
    /**
     * 格式化正则
     *
     * @param string $sRegex            
     * @return string
     */
    private function formatRegex($sRegex) {
        $sRegex = helper::escapeRegexCharacter ( $sRegex );
        
        // 还原变量特殊标记
        return str_replace ( [ 
                '\{',
                '\}' 
        ], [ 
                '{',
                '}' 
        ], $sRegex );
    }
    
    /**
     * 合并 in 参数
     *
     * @param array $in            
     * @param array $arrExtend            
     * @return array
     */
    private function mergeIn(array $in, array $arrExtend) {
        // 合并特殊参数
        foreach ( [ 
                'params',
                'where',
                'domain_where' 
        ] as $strType ) {
            if (! empty ( $arrExtend [$strType] ) && is_array ( $arrExtend [$strType] )) {
                if (! isset ( $in [$strType] )) {
                    $in [$strType] = [ ];
                }
                $in [$strType] = $this->mergeWhere ( $in [$strType], $arrExtend [$strType] );
            }
        }
        
        // 合并额外参数
        foreach ( [ 
                'prefix',
                'domain',
                'prepend',
                'strict',
                'router' 
        ] as $strType ) {
            if (isset ( $arrExtend [$strType] )) {
                $in [$strType] = $arrExtend [$strType];
            }
        }
        
        return $in;
    }
    
    /**
     * 合并 where 正则参数
     *
     * @param array $arrWhere            
     * @param array $arrExtend            
     * @return array
     */
    private function mergeWhere(array $arrWhere, array $arrExtend) {
        // 合并参数正则
        if (! empty ( $arrExtend ) && is_array ( $arrExtend )) {
            if (is_string ( key ( $arrExtend ) )) {
                $arrWhere = array_merge ( $arrWhere, $arrExtend );
            } else {
                $arrWhere [$arrExtend [0]] = $arrExtend [1];
            }
        }
        
        return $arrWhere;
    }
    
    /**
     * 分析 url 数据
     * like [home://blog/index?arg1=1&arg2=2]
     *
     * @param string $sUrl            
     * @return array
     */
    private function parseNodeUrl($sUrl) {
        $arrData = [ ];
        
        // 解析 url
        if (strpos ( $sUrl, '://' ) === false) {
            $sUrl = 'QueryPHP://' . $sUrl;
        }
        $sUrl = parse_url ( $sUrl );
        
        // 应用
        if ($sUrl ['scheme'] != 'QueryPHP') {
            $arrData [static::APP] = $sUrl ['scheme'];
        }
        
        // 控制器
        $arrData [static::CONTROLLER] = $sUrl ['host'];
        
        // 方法
        if (isset ( $sUrl ['path'] ) && $sUrl ['path'] != '/') {
            $arrData [static::ACTION] = ltrim ( $sUrl ['path'], '/' );
        }
        
        // 额外参数
        if (isset ( $sUrl ['query'] )) {
            foreach ( explode ( '&', $sUrl ['query'] ) as $strQuery ) {
                $strQuery = explode ( '=', $strQuery );
                $arrData [$strQuery [0]] = $strQuery [1];
            }
        }
        
        return $arrData;
    }
    
    /**
     * 返回完整 URL 地址
     *
     * @param string $sDomain            
     * @param string $sHttpPrefix            
     * @param string $sHttpSuffix            
     * @return string
     */
    private function urlWithDomain($sDomain = '', $sHttpPrefix = '', $sHttpSuffix = '') {
        static $sHttpPrefix = '', $sHttpSuffix = '';
        if (! $sHttpPrefix) {
            $sHttpPrefix = request::isSsls () ? 'https://' : 'http://';
            $sHttpSuffix = $this->classsFacesOption ( 'url\router_domain_top' );
        }
        return $sHttpPrefix . ($sDomain && $sDomain != '*' ? $sDomain . '.' : '') . $sHttpSuffix;
    }
    
    /**
     * 解析项目公共和基础路径
     *
     * @return void
     */
    private function parsePublicAndRoot() {
        if (request::isClis ()) {
            return;
        }
        $arrResult = [ ];
        
        // 分析 php 入口文件路径
        $arrResult ['enter_bak'] = $arrResult ['enter'] = static::$objProjectContainer->url_enter;
        if (! $arrResult ['enter']) {
            // php 文件
            if (request::isCgis ()) {
                $arrTemp = explode ( '.php', $_SERVER ["PHP_SELF"] ); // CGI/FASTCGI模式下
                $arrResult ['enter'] = rtrim ( str_replace ( $_SERVER ["HTTP_HOST"], '', $arrTemp [0] . '.php' ), '/' );
            } else {
                $arrResult ['enter'] = rtrim ( $_SERVER ["SCRIPT_NAME"], '/' );
            }
            $arrResult ['enter_bak'] = $arrResult ['enter'];
            
            // 如果为重写模式
            if ($this->classsFacesOption ( 'url\rewrite' ) === true) {
                $arrResult ['enter'] = dirname ( $arrResult ['enter'] );
                if ($arrResult ['enter'] == '\\') {
                    $arrResult ['enter'] = '/';
                }
            }
        }
        
        // 网站 URL 根目录
        $arrResult ['root'] = static::$objProjectContainer->url_root;
        if (! $arrResult ['root']) {
            $arrResult ['root'] = dirname ( $arrResult ['enter_bak'] );
            $arrResult ['root'] = ($arrResult ['root'] == '/' || $arrResult ['root'] == '\\') ? '' : $arrResult ['root'];
        }
        
        // 网站公共文件目录
        $arrResult ['public'] = static::$objProjectContainer->url_public;
        if (! $arrResult ['public']) {
            $arrResult ['public'] = $this->classsFacesOption ( 'url\public' );
        }
        
        // 快捷方法供 router::url 方法使用
        foreach ( [ 
                'enter',
                'root',
                'public' 
        ] as $sType ) {
            static::$objProjectContainer->instance ( 'url_' . $sType, $arrResult [$sType] );
        }
        
        unset ( $arrResult, $objProject );
    }
    
    /**
     * 清理 url 后缀
     *
     * @param string $sVal            
     * @return string
     */
    private function clearHtmlSuffix($sVal) {
        if ($this->classsFacesOption ( 'url\html_suffix' ) && ! empty ( $sVal )) {
            $sSuffix = substr ( $this->classsFacesOption ( 'url\html_suffix' ), 1 );
            $sVal = preg_replace ( '/\.' . $sSuffix . '$/', '', $sVal );
        }
        return $sVal;
    }
}
