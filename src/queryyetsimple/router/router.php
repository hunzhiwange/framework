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
 * (c) 2010-2017 http://queryphp.com All rights reserved.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace queryyetsimple\router;

use Closure;
use Exception;
use RuntimeException;
use ReflectionMethod;
use ReflectionException;
use InvalidArgumentException;
use queryyetsimple\mvc\iaction;
use queryyetsimple\http\request;
use queryyetsimple\http\response;
use queryyetsimple\support\option;
use queryyetsimple\support\helper;
use queryyetsimple\filesystem\fso;
use queryyetsimple\mvc\icontroller;
use queryyetsimple\support\infinity;
use queryyetsimple\pipeline\ipipeline;
use queryyetsimple\support\icontainer;

/**
 * 路由解析
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.01.10
 * @version 1.0
 */
class router
{
    use infinity;
    use option;
    
    /**
     * container
     *
     * @var \queryyetsimple\support\icontainer
     */
    protected $objContainer;
    
    /**
     * pipeline
     *
     * @var \queryyetsimple\pipeline\ipipeline
     */
    protected $objPipeline;
    
    /**
     * http 请求
     *
     * @var \queryyetsimple\http\request
     */
    protected $objRequest;
    
    /**
     * 注册域名
     *
     * @var array
     */
    protected $arrDomains = [];
    
    /**
     * 注册路由
     *
     * @var array
     */
    protected $arrRouters = [];
    
    /**
     * 参数正则
     *
     * @var array
     */
    protected $arrWheres = [];
    
    /**
     * 域名正则
     *
     * @var array
     */
    protected $arrDomainWheres = [];
    
    /**
     * 分组传递参数
     *
     * @var array
     */
    protected $arrGroupArgs = [];
    
    /**
     * 路由绑定资源
     *
     * @var string
     */
    protected $arrBinds = [];
    
    /**
     * 域名匹配数据
     *
     * @var array
     */
    protected $arrDomainData = [];
    
    /**
     * 路由缓存路径
     *
     * @var string
     */
    protected $strCachePath;
    
    /**
     * 路由 development
     *
     * @var boolean
     */
    protected $booDevelopment = false;
    
    /**
     * 应用名字
     *
     * @var string
     */
    protected $strApp;
    
    /**
     * 控制器名字
     *
     * @var string
     */
    protected $strController;
    
    /**
     * 方法名字
     *
     * @var string
     */
    protected $strAction;
    
    /**
     * 路由绑定中间件
     *
     * @var array
     */
    protected $arrMiddlewares = [];
    
    /**
     * 当前的中间件
     *
     * @var array
     */
    protected $arrCurrentMiddleware;
    
    /**
     * HTTP 方法
     *
     * @var array
     */
    protected $arrMethods = [];
    
    /**
     * 路由匹配变量
     *
     * @var array
     */
    protected $arrVariable = [];
    
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
     * 数字参数名
     *
     * @var string
     */
    const ARGS = 'args';
    
    /**
     * 配置
     *
     * @var array
     */
    protected $arrOption = [
        '~apps~' => [
            '~_~', 
            'home'
        ], 
        'default_app' => 'home', 
        'default_controller' => 'index', 
        'default_action' => 'index', 
        'router_cache' => true, 
        'model' => 'pathinfo', 
        'router_domain_on' => true, 
        'html_suffix' => '.html', 
        'router_domain_top' => '', 
        'make_subdomain_on' => true, 
        'pathinfo_depr' => '/', 
        'rewrite' => false, 
        'public' => 'http://public.foo.bar', 
        'pathinfo_restful' => true, 
        'args_protected' => [], 
        'args_regex' => [], 
        'args_strict' => false, 
        'middleware_strict' => false, 
        'method_strict' => false, 
        'controller_dir' => 'app/controller', 
        
        // 路由分组
        'middleware_group' => [], 
        
        // 路由别名
        'middleware_alias' => []
    ];
    
    /**
     * 构造函数
     *
     * @param \queryyetsimple\pipeline\ipipeline $objPipeline
     * @param \queryyetsimple\support\icontainer $objContainer
     * @param \queryyetsimple\http\request $objRequest
     * @param array $arrOption
     * @return void
     */
    public function __construct(icontainer $objContainer, ipipeline $objPipeline, request $objRequest, array $arrOption = [])
    {
        $this->objContainer = $objContainer;
        $this->objPipeline = $objPipeline;
        $this->objRequest = $objRequest;
        $this->options($arrOption);
    }
    
    /**
     * 执行请求
     *
     * @return $this
     */
    public function run()
    {
        // 非命令行模式
        if (! $this->objRequest->isCli()) {
            $this->parseWeb();
        } else {
            $this->parseCli();
        }
        
        // 完成请求
        $this->completeRequest();
        
        // 验证 HTTP 方法
        $this->validateMethod();
        
        // 穿越中间件
        $this->throughMiddleware($this->objPipeline, $this->objRequest);
        
        // 解析项目公共 url 地址
        $this->parsePublicAndRoot();
        
        return $this;
    }
    
    /**
     * 匹配路由
     *
     * @return void
     */
    public function parse()
    {
        // 读取缓存
        $this->readCache();
        
        $arrNextParse = [];
        
        // 解析域名
        if ($this->getOption('router_domain_on') === true) {
            if (($arrParseData = $this->parseDomain($arrNextParse)) !== false) {
                return $arrParseData;
            }
        }
        
        // 解析路由
        $arrNextParse = $arrNextParse ? array_column($arrNextParse, 'url') : [];
        return $this->parseRouter($arrNextParse);
    }
    
    /**
     * 取回应用名
     *
     * @return string
     */
    public function app()
    {
        if ($this->strApp) {
            return $this->strApp;
        } else {
            if (($this->strApp = env('app_name'))) {
                return $this->strApp;
            }
            $sVar = static::APP;
            return $this->strApp = $_GET[$sVar] = ! empty($_POST[$sVar]) ? $_POST[$sVar] : (! empty($_GET[$sVar]) ? $_GET[$sVar] : $this->getOption('default_app'));
        }
    }
    
    /**
     * 取回控制器名
     *
     * @return string
     */
    public function controller()
    {
        if ($this->strController) {
            return $this->strController;
        } else {
            if (($this->strController = env('controller_name'))) {
                return $this->strController;
            }
            $sVar = static::CONTROLLER;
            return $this->strController = $_GET[$sVar] = ! empty($_GET[$sVar]) ? $_GET[$sVar] : $this->getOption('default_controller');
        }
    }
    
    /**
     * 取回方法名
     *
     * @return string
     */
    public function action()
    {
        if ($this->strAction) {
            return $this->strAction;
        } else {
            if (($this->strAction = env('action_name'))) {
                return $this->strAction;
            }
            $sVar = static::ACTION;
            return $this->strAction = $_GET[$sVar] = ! empty($_POST[$sVar]) ? $_POST[$sVar] : (! empty($_GET[$sVar]) ? $_GET[$sVar] : $this->getOption('default_action'));
        }
    }
    
    /**
     * 生成路由地址
     *
     * @param string $sUrl
     * @param array $arrParams
     * @param array $arrOption
     * @sub boolean suffix 是否包含后缀
     * @sub boolean normal 是否为普通 url
     * @sub string subdomain 子域名
     * @return string
     */
    public function url($sUrl, $arrParams = [], $arrOption = [])
    {
        $arrOption = array_merge([
            'suffix' => true, 
            'normal' => false, 
            'subdomain' => 'www'
        ], $arrOption);
        
        $arrOption['args_app'] = static::APP;
        $arrOption['args_controller'] = static::CONTROLLER;
        $arrOption['args_action'] = static::ACTION;
        $arrOption['url_enter'] = $this->objContainer['url_enter'];
        
        // 以 “/” 开头的为自定义URL
        $arrOption['custom'] = false;
        if (0 === strpos($sUrl, '/')) {
            $arrOption['custom'] = true;
        }         

        // 普通 url
        else {
            if ($sUrl != '') {
                if (! strpos($sUrl, '://')) {
                    $sUrl = $_GET[$arrOption['args_app']] . '://' . $sUrl;
                }
                
                // 解析 url
                $arrArray = parse_url($sUrl);
            } else {
                $arrArray = [];
            }
            
            $arrOption['app'] = isset($arrArray['scheme']) ? $arrArray['scheme'] : $_GET[$arrOption['args_app']]; // APP
            

            // 分析获取模块和操作(应用)
            if (! empty($arrParams[$arrOption['args_app']])) {
                $arrOption['app'] = $arrParams[$arrOption['args_app']];
                unset($arrParams[$arrOption['args_app']]);
            }
            if (! empty($arrParams[$arrOption['args_controller']])) {
                $arrOption['controller'] = $arrParams[$arrOption['args_controller']];
                unset($arrParams[$arrOption['args_controller']]);
            }
            if (! empty($arrParams[$arrOption['args_action']])) {
                $arrOption['action'] = $arrParams[$arrOption['args_action']];
                unset($arrParams[$arrOption['args_action']]);
            }
            if (isset($arrArray['path'])) {
                if (! isset($arrOption['controller'])) {
                    if (! isset($arrArray['host'])) {
                        $arrOption['controller'] = $_GET[$arrOption['args_controller']];
                    } else {
                        $arrOption['controller'] = $arrArray['host'];
                    }
                }
                
                if (! isset($arrOption['action'])) {
                    $arrOption['action'] = substr($arrArray['path'], 1);
                }
            } else {
                if (! isset($arrOption['controller'])) {
                    $arrOption['controller'] = $_GET[$arrOption['args_controller']];
                }
                
                if (! isset($arrOption['action'])) {
                    if (! isset($arrArray['host'])) {
                        $arrOption['action'] = $_GET[$arrOption['args_action']];
                    } else {
                        $arrOption['action'] = $arrArray['host'];
                    }
                }
            }
            
            // 如果指定了查询参数
            if (isset($arrArray['query'])) {
                $arrQuery = [];
                parse_str($arrArray['query'], $arrQuery);
                $arrParams = array_merge($arrQuery, $arrParams);
            }
        }
        
        // 如果开启了URL解析，则URL模式为非普通模式
        if (($this->getOption('model') == 'pathinfo' && $arrOption['normal'] === false) || $arrOption['custom'] === true) {
            // 非自定义 url
            if ($arrOption['custom'] === false) {
                // 额外参数
                $sStr = '/';
                foreach ($arrParams as $sVar => $sVal) {
                    if (! is_scalar($sVal)) {
                        if (is_array($sVal) && $sVal) {
                            $sStr .= implode('/', $sVal) . '/';
                        }
                        continue;
                    }
                    $sStr .= $sVar . '/' . urlencode($sVal) . '/';
                }
                $sStr = substr($sStr, 0, - 1);
                
                // 分析 url
                $sUrl = ($arrOption['url_enter'] !== '/' ? $arrOption['url_enter'] : '') . ($this->getOption('default_app') != $arrOption['app'] ? '/' . $arrOption['app'] . '/' : '/');
                
                if ($sStr) {
                    $sUrl .= $arrOption['controller'] . '/' . $arrOption['action'] . $sStr;
                } else {
                    $sTemp = '';
                    if ($this->getOption('default_controller') != $arrOption['controller'] || $this->getOption('default_action') != $arrOption['action']) {
                        $sTemp .= $arrOption['controller'];
                    }
                    if ($this->getOption('default_action') != $arrOption['action']) {
                        $sTemp .= '/' . $arrOption['action'];
                    }
                    
                    if ($sTemp == '') {
                        $sUrl = rtrim($sUrl, '/' . '/');
                    } else {
                        $sUrl .= $sTemp;
                    }
                    unset($sTemp);
                }
            }             

            // 自定义 url
            else {
                // 自定义支持参数变量替换
                if (strpos($sUrl, '{') !== false) {
                    $sUrl = preg_replace_callback("/{(.+?)}/", function ($arrMatches) use(&$arrParams)
                    {
                        if (isset($arrParams[$arrMatches[1]])) {
                            $sReturn = $arrParams[$arrMatches[1]];
                            unset($arrParams[$arrMatches[1]]);
                        } else {
                            $sReturn = $arrMatches[0];
                        }
                        return $sReturn;
                    }, $sUrl);
                }
                
                // 额外参数
                $sStr = '/';
                foreach ($arrParams as $sVar => $sVal) {
                    if (! is_scalar($sVal)) {
                        if (is_array($sVal) && $sVal) {
                            $sStr .= implode('/', $sVal) . '/';
                        }
                        continue;
                    }
                    $sStr .= $sVar . '/' . urlencode($sVal) . '/';
                }
                $sStr = substr($sStr, 0, - 1);
                
                $sUrl .= $sStr;
            }
            
            if ($arrOption['suffix'] && $sUrl) {
                $sUrl .= $arrOption['suffix'] === true ? $this->getOption('html_suffix') : $arrOption['suffix'];
            }
        }         

        // 普通url模式
        else {
            $sStr = '';
            foreach ($arrParams as $sVar => $sVal) {
                if (! is_scalar($sVal)) {
                    if (is_array($sVal) && $sVal) {
                        $sStr .= implode('/', $sVal) . '/';
                    }
                    continue;
                }
                $sStr .= $sVar . '=' . urlencode($sVal) . '&';
            }
            $sStr = rtrim($sStr, '&');
            
            $sTemp = '';
            if ($arrOption['normal'] === true || $this->getOption('default_app') != $arrOption['app']) {
                $sTemp[] = $arrOption['args_app'] . '=' . $arrOption['app'];
            }
            if ($this->getOption('default_controller') != $arrOption['controller']) {
                $sTemp[] = $arrOption['args_controller'] . '=' . $arrOption['controller'];
            }
            if ($this->getOption('default_action') != $arrOption['action']) {
                $sTemp[] = $arrOption['args_action'] . '=' . $arrOption['action'];
            }
            if ($sStr) {
                $sTemp[] = $sStr;
            }
            if (! empty($sTemp)) {
                $sTemp = '?' . implode('&', $sTemp);
            }
            $sUrl = ($arrOption['normal'] === true || $arrOption['url_enter'] !== '/' ? $arrOption['url_enter'] : '') . $sTemp;
            unset($sTemp);
        }
        
        // 子域名支持
        if ($this->getOption('make_subdomain_on') === true && $this->getOption('router_domain_top')) {
            if ($arrOption['subdomain']) {
                $sUrl = $this->urlWithDomain($arrOption['subdomain']) . $sUrl;
            }
        }
        
        return $sUrl;
    }
    
    /**
     * 路由 URL 跳转
     *
     * @param string $sUrl
     * @param 额外参数 $arrOption
     * @sub string make 是否使用 url 生成地址
     * @sub string params url 额外参数
     * @sub string message 消息
     * @sub int time 停留时间，0表示不停留
     * @return void
     */
    public function redirect($sUrl, $arrOption = [])
    {
        $arrOption = array_merge([
            'make' => true, 
            'params' => [], 
            'message' => '', 
            'time' => 0
        ], $arrOption);
        
        $this->urlRedirect($arrOption['make'] ? $this->url($sUrl, $arrOption['params']) : $sUrl, $arrOption['time'], $arrOption['message']);
    }
    
    /**
     * URL 重定向
     *
     * @param string $sUrl
     * @param number $nTime
     * @param string $sMsg
     * @return void
     */
    public function urlRedirect($sUrl, $nTime = 0, $sMsg = '')
    {
        $sUrl = str_replace(PHP_EOL, '', $sUrl); // 不支持多行 url
        if (empty($sMsg)) {
            $sMsg = 'Please wait for a while...';
        }
        
        if (! headers_sent()) {
            if (0 == $nTime) {
                header("Location:" . $sUrl);
            } else {
                header("refresh:{$nTime};url={$sUrl}");
                include dirname(__DIR__) . '/bootstrap/template/url.php'; // 包含跳转页面模板
            }
            exit();
        } else {
            $sHeader = "<meta http-equiv='Refresh' content='{$nTime};URL={$sUrl}'>";
            if ($nTime == 0) {
                $sHeader = '';
            }
            include dirname(__DIR__) . '/bootstrap/template/url.php'; // 包含跳转页面模板
            exit();
        }
    }
    
    /**
     * 穿越中间件
     *
     * @param \queryyetsimple\pipeline\ipipeline $objPipeline
     * @param \queryyetsimple\http\request $objPassed
     * @param array $arrPassedExtend
     * @return void
     */
    public function throughMiddleware(ipipeline $objPipeline, request $objPassed, array $arrPassedExtend = [])
    {
        if (is_null($this->arrCurrentMiddleware)) {
            $this->arrCurrentMiddleware = $this->getMiddleware($this->packageNode());
        }
        
        if (! $this->arrCurrentMiddleware) {
            return;
        }
        
        $arrCurrentMiddleware = $this->arrCurrentMiddleware;
        $strMethod = ! $arrPassedExtend ? 'handle' : 'terminate';
        $arrCurrentMiddleware = array_map(function ($strItem) use($strMethod)
        {
            if (! method_exists($strItem, $strMethod)) {
                return '';
            }
            
            if (strpos($strItem, ':') === false) {
                return $strItem . '@' . $strMethod;
            } else {
                return str_replace(':', '@' . $strMethod . ':', $strItem);
            }
        }, $arrCurrentMiddleware);
        $arrCurrentMiddleware = array_filter($arrCurrentMiddleware);
        
        if ($arrCurrentMiddleware) {
            $objPipeline->send($objPassed)->sendExtend($arrPassedExtend)->through($arrCurrentMiddleware)->then(function ($objPassed)
            {});
        }
    }
    
    /**
     * 导入路由规则
     *
     * @param mixed $mixRouter
     * @param string $strUrl
     * @param arra $arrOption
     * @sub string domain 域名
     * @sub array params 参数
     * @sub array where 参数正则
     * @sub boolean prepend 插入顺序
     * @sub boolean strict 严格模式，启用将在匹配正则 $
     * @sub string prefix 前缀
     * @return void
     */
    public function import($mixRouter, $strUrl = '', $arrOption = [])
    {
        if (! $this->checkExpired()) {
            return;
        }
        
        $arrOption = $this->mergeOption([
            'prepend' => false, 
            'where' => [], 
            'params' => [], 
            'domain' => '', 
            'prefix' => ''
        ], $this->mergeOption($this->arrGroupArgs, $arrOption));
        
        // 支持数组传入
        if (! is_array($mixRouter) || count($mixRouter) == count($mixRouter, 1)) {
            $strTemp = $mixRouter;
            $mixRouter = [];
            if (is_string($strTemp)) {
                $mixRouter[] = [
                    $strTemp, 
                    $strUrl, 
                    $arrOption
                ];
            } else {
                if ($strUrl || ! empty($strTemp[1])) {
                    $mixRouter[] = [
                        $strTemp[0], 
                        (! empty($strTemp[1]) ? $strTemp[1] : $strUrl), 
                        $arrOption
                    ];
                }
            }
        } else {
            foreach ($mixRouter as $intKey => $arrRouter) {
                if (! is_array($arrRouter) || count($arrRouter) < 2) {
                    continue;
                }
                if (! isset($arrRouter[2])) {
                    $arrRouter[2] = [];
                }
                if (! $arrRouter[1]) {
                    $arrRouter[1] = $strUrl;
                }
                $arrRouter[2] = $this->mergeOption($arrOption, $arrRouter[2]);
                $mixRouter[$intKey] = $arrRouter;
            }
        }
        
        foreach ($mixRouter as $arrArgs) {
            $strPrefix = ! empty($arrArgs[2]['prefix']) ? $arrArgs[2]['prefix'] : '';
            $arrArgs[0] = $strPrefix . $arrArgs[0];
            
            $arrRouter = [
                'url' => $arrArgs[1], 
                'regex' => $arrArgs[0], 
                'params' => $arrArgs[2]['params'], 
                'where' => $this->arrWheres, 
                'domain' => $arrArgs[2]['domain']
            ];
            
            if (isset($arrArgs[2]['strict'])) {
                $arrRouter['strict'] = $arrArgs[2]['strict'];
            }
            
            // 合并参数正则
            if (! empty($arrArgs[2]['where']) && is_array($arrArgs[2]['where'])) {
                $arrRouter['where'] = $this->mergeWhere($arrRouter['where'], $arrArgs[2]['where']);
            }
            
            if (! isset($this->arrRouters[$arrArgs[0]])) {
                $this->arrRouters[$arrArgs[0]] = [];
            }
            
            // 优先插入
            if ($arrArgs[2]['prepend'] === true) {
                array_unshift($this->arrRouters[$arrArgs[0]], $arrRouter);
            } else {
                array_push($this->arrRouters[$arrArgs[0]], $arrRouter);
            }
            
            // 域名支持
            if (! empty($arrRouter['domain'])) {
                $arrOption['router'] = true;
                $this->domain($arrRouter['domain'], $arrArgs[0], $arrOption);
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
    public function regex($mixRegex, $strValue = '')
    {
        if (! $this->checkExpired()) {
            return;
        }
        
        if (is_string($mixRegex)) {
            $this->arrWheres[$mixRegex] = $strValue;
        } else {
            $this->arrWheres = $this->mergeWhere($this->arrWheres, $mixRegex);
        }
    }
    
    /**
     * 注册全局域名参数正则
     *
     * @param mixed $mixRegex
     * @param string $strValue
     * @return void
     */
    public function regexDomain($mixRegex, $strValue = '')
    {
        if (! $this->checkExpired()) {
            return;
        }
        
        if (is_string($mixRegex)) {
            $this->arrDomainWheres[$mixRegex] = $strValue;
        } else {
            $this->arrDomainWheres = $this->mergeWhere($this->arrDomainWheres, $mixRegex);
        }
    }
    
    /**
     * 注册域名
     *
     * @param string $strDomain
     * @param mixed $mixUrl
     * @param array $arrOption
     * @sub array params 扩展参数
     * @sub array domain_where 域名参数
     * @sub boolean prepend 插入顺序
     * @sub string router 对应路由规则
     * @return void
     */
    public function domain($strDomain, $mixUrl, $arrOption = [])
    {
        if (! $this->checkExpired()) {
            return;
        }
        
        $arrOption = $this->mergeOption([
            'prepend' => false, 
            'params' => [], 
            'domain_where' => [], 
            'router' => false
        ], $arrOption);
        
        // 闭包直接转接到分组
        if ($mixUrl instanceof Closure) {
            $arrOption['domain'] = $strDomain;
            $this->group($arrOption, $mixUrl);
        }         

        // 注册域名
        else {
            $arrDomain = [
                'url' => $mixUrl, 
                'params' => $arrOption['params'], 
                'router' => $arrOption['router']
            ];
            
            // 合并参数正则
            $arrDomainWheres = $this->arrDomainWheres;
            if (! empty($arrOption['domain_where']) && is_array($arrOption['domain_where'])) {
                $arrDomainWheres = $this->mergeWhere($arrOption['domain_where'], $arrDomainWheres);
            }
            
            // 主域名只有一个，路由可以有多个
            $strDomainBox = $arrDomain['router'] === false ? 'main' : 'rule';
            if (! isset($this->arrDomains[$strDomain])) {
                $this->arrDomains[$strDomain] = [];
            }
            $this->arrDomains[$strDomain]['domain_where'] = $arrDomainWheres;
            if (! isset($this->arrDomains[$strDomain][$strDomainBox])) {
                $this->arrDomains[$strDomain][$strDomainBox] = [];
            }
            
            // 纯域名绑定只支持一个，可以被覆盖
            if ($arrDomain['router'] === false) {
                $this->arrDomains[$strDomain][$strDomainBox] = $arrDomain;
            } else {
                // 优先插入
                if ($arrOption['prepend'] === true) {
                    array_unshift($this->arrDomains[$strDomain][$strDomainBox], $arrDomain);
                } else {
                    array_push($this->arrDomains[$strDomain][$strDomainBox], $arrDomain);
                }
            }
        }
    }
    
    /**
     * 注册分组路由
     *
     * @param array $arrOption
     * @sub string prefix 前缀
     * @sub string domain 域名
     * @sub array params 参数
     * @sub array where 参数正则
     * @sub boolean prepend 插入顺序
     * @sub boolean strict 严格模式，启用将在匹配正则 $
     * @param mixed $mixRouter
     * @return void
     */
    public function group(array $arrOption, $mixRouter)
    {
        if (! $this->checkExpired()) {
            return;
        }
        
        $this->arrGroupArgs = $arrOption = $this->mergeOption($this->arrGroupArgs, $arrOption);
        
        if ($mixRouter instanceof Closure) {
            call_user_func_array($mixRouter, []);
        } else {
            if (! is_array(current($mixRouter))) {
                $mixRouter = [
                    $mixRouter
                ];
            }
            foreach ($mixRouter as $arrVal) {
                if (! is_array($arrVal) || count($arrVal) < 2) {
                    continue;
                }
                
                if (! isset($arrVal[2])) {
                    $arrVal[2] = [];
                }
                
                $strPrefix = ! empty($arrArgs[2]['prefix']) ? $arrArgs[2]['prefix'] : (! empty($this->arrGroupArgs['prefix']) ? $this->arrGroupArgs['prefix'] : '');
                
                $this->import($strPrefix . $arrVal[0], $arrVal[1], $this->mergeOption($arrOption, $arrVal[2]));
            }
        }
        
        $this->arrGroupArgs = [];
    }
    
    /**
     * 导入路由配置数据
     *
     * @param array $arrData
     * @return void
     */
    public function importCache($arrData)
    {
        if (! $this->checkExpired()) {
            return;
        }
        
        if (isset($arrData['~domains~'])) {
            foreach ($arrData['~domains~'] as $arrVal) {
                if (is_array($arrVal) && isset($arrVal[1])) {
                    empty($arrVal[2]) && $arrVal[2] = [];
                    $this->domain($arrVal[0], $arrVal[1], $arrVal[2]);
                }
            }
            unset($arrData['~domains~']);
        }
        
        if ($arrData) {
            $this->import($arrData);
        }
    }
    
    /**
     * 获取绑定资源
     *
     * @param string $sBindName
     * @return mixed
     */
    public function getBind($sBindName)
    {
        return isset($this->arrBinds[$sBindName]) ? $this->arrBinds[$sBindName] : null;
    }
    
    /**
     * 判断是否绑定资源
     *
     * @param string $sBindName
     * @return boolean
     */
    public function hasBind($sBindName)
    {
        return isset($this->arrBinds[$sBindName]);
    }
    
    /**
     * 注册绑定资源
     *
     * @param mixed $mixBind
     * @param string $sController
     * @param string $sAction
     * @param string $sApp
     * @return mixed|void
     */
    public function bind($mixBindName = null, $mixBind = null)
    {
        $sController = $sAction = $sApp = null;
        if ($mixBindName) {
            list($sController, $sAction, $sApp) = $this->parseNode($mixBindName);
        }
        $sBindName = $this->packageNode($sController, $sAction, $sApp);
        
        if (is_null($mixBind)) {
            return $this->arrBinds[$sBindName] = $this->parseDefaultBind($sController, $sAction, $sApp);
        }
        
        if (! is_null($sAction)) {
            return $this->arrBinds[$sBindName] = $mixBind;
        } else {
            ! $sAction = $sAction = $this->action();
            
            switch (true) {
                // 判断是否为回调
                case is_callable($mixBind):
                    return $this->arrBinds[$sBindName] = $mixBind;
                    break;
                
                // 如果为方法则注册为方法
                case is_object($mixBind) && (method_exists($mixBind, 'run') || $mixBind instanceof iaction):
                    return $this->arrBinds[$sBindName] = [
                        $mixBind, 
                        'run'
                    ];
                    break;
                
                // 如果为控制器实例，注册为回调
                case $mixBind instanceof icontroller:
                // 实例回调
                case is_object($mixBind):
                // 静态类回调
                case is_string($mixBind) && is_callable([
                    $mixBind, 
                    $sAction
                ]):
                    return $this->arrBinds[$sBindName] = [
                        $mixBind, 
                        $sAction
                    ];
                    break;
                
                // 数组支持,方法名即数组的键值,注册方法
                case is_array($mixBind):
                    if (isset($mixBind[$sAction])) {
                        return $this->arrBinds[$sBindName] = $mixBind[$sAction];
                    } else {
                        throw new InvalidArgumentException(sprintf('The method %s of controller %s is not registered.', $sAction, $sController));
                    }
                    break;
                
                // 简单数据直接输出
                case is_scalar($mixBind):
                    return $this->arrBinds[$sBindName] = $mixBind;
                    break;
                
                default:
                    throw new InvalidArgumentException('The type of registered controller is not supported.');
                    break;
            }
        }
    }
    
    /**
     * 执行绑定
     *
     * @param string $sController
     * @param string $sAction
     * @param string $sApp
     * @param boolean $booForChild
     * @return mixed|void
     */
    public function doBind($sController = null, $sAction = null, $sApp = null, $booForChild = false)
    {
        if (is_null($sController)) {
            $sController = $this->controller();
        }
        
        if (is_null($sAction)) {
            $sAction = $this->action();
        }
        
        if (is_null($sApp)) {
            $sApp = $this->app();
        }
        
        if (! ($mixAction = $this->getBind($this->packageNode($sController, $sAction, $sApp))) && ! ($mixAction = $this->bind($this->packageNode($sController, $sAction, $sApp)))) {
            throw new InvalidArgumentException(sprintf('The method %s of controller %s is not registered.', $sAction, $sController));
        }
        
        switch (true) {
            // 判断是否为控制器回调
            case is_array($mixAction) && isset($mixAction[1]) && $mixAction[0] instanceof icontroller:
                try {
                    $objClass = new ReflectionMethod($mixAction[0], $mixAction[1]);
                    if ($objClass->isPublic() && ! $objClass->isStatic()) {
                        return $this->objContainer->call($mixAction, $this->arrVariable);
                    } else {
                        throw new InvalidArgumentException(sprintf('The method %s of controller %s is not registered.', $sAction, $sController));
                    }
                } catch (ReflectionException $oE) {
                    if ($booForChild === false) {
                        // 请求默认子方法器
                        return call_user_func_array([
                            $mixAction[0], 
                            'action'
                        ], [
                            $mixAction[1]
                        ]);
                    } else {
                        throw new InvalidArgumentException(sprintf('The method %s of controller %s is not registered.', $sAction, $sController));
                    }
                }
                break;
            
            // 判断是否为回调
            case is_callable($mixAction):
                return $this->objContainer->call($mixAction, $this->arrVariable);
                break;
            
            // 如果为方法则注册为方法
            case $mixAction instanceof iaction:
            case is_object($mixAction):
                if (method_exists($mixAction, 'run')) {
                    // 注册方法
                    $this->bind($this->packageNode($sController, $sAction, $sApp), [
                        $mixAction, 
                        'run'
                    ]);
                    return $this->doBind($sController, $sAction, $sApp);
                } else {
                    throw new InvalidArgumentException('The run method do not exits.');
                }
                break;
            
            // 数组支持,方法名即数组的键值,注册方法
            case is_array($mixAction):
                return $mixAction;
                break;
            
            // 简单数据直接输出
            case is_scalar($mixAction):
                return $mixAction;
                break;
            
            default:
                throw new InvalidArgumentException(sprintf('The registration method type %s is not supported.', $sAction));
                break;
        }
    }
    
    /**
     * 获取绑定的中间件
     *
     * @param string $sNode
     * @return mixed
     */
    public function getMiddleware($sNode)
    {
        $arrMiddleware = [];
        foreach ($this->arrMiddlewares as $sKey => $arrValue) {
            $sKey = helper::prepareRegexForWildcard($sKey, $this->getOption('middleware_strict'));
            if (preg_match($sKey, $sNode, $arrRes)) {
                $arrMiddleware = array_merge($arrMiddleware, $arrValue);
            }
        }
        return $arrMiddleware;
    }
    
    /**
     * 注册绑定中间件
     *
     * @param string $sMiddlewareName
     * @param string|array $mixMiddleware
     * @return void
     */
    public function middleware($sMiddlewareName, $mixMiddleware)
    {
        if (! $this->checkExpired()) {
            return;
        }
        
        if (! $mixMiddleware) {
            throw new InvalidArgumentException(sprintf('Middleware %s disallowed empty.', $sMiddlewareName));
        }
        
        if (! isset($this->arrMiddlewares[$sMiddlewareName])) {
            $this->arrMiddlewares[$sMiddlewareName] = [];
        }
        
        $this->arrMiddlewares[$sMiddlewareName] = array_merge($this->arrMiddlewares[$sMiddlewareName], $this->parseMiddlewares($mixMiddleware));
    }
    
    /**
     * 批量注册绑定中间件
     *
     * @param array $arrMiddleware
     * @return void
     */
    public function middlewares(array $arrMiddleware)
    {
        if (! $this->checkExpired()) {
            return;
        }
        
        foreach ($arrMiddleware as $sMiddlewareName => $mixMiddleware) {
            $this->middleware($sMiddlewareName, $mixMiddleware);
        }
    }
    
    /**
     * 获取绑定的 HTTP 方法
     *
     * @param string $sNode
     * @return mixed
     */
    public function getMethod($sNode)
    {
        if (array_key_exists($sNode, $this->arrMethods)) {
            return $this->arrMethods[$sNode];
        }
        
        $arrMethod = [];
        foreach ($this->arrMethods as $sKey => $arrValue) {
            $sKey = helper::prepareRegexForWildcard($sKey, $this->getOption('method_strict'));
            if (preg_match($sKey, $sNode, $arrRes)) {
                if ($arrMethod) {
                    $arrMethod = array_intersect($arrMethod, $arrValue);
                } else {
                    $arrMethod = $arrValue;
                }
            }
        }
        return $arrMethod;
    }
    
    /**
     * 注册绑定 HTTP 方法
     *
     * @param string $sMethodName
     * @param string|array $mixMethod
     * @return void
     */
    public function method($sMethodName, $mixMethod)
    {
        if (! $this->checkExpired()) {
            return;
        }
        
        if (! $mixMethod) {
            throw new InvalidArgumentException(sprintf('Method %s disallowed empty', $sMethodName));
        }
        
        if (! isset($this->arrMethods[$sMethodName])) {
            $this->arrMethods[$sMethodName] = [];
        }
        
        $mixMethod = ( array ) $mixMethod;
        
        $mixMethod = array_map(function ($strItem)
        {
            return strtoupper($strItem);
        }, $mixMethod);
        
        if (is_array($mixMethod)) {
            $this->arrMethods[$sMethodName] = array_merge($this->arrMethods[$sMethodName], $mixMethod);
        } else {
            $this->arrMethods[$sMethodName][] = $mixMethod;
        }
    }
    
    /**
     * 批量注册绑定 HTTP 方法
     *
     * @param array $arrMethod
     * @return void
     */
    public function methods($arrMethod)
    {
        if (! $this->checkExpired()) {
            return;
        }
        
        foreach ($arrMethod as $sMethod => $mixMethod) {
            $this->method($sMethod, $mixMethod);
        }
    }
    
    /**
     * web 分析 url 参数
     *
     * @return void
     */
    protected function parseWeb()
    {
        // 分析 pathinfo
        if ($this->getOption('model') == 'pathinfo') {
            // 分析 pathinfo
            $this->pathInfo();
            
            // 解析结果
            $_GET = array_merge($_GET, ($arrRouter = $this->parse()) ? $arrRouter : $this->parsePathInfo());
        }
    }
    
    /**
     * 验证 HTTP 方法
     *
     * @return void
     */
    protected function validateMethod()
    {
        $arrMethod = $this->getMethod($this->packageNode());
        if ($arrMethod && ! in_array($this->objRequest->method(), $arrMethod)) {
            throw new RuntimeException(sprintf('The node is allowed http method %s,but your current http method is %s', implode(',', $arrMethod), $this->objRequest->method()));
        }
    }
    
    /**
     * pathinfo 解析入口
     *
     * @return void
     */
    protected function pathInfo()
    {
        $sPathInfo = $this->clearHtmlSuffix($this->objRequest->pathinfo());
        $sPathInfo = empty($sPathInfo) ? '/' : $sPathInfo;
        $_SERVER['PATH_INFO'] = $sPathInfo;
    }
    
    /**
     * 分析 cli 参数
     *
     * @return void
     */
    protected function parseCli()
    {
        $arrArgv = isset($GLOBALS['argv']) ? $GLOBALS['argv'] : [];
        
        if (! isset($arrArgv) || empty($arrArgv)) {
            return;
        }
        
        // 第一个为脚本自身
        array_shift($arrArgv);
        
        // 继续分析
        if ($arrArgv) {
            
            // app
            if (is_array($this->getOption('~apps~')) && in_array($arrArgv[0], $this->getOption('~apps~'))) {
                $_GET[static::APP] = array_shift($arrArgv);
            }
            
            // controller
            if ($arrArgv) {
                $_GET[static::CONTROLLER] = array_shift($arrArgv);
            }
            
            // 方法
            if ($arrArgv) {
                $_GET[static::ACTION] = array_shift($arrArgv);
            }
            
            // 剩余参数
            if ($arrArgv) {
                for ($nI = 0, $nCnt = count($arrArgv); $nI < $nCnt; $nI ++) {
                    if (isset($arrArgv[$nI + 1])) {
                        $_GET[$arrArgv[$nI]] = ( string ) $arrArgv[++ $nI];
                    } elseif ($nI == 0) {
                        $_GET[$_GET[static::ACTION]] = ( string ) $arrArgv[$nI];
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
    protected function parsePathInfo()
    {
        $arrPathInfo = [
            static::ARGS => []
        ];
        
        $sPathInfo = $_SERVER['PATH_INFO'];
        $arrPaths = explode($this->getOption('pathinfo_depr'), trim($sPathInfo, '/'));
        
        if (is_array($this->getOption('~apps~')) && in_array($arrPaths[0], $this->getOption('~apps~'))) {
            $arrPathInfo[static::APP] = array_shift($arrPaths);
        }
        
        // 控制器名称
        if (isset($_GET[static::CONTROLLER])) {
            $arrPathInfo[static::CONTROLLER] = $_GET[static::CONTROLLER];
        }
        
        // 方法名称
        if (isset($_GET[static::ACTION])) {
            $arrPathInfo[static::ACTION] = $_GET[static::ACTION];
        }
        
        for ($nI = 0, $nCnt = count($arrPaths); $nI < $nCnt; $nI ++) {
            if (is_numeric($arrPaths[$nI]) || in_array($arrPaths[$nI], $this->getOption('args_protected')) || $this->matchArgs($arrPaths[$nI], $this->getOption('args_regex'))) {
                $arrPathInfo[static::ARGS][] = $arrPaths[$nI];
            } else {
                if (! isset($arrPathInfo[static::CONTROLLER])) {
                    $arrPathInfo[static::CONTROLLER] = $arrPaths[$nI];
                } elseif (! isset($arrPathInfo[static::ACTION])) {
                    $arrPathInfo[static::ACTION] = $arrPaths[$nI];
                } else {
                    if (isset($arrPaths[$nI + 1])) {
                        $arrPathInfo[$arrPaths[$nI]] = ( string ) $arrPaths[++ $nI];
                    } else {
                        $arrPathInfo[static::ARGS][] = $arrPaths[$nI];
                    }
                }
            }
        }
        
        return $arrPathInfo;
    }
    
    /**
     * 是否匹配参数正则
     *
     * @param array $strValue
     * @param array $arrRegex
     * @return boolean
     */
    protected function matchArgs($strValue, array $arrRegex = [])
    {
        if (! $arrRegex) {
            return false;
        }
        
        foreach ($arrRegex as $strRegex) {
            $strRegex = sprintf('/^(%s)%s/', $strRegex, $this->getOption('args_strict') ? '$' : '');
            if (preg_match($strRegex, $strValue, $arrRes)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * 解析域名路由
     *
     * @param array $arrNextParse
     * @return void
     */
    protected function parseDomain(&$arrNextParse)
    {
        if (! $this->arrDomains || ! $this->getOption('router_domain_top')) {
            return false;
        }
        
        $strHost = $this->objRequest->host();
        
        $booFindDomain = false;
        foreach ($this->arrDomains as $sKey => $arrDomains) {
            
            // 直接匹配成功
            if ($strHost === $sKey || $strHost === $sKey . '.' . $this->getOption('router_domain_top')) {
                $booFindDomain = true;
            }            

            // 域名参数支持
            elseif (strpos($sKey, '{') !== false) {
                if (strpos($sKey, $this->getOption('router_domain_top')) === false) {
                    $sKey = $sKey . '.' . $this->getOption('router_domain_top');
                }
                
                // 解析匹配正则
                $sKey = $this->formatRegex($sKey);
                $sKey = preg_replace_callback("/{(.+?)}/", function ($arrMatches) use(&$arrDomains)
                {
                    $arrDomains['args'][] = $arrMatches[1];
                    return '(' . (isset($arrDomains['domain_where'][$arrMatches[1]]) ? $arrDomains['domain_where'][$arrMatches[1]] : static::DEFAULT_REGEX) . ')';
                }, $sKey);
                $sKey = '/^' . $sKey . '$/';
                
                // 匹配结果
                if (preg_match($sKey, $strHost, $arrRes)) {
                    // 变量解析
                    if (isset($arrDomains['args'])) {
                        array_shift($arrRes);
                        foreach ($arrDomains['args'] as $intArgsKey => $strArgs) {
                            $this->arrDomainData[$strArgs] = $arrRes[$intArgsKey];
                            $this->objRequest->setRouter($strArgs, $arrRes[$intArgsKey]);
                            $this->arrVariable[$strArgs] = $arrRes[$intArgsKey];
                        }
                    }
                    
                    $booFindDomain = true;
                }
            }
            
            // 分析结果
            if ($booFindDomain === true) {
                if (isset($arrDomains['rule'])) {
                    $arrNextParse = $arrDomains['rule'];
                    return false;
                } else {
                    $arrData = $this->parseNodeUrl($arrDomains['main']['url']);
                    
                    // 额外参数[放入 GET]
                    if (is_array($arrDomains['main']['params']) && $arrDomains['main']['params']) {
                        $arrData = array_merge($arrData, $arrDomains['main']['params']);
                    }
                    
                    // 合并域名匹配数据
                    $arrData = array_merge($this->arrDomainData, $arrData);
                    
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
    protected function parseRouter($arrNextParse = [])
    {
        if (! $this->arrRouters) {
            return;
        }
        
        $arrData = [];
        $sPathinfo = $_SERVER['PATH_INFO'];
        
        // 匹配路由
        foreach ($this->arrRouters as $sKey => $arrRouters) {
            // 域名过滤掉无关路由
            if ($arrNextParse && ! in_array($sKey, $arrNextParse)) {
                continue;
            }
            
            foreach ($arrRouters as $arrRouter) {
                // 尝试匹配
                $booFindFouter = false;
                if ($arrRouter['regex'] == $sPathinfo) {
                    $booFindFouter = true;
                } else {
                    // 解析匹配正则
                    $arrRouter['regex'] = $this->formatRegex($arrRouter['regex']);
                    $arrRouter['regex'] = preg_replace_callback("/{(.+?)}/", function ($arrMatches) use(&$arrRouter)
                    {
                        $arrRouter['args'][] = $arrMatches[1];
                        return '(' . (isset($arrRouter['where'][$arrMatches[1]]) ? $arrRouter['where'][$arrMatches[1]] : static::DEFAULT_REGEX) . ')';
                    }, $arrRouter['regex']);
                    $arrRouter['regex'] = '/^\/' . $arrRouter['regex'] . ((isset($arrRouter['strict']) ? $arrRouter['strict'] : $this->getOption('router_strict')) ? '$' : '') . '/';
                    
                    // 匹配结果
                    if (preg_match($arrRouter['regex'], $sPathinfo, $arrRes)) {
                        $booFindFouter = true;
                    }
                }
                
                // 分析结果
                if ($booFindFouter === true) {
                    $arrData = $this->parseNodeUrl($arrRouter['url']);
                    
                    // 额外参数
                    if (is_array($arrRouter['params']) && $arrRouter['params']) {
                        $arrData = array_merge($arrData, $arrRouter['params']);
                    }
                    
                    // 变量解析
                    if (isset($arrRouter['args'])) {
                        array_shift($arrRes);
                        foreach ($arrRouter['args'] as $intArgsKey => $strArgs) {
                            $arrData[$strArgs] = $arrRes[$intArgsKey];
                            $this->objRequest->setRouter($strArgs, $arrRes[$intArgsKey]);
                            $this->arrVariable[$strArgs] = $arrRes[$intArgsKey];
                        }
                    }
                    break 2;
                }
            }
        }
        
        // 合并域名匹配数据
        $arrData = array_merge($this->arrDomainData, $arrData);
        
        return $arrData;
    }
    
    /**
     * 设置路由缓存地址
     *
     * @param string $strCachePath
     * @return $this
     */
    public function cachePath($strCachePath)
    {
        $this->strCachePath = $strCachePath;
        return $this;
    }
    
    /**
     * 设置 development
     *
     * @param boolean $booDevelopment
     * @return $this
     */
    public function development($booDevelopment)
    {
        $this->booDevelopment = $booDevelopment;
        return $this;
    }
    
    /**
     * 检查路由缓存是否过期
     *
     * @return boolean
     */
    public function checkExpired()
    {
        return $this->booDevelopment === true || ! $this->checkOpen() || ! is_file($this->strCachePath);
    }
    
    /**
     * 检查路由缓存是否开启
     *
     * @return boolean
     */
    public function checkOpen()
    {
        return $this->getOption('router_cache') && $this->strCachePath;
    }
    
    /**
     * 路由缓存
     *
     * @return void
     */
    protected function readCache()
    {
        if (! $this->checkOpen()) {
            return;
        }
        
        if ($this->booDevelopment === false && is_file($this->strCachePath)) {
            $arrCacheData = ( array ) include $this->strCachePath;
            $this->arrDomains = $arrCacheData['domains'];
            $this->arrRouters = $arrCacheData['routers'];
            $this->arrDomainWheres = $arrCacheData['domain_wheres'];
            $this->arrWheres = $arrCacheData['wheres'];
            $this->arrMiddlewares = $arrCacheData['middlewares'];
            $this->arrMethods = $arrCacheData['methods'];
            unset($arrCacheData);
            return;
        }
        
        $arrCacheData = [
            'domains' => $this->arrDomains, 
            'routers' => $this->arrRouters, 
            'domain_wheres' => $this->arrDomainWheres, 
            'wheres' => $this->arrWheres, 
            'middlewares' => $this->arrMiddlewares, 
            'methods' => $this->arrMethods
        ];
        
        if (! is_dir(dirname($this->strCachePath))) {
            fso::createDirectory(dirname($this->strCachePath));
        }
        
        if (! file_put_contents($this->strCachePath, '<?php return ' . var_export($arrCacheData, true) . '; ?>')) {
            throw new RuntimeException(sprintf('Dir %s do not have permission.', $this->strCachePath));
        }
        ! file_put_contents($this->strCachePath, '<?php /* ' . date('Y-m-d H:i:s') . ' */ ?>' . PHP_EOL . php_strip_whitespace($this->strCachePath));
        
        unset($arrCacheData);
    }
    
    /**
     * 格式化正则
     *
     * @param string $sRegex
     * @return string
     */
    protected function formatRegex($sRegex)
    {
        $sRegex = helper::escapeRegexCharacter($sRegex);
        
        // 还原变量特殊标记
        return str_replace([
            '\{', 
            '\}'
        ], [
            '{', 
            '}'
        ], $sRegex);
    }
    
    /**
     * 合并 option 参数
     *
     * @param array $arrOption
     * @param array $arrExtend
     * @return array
     */
    protected function mergeOption(array $arrOption, array $arrExtend)
    {
        // 合并特殊参数
        foreach ([
            'params', 
            'where', 
            'domain_where'
        ] as $strType) {
            if (! empty($arrExtend[$strType]) && is_array($arrExtend[$strType])) {
                if (! isset($arrOption[$strType])) {
                    $arrOption[$strType] = [];
                }
                $arrOption[$strType] = $this->mergeWhere($arrOption[$strType], $arrExtend[$strType]);
            }
        }
        
        // 合并额外参数
        foreach ([
            'prefix', 
            'domain', 
            'prepend', 
            'strict', 
            'router'
        ] as $strType) {
            if (isset($arrExtend[$strType])) {
                $arrOption[$strType] = $arrExtend[$strType];
            }
        }
        
        return $arrOption;
    }
    
    /**
     * 合并 where 正则参数
     *
     * @param array $arrWhere
     * @param array $arrExtend
     * @return array
     */
    protected function mergeWhere(array $arrWhere, array $arrExtend)
    {
        // 合并参数正则
        if (! empty($arrExtend) && is_array($arrExtend)) {
            if (is_string(key($arrExtend))) {
                $arrWhere = array_merge($arrWhere, $arrExtend);
            } else {
                $arrWhere[$arrExtend[0]] = $arrExtend[1];
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
    protected function parseNodeUrl($sUrl)
    {
        $arrData = [];
        
        // 解析 url
        if (strpos($sUrl, '://') === false) {
            $sUrl = 'QueryPHP://' . $sUrl;
        }
        $sUrl = parse_url($sUrl);
        
        // 应用
        if ($sUrl['scheme'] != 'QueryPHP') {
            $arrData[static::APP] = $sUrl['scheme'];
        }
        
        // 控制器
        $arrData[static::CONTROLLER] = $sUrl['host'];
        
        // 方法
        if (isset($sUrl['path']) && $sUrl['path'] != '/') {
            $arrData[static::ACTION] = ltrim($sUrl['path'], '/');
        }
        
        // 额外参数
        if (isset($sUrl['query'])) {
            foreach (explode('&', $sUrl['query']) as $strQuery) {
                $strQuery = explode('=', $strQuery);
                $arrData[$strQuery[0]] = $strQuery[1];
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
    protected function urlWithDomain($sDomain = '', $sHttpPrefix = '', $sHttpSuffix = '')
    {
        static $sHttpPrefix = '', $sHttpSuffix = '';
        if (! $sHttpPrefix) {
            $sHttpPrefix = $this->objRequest->isSsl() ? 'https://' : 'http://';
            $sHttpSuffix = $this->getOption('router_domain_top');
        }
        return $sHttpPrefix . ($sDomain && $sDomain != '*' ? $sDomain . '.' : '') . $sHttpSuffix;
    }
    
    /**
     * 完成请求
     *
     * @return void
     */
    protected function completeRequest()
    {
        if ($this->getOption('pathinfo_restful')) {
            $this->pathinfoRestful();
        }
        
        foreach ([
            'app', 
            'controller', 
            'action'
        ] as $strType) {
            $this->objContainer->instance($strType . '_name', $this->{$strType}());
            $this->objRequest->{'set' . ucfirst($strType)}($this->{$strType}());
        }
        $_REQUEST = array_merge($_POST, $_GET);
    }
    
    /**
     * 智能 restful 解析
     * 路由匹配失败后尝试智能化解析
     *
     * @return void
     */
    protected function pathinfoRestful()
    {
        switch ($this->objRequest->method()) {
            case 'GET':
                if (empty($_GET[static::ACTION])) {
                    $_GET[static::ACTION] = ! empty($_GET['args']) ? 'show' : '';
                }
                break;
            case 'POST':
                if (empty($_GET[static::ACTION])) {
                    $_GET[static::ACTION] = 'store';
                }
                break;
            case 'PUT':
                if (empty($_GET[static::ACTION])) {
                    $_GET[static::ACTION] = 'update';
                }
                break;
            case 'DELETE':
                if (empty($_GET[static::ACTION])) {
                    $_GET[static::ACTION] = 'destroy';
                }
                break;
        }
    }
    
    /**
     * 解析项目公共和基础路径
     *
     * @return void
     */
    protected function parsePublicAndRoot()
    {
        if ($this->objRequest->isCli()) {
            return;
        }
        
        if (! $this->objContainer['url_enter']) {
            $this->objContainer->instance('url_enter', $this->getOption('rewrite') === true ? $this->objRequest->enterRewrite() : $this->objRequest->enter());
        } else {
            $this->objRequest->setEnter($this->objContainer['url_enter']);
        }
        
        if (! $this->objContainer['url_root']) {
            $this->objContainer->instance('url_root', $this->objRequest->root());
        } else {
            $this->objRequest->setRoot($this->objContainer['url_root']);
        }
        
        if (! $this->objContainer['url_public']) {
            $this->objRequest->setPublics($this->getOption('public'));
            $this->objContainer->instance('url_public', $this->objRequest->publics());
        } else {
            $this->objRequest->setPublics($this->objContainer['url_public']);
        }
    }
    
    /**
     * 清理 url 后缀
     *
     * @param string $sVal
     * @return string
     */
    protected function clearHtmlSuffix($sVal)
    {
        if ($this->getOption('html_suffix') && ! empty($sVal)) {
            $sSuffix = substr($this->getOption('html_suffix'), 1);
            $sVal = preg_replace('/\.' . $sSuffix . '$/', '', $sVal);
        }
        return $sVal;
    }
    
    /**
     * 分析默认绑定
     *
     * @param string $sController
     * @param string $sAction
     * @param string $sApp
     * @return false|callable
     */
    protected function parseDefaultBind($sController = null, $sAction = null, $sApp = null)
    {
        if (is_null($sController)) {
            $sController = $this->controller();
        }
        
        if (is_null($sAction)) {
            $sAction = $this->action();
        }
        
        if (is_null($sApp)) {
            $sApp = $this->app();
        }
        
        // 尝试读取默认控制器
        $sControllerClass = '\\' . $sApp . '\\' . $this->getOption('controller_dir') . '\\' . $sController;
        $booFindController = false;
        if (class_exists($sControllerClass)) {
            $booFindController = true;
        }
        
        // 尝试直接读取方法类
        $sActionClass = '\\' . $sApp . '\\' . $this->getOption('controller_dir') . '\\' . $sController . '\\' . $sAction;
        if (class_exists($sActionClass)) {
            if (! $booFindController) {
                throw new RuntimeException(sprintf('Parent controller %s must be set', $sControllerClass));
            }
            
            return [
                $this->objContainer->make($sActionClass, $this->arrVariable)->setController($this->objContainer->make($sControllerClass, $this->arrVariable)->setView($this->objContainer['view'])->setRouter($this)), 
                'run'
            ];
        } elseif ($booFindController === true) {
            return [
                $this->objContainer->make($sControllerClass, $this->arrVariable)->setView($this->objContainer['view'])->setRouter($this), 
                $sAction
            ];
        }
        
        return false;
    }
    
    /**
     * 取得打包节点
     *
     * @param string $strApp
     * @param string $strController
     * @param string $strAction
     * @return string
     */
    protected function packageNode($strController = null, $strAction = null, $strApp = null)
    {
        return ($strApp ?  : $this->app()) . '://' . ($strController ?  : $this->controller()) . '/' . ($strAction ?  : $this->action());
    }
    
    /**
     * 分析节点
     *
     * @param string $strApp
     * @return arrat
     */
    protected function parseNode($strNode)
    {
        $sController = $sAction = $sApp = null;
        $arrTemp = $this->parseNodeUrl($strNode);
        
        if (! empty($arrTemp[static::APP]) && $arrTemp[static::APP] != '*') {
            $sApp = $arrTemp[static::APP];
        }
        if (! empty($arrTemp[static::CONTROLLER]) && $arrTemp[static::CONTROLLER] != '*') {
            $sController = $arrTemp[static::CONTROLLER];
        }
        if (! empty($arrTemp[static::ACTION]) && $arrTemp[static::ACTION] != '*') {
            $sAction = $arrTemp[static::ACTION];
        }
        
        unset($arrTemp);
        
        return [
            $sController ?  : $this->controller(), 
            $sAction ?  : $this->action(), 
            $sApp ?  : $this->app()
        ];
    }
    
    /**
     * 解析中间件
     *
     * @param string|array $mixMiddleware
     * @return array
     */
    protected function parseMiddlewares($mixMiddleware)
    {
        $arrMiddleware = [];
        foreach (( array ) $mixMiddleware as $strTemp) {
            if (! is_string($strTemp)) {
                throw new InvalidArgumentException('Middleware only allowed string.');
            }
            
            $strParams = '';
            if (strpos($strTemp, ':') !== false) {
                list($strTemp, $strParams) = explode(':', $strTemp);
            }
            
            if (isset($this->getOption('middleware_group')[$strTemp])) {
                foreach (( array ) $this->getOption('middleware_group')[$strTemp] as $strTempTwo) {
                    $strParams = '';
                    if (strpos($strTempTwo, ':') !== false) {
                        list($strTempTwo, $strParams) = explode(':', $strTempTwo);
                    }
                    
                    if (isset($this->getOption('middleware_alias')[$strTempTwo])) {
                        $arrMiddleware[] = $this->explodeMiddlewareName($this->getOption('middleware_alias')[$strTempTwo], $strParams);
                    } else {
                        $arrMiddleware[] = $this->explodeMiddlewareName($strBackupTempTwo, $strParams);
                    }
                }
            } elseif (isset($this->getOption('middleware_alias')[$strTemp])) {
                $arrMiddleware[] = $this->explodeMiddlewareName($this->getOption('middleware_alias')[$strTemp], $strParams);
            } else {
                $arrMiddleware [] = $this->explodeMiddlewareName($strTemp, $strParams);
            }
        }

        return $arrMiddleware;
    }

    /**
     * 中间件名字
     *
     * @param string $strMiddleware
     * @param string $strParams
     * @return string
     */
    protected function explodeMiddlewareName($strMiddleware, $strParams)
    {
        return $strMiddleware . ($strParams ? ':' . $strParams : '');
    }
}
