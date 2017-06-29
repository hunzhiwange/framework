<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\http;

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

use ArrayAccess;
use RuntimeException;
use queryyetsimple\flow\control;
use queryyetsimple\classs\option;
use queryyetsimple\classs\infinity;
use queryyetsimple\cookie\interfaces\cookie;
use queryyetsimple\session\interfaces\store;
use queryyetsimple\support\interfaces\arrayable;

/**
 * http 请求
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2016.11.19
 * @version 1.0
 */
class request implements arrayable, ArrayAccess {
    
    use control;
    use option;
    use infinity;
    
    /**
     * cookie 存储
     *
     * @var \queryyetsimple\cookie\interfaces\cookie
     */
    protected $objCookie = null;
    
    /**
     * session 存储
     *
     * @var \queryyetsimple\session\interfaces\store
     */
    protected $objSession = null;
    
    /**
     * 当前 url
     *
     * @var string
     */
    protected $strUrl = null;
    
    /**
     * 基础 url
     *
     * @var string
     */
    protected $sBaseUrl = null;
    
    /**
     * 请求 url
     *
     * @var string
     */
    protected $sRequestUrl = null;
    
    /**
     * 请求类型
     *
     * @var string
     */
    protected $strMethod = null;
    
    /**
     * 实际请求类型
     *
     * @var string
     */
    protected $strMethodReal = null;
    
    /**
     * 域名
     *
     * @var string
     */
    protected $strDomain = null;
    
    /**
     * HOST
     *
     * @var string
     */
    protected $strHost = null;
    
    /**
     * 入口文件
     *
     * @var string
     */
    protected $strEnter = null;
    
    /**
     * root
     *
     * @var string
     */
    protected $strRoot = null;
    
    /**
     * public
     *
     * @var string
     */
    protected $strPublic = null;
    
    /**
     * 应用名字
     *
     * @var string
     */
    protected $strApp = null;
    
    /**
     * 控制器名字
     *
     * @var string
     */
    protected $strController = null;
    
    /**
     * 方法名字
     *
     * @var string
     */
    protected $strAction = null;
    
    /**
     * ALL
     *
     * @var array
     */
    protected $arrAll = null;
    
    /**
     * GET
     *
     * @var array
     */
    protected $arrGet = null;
    
    /**
     * POST
     *
     * @var array
     */
    protected $arrPost = null;
    
    /**
     * REQUEST
     *
     * @var array
     */
    protected $arrRequest = null;
    
    /**
     * COOKIE
     *
     * @var array
     */
    protected $arrCookie = null;
    
    /**
     * SESSION
     *
     * @var array
     */
    protected $arrSession = null;
    
    /**
     * SERVER
     *
     * @var array
     */
    protected $arrServer = null;
    
    /**
     * ENV
     *
     * @var array
     */
    protected $arrEnv = null;
    
    /**
     * PUT
     *
     * @var array
     */
    protected $arrPut = null;
    
    /**
     * FILES
     *
     * @var array
     */
    protected $arrFiles = null;
    
    /**
     * HEADER
     *
     * @var array
     */
    protected $arrHeader = null;
    
    /**
     * 当前语言
     *
     * @var string
     */
    protected $sLangset = null;
    
    /**
     * 配置
     *
     * @var array
     */
    protected $arrOption = [ 
            'var_method' => '_method',
            'var_ajax' => '_ajax',
            'var_pjax' => '_pjax' 
    ];
    
    /**
     * 构造函数
     *
     * @param \queryyetsimple\session\interfaces\store $objSession            
     * @param \queryyetsimple\cookie\interfaces\cookie $objCookie            
     * @return void
     */
    public function __construct(store $objSession, cookie $objCookie, array $arrOption = []) {
        $this->objSession = $objSession;
        $this->objCookie = $objCookie;
        $this->options ( $arrOption );
    }
    
    /**
     * all 参数
     *
     * @param string $sKey            
     * @param mixed $mixDefault            
     * @param string|array $mixFilter            
     * @param boolean $booFile            
     * @return mixed
     */
    public function all($sKey, $mixDefault = null, $mixFilter = null, $booFile = false) {
        return $this->input ( $sKey, $mixDefault, $mixFilter, null, $this->globalAll ( $booFile ) );
    }
    
    /**
     * 批量 all 参数
     *
     * @param array $arrKey            
     * @param mixed $mixDefault            
     * @param string|array $mixFilter            
     * @param boolean $booFile            
     * @return array
     */
    public function alls(array $arrKey, $mixDefault = null, $mixFilter = null, $booFile = false) {
        $arrValue = [ ];
        foreach ( $arrKey as $strKey )
            $arrValue [] = $this->all ( $strKey, $mixDefault, $mixFilter, $booFile );
        return $arrValue;
    }
    
    /**
     * 全部 all 参数
     *
     * @param mixed $mixDefault            
     * @param string|array $mixFilter            
     * @return array
     */
    public function allAll($mixDefault = null, $mixFilter = null, $booFile = false) {
        return $this->inputAll ( $mixDefault, $mixFilter, null, $this->globalAll ( $booFile ) );
    }
    
    /**
     * arg 参数
     *
     * @param string $sKey            
     * @param mixed $mixDefault            
     * @param string|array $mixFilter            
     * @return mixed
     */
    public function arg($sKey, $mixDefault = null, $mixFilter = null) {
        return $this->input ( $sKey, $mixDefault, $mixFilter, null );
    }
    
    /**
     * 批量 arg 参数
     *
     * @param string $sKey            
     * @param mixed $mixDefault            
     * @param string|array $mixFilter            
     * @return array
     */
    public function args(array $arrKey, $mixDefault = null, $mixFilter = null) {
        return $this->inputs ( $arrKey, $mixDefault, $mixFilter, null );
    }
    
    /**
     * 全部 arg 参数
     *
     * @param mixed $mixDefault            
     * @param string|array $mixFilter            
     * @return array
     */
    public function argAll($mixDefault = null, $mixFilter = null) {
        return $this->inputAll ( $mixDefault, $mixFilter, null );
    }
    
    /**
     * 设置 arg 参数
     *
     * @param string $sKey            
     * @param mixed $mixValue            
     * @param string $sType            
     * @return $this
     */
    public function setArg($sKey, $mixValue) {
        return $this->setInput ( $sKey, $mixValue, null );
    }
    
    /**
     * 批量设置 arg 参数
     *
     * @param array $arrValue            
     * @param string $sType            
     * @return $this
     */
    public function setArgs(array $arrValue, $mixValue) {
        return $this->setInputs ( $arrValue, null );
    }
    
    /**
     * get 参数
     *
     * @param string $sKey            
     * @param mixed $mixDefault            
     * @param string|array $mixFilter            
     * @return mixed
     */
    public function get($sKey, $mixDefault = null, $mixFilter = null) {
        return $this->input ( $sKey, $mixDefault, $mixFilter, 'get' );
    }
    
    /**
     * 批量 get 参数
     *
     * @param string $sKey            
     * @param mixed $mixDefault            
     * @param string|array $mixFilter            
     * @return array
     */
    public function gets(array $arrKey, $mixDefault = null, $mixFilter = null) {
        return $this->inputs ( $arrKey, $mixDefault, $mixFilter, 'get' );
    }
    
    /**
     * 全部 get 参数
     *
     * @param mixed $mixDefault            
     * @param string|array $mixFilter            
     * @return array
     */
    public function getAll($mixDefault = null, $mixFilter = null) {
        return $this->inputAll ( $mixDefault, $mixFilter, 'get' );
    }
    
    /**
     * 设置 get 参数
     *
     * @param string $sKey            
     * @param mixed $mixValue            
     * @param string $sType            
     * @return $this
     */
    public function setGet($sKey, $mixValue) {
        return $this->setInput ( $sKey, $mixValue, 'get' );
    }
    
    /**
     * 批量设置 get 参数
     *
     * @param array $arrValue            
     * @param string $sType            
     * @return $this
     */
    public function setGets(array $arrValue, $mixValue) {
        return $this->setInputs ( $arrValue, 'post' );
    }
    
    /**
     * post 参数
     *
     * @param string $sKey            
     * @param mixed $mixDefault            
     * @param string|array $mixFilter            
     * @return mixed
     */
    public function post($sKey, $mixDefault = null, $mixFilter = null) {
        return $this->input ( $sKey, $mixDefault, $mixFilter, 'post' );
    }
    
    /**
     * 批量 post 参数
     *
     * @param string $sKey            
     * @param mixed $mixDefault            
     * @param string|array $mixFilter            
     * @return array
     */
    public function posts(array $arrKey, $mixDefault = null, $mixFilter = null) {
        return $this->inputs ( $arrKey, $mixDefault, $mixFilter, 'post' );
    }
    /**
     * 全部 post 参数
     *
     * @param mixed $mixDefault            
     * @param string|array $mixFilter            
     * @return array
     */
    public function postAll($mixDefault = null, $mixFilter = null) {
        return $this->inputAll ( $mixDefault, $mixFilter, 'post' );
    }
    
    /**
     * 设置 post 参数
     *
     * @param string $sKey            
     * @param mixed $mixValue            
     * @param string $sType            
     * @return $this
     */
    public function setPost($sKey, $mixValue) {
        return $this->setInput ( $sKey, $mixValue, 'post' );
    }
    
    /**
     * 批量设置 post 参数
     *
     * @param array $arrValue            
     * @param string $sType            
     * @return $this
     */
    public function setPosts(array $arrValue, $mixValue) {
        return $this->setInputs ( $arrValue, 'post' );
    }
    
    /**
     * request 参数
     *
     * @param string $sKey            
     * @param mixed $mixDefault            
     * @param string|array $mixFilter            
     * @return mixed
     */
    public function request($sKey, $mixDefault = null, $mixFilter = null) {
        return $this->input ( $sKey, $mixDefault, $mixFilter, 'request' );
    }
    
    /**
     * 批量 request 参数
     *
     * @param string $sKey            
     * @param mixed $mixDefault            
     * @param string|array $mixFilter            
     * @return array
     */
    public function requests(array $arrKey, $mixDefault = null, $mixFilter = null) {
        return $this->inputs ( $arrKey, $mixDefault, $mixFilter, 'request' );
    }
    
    /**
     * 全部 request 参数
     *
     * @param mixed $mixDefault            
     * @param string|array $mixFilter            
     * @return array
     */
    public function requestAll($mixDefault = null, $mixFilter = null) {
        return $this->inputAll ( $mixDefault, $mixFilter, 'request' );
    }
    
    /**
     * 设置 request 参数
     *
     * @param string $sKey            
     * @param mixed $mixValue            
     * @param string $sType            
     * @return $this
     */
    public function setRequest($sKey, $mixValue) {
        return $this->setInput ( $sKey, $mixValue, 'request' );
    }
    
    /**
     * 批量设置 request 参数
     *
     * @param array $arrValue            
     * @param string $sType            
     * @return $this
     */
    public function setRequests(array $arrValue, $mixValue) {
        return $this->setInputs ( $arrValue, 'request' );
    }
    
    /**
     * cookie 参数
     *
     * @param string $sKey            
     * @param mixed $mixDefault            
     * @param string|array $mixFilter            
     * @return mixed
     */
    public function cookie($sKey, $mixDefault = null, $mixFilter = null) {
        return $this->input ( $sKey, $mixDefault, $mixFilter, 'cookie' );
    }
    
    /**
     * 批量 cookie 参数
     *
     * @param string $sKey            
     * @param mixed $mixDefault            
     * @param string|array $mixFilter            
     * @return array
     */
    public function cookies(array $arrKey, $mixDefault = null, $mixFilter = null) {
        return $this->inputs ( $arrKey, $mixDefault, $mixFilter, 'cookie' );
    }
    
    /**
     * 全部 cookie 参数
     *
     * @param mixed $mixDefault            
     * @param string|array $mixFilter            
     * @return array
     */
    public function cookieAll($mixDefault = null, $mixFilter = null) {
        return $this->inputAll ( $mixDefault, $mixFilter, 'cookie' );
    }
    
    /**
     * 设置 cookie 参数
     *
     *
     * @param string $sKey            
     * @param mixed $mixValue            
     * @param string $sType            
     * @return $this
     */
    public function setCookie($sKey, $mixValue) {
        return $this->setInput ( $sKey, $mixValue, 'cookie' );
    }
    
    /**
     * 批量设置 cookie 参数
     *
     * @param array $arrValue            
     * @param string $sType            
     * @return $this
     */
    public function setCookies(array $arrValue, $mixValue) {
        return $this->setInputs ( $arrValue, 'cookie' );
    }
    
    /**
     * session 参数
     *
     * @param string $sKey            
     * @param mixed $mixDefault            
     * @param string|array $mixFilter            
     * @return mixed
     */
    public function session($sKey, $mixDefault = null, $mixFilter = null) {
        return $this->input ( $sKey, $mixDefault, $mixFilter, 'session' );
    }
    
    /**
     * 批量 session 参数
     *
     * @param string $sKey            
     * @param mixed $mixDefault            
     * @param string|array $mixFilter            
     * @return array
     */
    public function sessions(array $arrKey, $mixDefault = null, $mixFilter = null) {
        return $this->inputs ( $arrKey, $mixDefault, $mixFilter, 'session' );
    }
    
    /**
     * 全部 session 参数
     *
     * @param mixed $mixDefault            
     * @param string|array $mixFilter            
     * @return array
     */
    public function sessionAll($mixDefault = null, $mixFilter = null) {
        return $this->inputAll ( $mixDefault, $mixFilter, 'session' );
    }
    
    /**
     * 设置 session 参数
     *
     * @param string $sKey            
     * @param mixed $mixValue            
     * @param string $sType            
     * @return $this
     */
    public function setSession($sKey, $mixValue) {
        return $this->setInput ( $sKey, $mixValue, 'session' );
    }
    
    /**
     * 批量设置 session 参数
     *
     * @param array $arrValue            
     * @param string $sType            
     * @return $this
     */
    public function setSessions(array $arrValue, $mixValue) {
        return $this->setInputs ( $arrValue, 'session' );
    }
    
    /**
     * 返回 session 存储
     *
     * @return \queryyetsimple\session\interfaces\store|null
     */
    public function getSessionStore() {
        return $this->objSession;
    }
    
    /**
     * 是否设置 session 存储
     *
     * @return bool
     */
    public function hasSessionStore() {
        return null !== $this->objSession;
    }
    
    /**
     * 设置 session 存储
     *
     * @param \queryyetsimple\session\interfaces\store $objSession            
     * @return $this
     */
    public function setSessionStore(store $objSession) {
        if ($this->checkFlowControl ())
            return $this;
        $this->objSession = $objSession;
        return $this;
    }
    
    /**
     * 设置 cookie 存储
     *
     * @return \queryyetsimple\cookie\interfaces\cookie|null
     */
    public function getCookieStore() {
        return $this->objCookie;
    }
    
    /**
     * 是否设置 cookie 存储
     *
     * @return bool
     */
    public function hasCookieStore() {
        return null !== $this->objCookie;
    }
    
    /**
     * 设置 cookie 储存
     *
     * @param \queryyetsimple\cookie\interfaces\cookie $objCookie            
     * @return $this
     */
    public function setCookieStore(cookie $objCookie) {
        if ($this->checkFlowControl ())
            return $this;
        $this->objCookie = $objCookie;
        return $this;
    }
    
    /**
     * server 参数
     *
     * @param string $sKey            
     * @param mixed $mixDefault            
     * @param string|array $mixFilter            
     * @return mixed
     */
    public function server($sKey, $mixDefault = null, $mixFilter = null) {
        return $this->input ( $sKey, $mixDefault, $mixFilter, 'server' );
    }
    
    /**
     * 批量 server 参数
     *
     * @param string $sKey            
     * @param mixed $mixDefault            
     * @param string|array $mixFilter            
     * @return array
     */
    public function servers(array $arrKey, $mixDefault = null, $mixFilter = null) {
        return $this->inputs ( $arrKey, $mixDefault, $mixFilter, 'server' );
    }
    
    /**
     * 全部 server 参数
     *
     * @param mixed $mixDefault            
     * @param string|array $mixFilter            
     * @return array
     */
    public function serverAll($mixDefault = null, $mixFilter = null) {
        return $this->inputAll ( $mixDefault, $mixFilter, 'server' );
    }
    
    /**
     * 设置 server 参数
     *
     * @param string $sKey            
     * @param mixed $mixValue            
     * @param string $sType            
     * @return $this
     */
    public function setServer($sKey, $mixValue) {
        return $this->setInput ( $sKey, $mixValue, 'server' );
    }
    
    /**
     * 批量设置 server 参数
     *
     * @param array $arrValue            
     * @param string $sType            
     * @return $this
     */
    public function setServers(array $arrValue, $mixValue) {
        return $this->setInputs ( $arrValue, 'server' );
    }
    
    /**
     * env 参数
     *
     * @param string $sKey            
     * @param mixed $mixDefault            
     * @param string|array $mixFilter            
     * @return mixed
     */
    public function env($sKey, $mixDefault = null, $mixFilter = null) {
        return $this->input ( $sKey, $mixDefault, $mixFilter, 'env' );
    }
    
    /**
     * 批量 env 参数
     *
     * @param string $sKey            
     * @param mixed $mixDefault            
     * @param string|array $mixFilter            
     * @return array
     */
    public function envs(array $arrKey, $mixDefault = null, $mixFilter = null) {
        return $this->inputs ( $arrKey, $mixDefault, $mixFilter, 'env' );
    }
    
    /**
     * 全部 env 参数
     *
     * @param mixed $mixDefault            
     * @param string|array $mixFilter            
     * @return array
     */
    public function envAll($mixDefault = null, $mixFilter = null) {
        return $this->inputAll ( $mixDefault, $mixFilter, 'env' );
    }
    
    /**
     * 设置 env 参数
     *
     * @param string $sKey            
     * @param mixed $mixValue            
     * @param string $sType            
     * @return $this
     */
    public function setEnv($sKey, $mixValue) {
        return $this->setInput ( $sKey, $mixValue, 'env' );
    }
    
    /**
     * 批量设置 env 参数
     *
     * @param array $arrValue            
     * @param string $sType            
     * @return $this
     */
    public function setEnvs(array $arrValue, $mixValue) {
        return $this->setInputs ( $arrValue, 'env' );
    }
    
    /**
     * put 参数
     *
     * @param string $sKey            
     * @param mixed $mixDefault            
     * @param string|array $mixFilter            
     * @return mixed
     */
    public function put($sKey, $mixDefault = null, $mixFilter = null) {
        return $this->input ( $sKey, $mixDefault, $mixFilter, 'put' );
    }
    
    /**
     * 批量 put 参数
     *
     * @param string $sKey            
     * @param mixed $mixDefault            
     * @param string|array $mixFilter            
     * @return array
     */
    public function puts(array $arrKey, $mixDefault = null, $mixFilter = null) {
        return $this->inputs ( $arrKey, $mixDefault, $mixFilter, 'put' );
    }
    
    /**
     * 全部 put 参数
     *
     * @param mixed $mixDefault            
     * @param string|array $mixFilter            
     * @return array
     */
    public function putAll($mixDefault = null, $mixFilter = null) {
        return $this->inputAll ( $mixDefault, $mixFilter, 'put' );
    }
    
    /**
     * 设置 put 参数
     *
     * @param string $sKey            
     * @param mixed $mixValue            
     * @param string $sType            
     * @return $this
     */
    public function setPut($sKey, $mixValue) {
        return $this->setInput ( $sKey, $mixValue, 'put' );
    }
    
    /**
     * 批量设置 put 参数
     *
     * @param array $arrValue            
     * @param string $sType            
     * @return $this
     */
    public function setPuts(array $arrValue, $mixValue) {
        return $this->setInputs ( $arrValue, 'put' );
    }
    
    /**
     * patch 参数
     *
     * @param string $sKey            
     * @param mixed $mixDefault            
     * @param string|array $mixFilter            
     * @return mixed
     */
    public function patch($sKey, $mixDefault = null, $mixFilter = null) {
        return $this->input ( $sKey, $mixDefault, $mixFilter, 'patch' );
    }
    
    /**
     * 批量 patch 参数
     *
     * @param string $sKey            
     * @param mixed $mixDefault            
     * @param string|array $mixFilter            
     * @return array
     */
    public function patchs(array $arrKey, $mixDefault = null, $mixFilter = null) {
        return $this->inputs ( $arrKey, $mixDefault, $mixFilter, 'patch' );
    }
    
    /**
     * 全部 patch 参数
     *
     * @param mixed $mixDefault            
     * @param string|array $mixFilter            
     * @return array
     */
    public function patchAll($mixDefault = null, $mixFilter = null) {
        return $this->inputAll ( $mixDefault, $mixFilter, 'patch' );
    }
    
    /**
     * 设置 patch 参数
     *
     * @param string $sKey            
     * @param mixed $mixValue            
     * @param string $sType            
     * @return $this
     */
    public function setPatch($sKey, $mixValue) {
        return $this->setInput ( $sKey, $mixValue, 'patch' );
    }
    
    /**
     * 批量设置 patch 参数
     *
     * @param array $arrValue            
     * @param string $sType            
     * @return $this
     */
    public function setPatchs(array $arrValue, $mixValue) {
        return $this->setInputs ( $arrValue, 'patch' );
    }
    
    /**
     * delete 参数
     *
     * @param string $sKey            
     * @param mixed $mixDefault            
     * @param string|array $mixFilter            
     * @return mixed
     */
    public function delete($sKey, $mixDefault = null, $mixFilter = null) {
        return $this->input ( $sKey, $mixDefault, $mixFilter, 'delete' );
    }
    
    /**
     * 批量 delete 参数
     *
     * @param string $sKey            
     * @param mixed $mixDefault            
     * @param string|array $mixFilter            
     * @return array
     */
    public function deletes(array $arrKey, $mixDefault = null, $mixFilter = null) {
        return $this->inputs ( $arrKey, $mixDefault, $mixFilter, 'delete' );
    }
    
    /**
     * 全部 delete 参数
     *
     * @param mixed $mixDefault            
     * @param string|array $mixFilter            
     * @return array
     */
    public function deleteAll($mixDefault = null, $mixFilter = null) {
        return $this->inputAll ( $mixDefault, $mixFilter, 'delete' );
    }
    
    /**
     * 设置 delete 参数
     *
     * @param string $sKey            
     * @param mixed $mixValue            
     * @param string $sType            
     * @return $this
     */
    public function setDelete($sKey, $mixValue) {
        return $this->setInput ( $sKey, $mixValue, 'delete' );
    }
    
    /**
     * 批量设置 delete 参数
     *
     * @param array $arrValue            
     * @param string $sType            
     * @return $this
     */
    public function setDeletes(array $arrValue, $mixValue) {
        return $this->setInputs ( $arrValue, 'delete' );
    }
    
    /**
     * 获取变量
     *
     * @param string $sKey            
     * @param mixed $mixDefault            
     * @param string|array $mixFilter            
     * @param string $sType            
     * @param array $arrVar            
     * @return mixed
     */
    public function input($sKey, $mixDefault = null, $mixFilter = null, $sType = 'request', $arrVar = null) {
        $sKey = ( string ) $sKey;
        if (is_null ( $arrVar )) {
            $arrVar = $this->parseVar ( $mixFilter, $sType );
        } else {
            $this->parseFilter ( $mixFilter );
        }
        
        if (strpos ( $sKey, '|' ) !== false) {
            $arrTemp = explode ( '|', $sKey );
            $sKey = array_shift ( $arrTemp );
            $mixFilter = array_merge ( $mixFilter, $arrTemp );
            unset ( $arrTemp );
        }
        
        if (! isset ( $arrVar [$sKey] ))
            return $mixDefault;
        
        if ($mixFilter)
            $this->filterValue ( $arrVar [$sKey], $mixFilter, $mixDefault );
        
        return $arrVar [$sKey];
    }
    
    /**
     * 批量获取变量
     *
     * @param array $arrKey            
     * @param mixed $mixDefault            
     * @param string|array $mixFilter            
     * @param string $sType            
     * @return array
     */
    public function inputs(array $arrKey, $mixDefault = null, $mixFilter = null, $sType = 'request') {
        $arrValue = [ ];
        foreach ( $arrKey as $strKey )
            $arrValue [] = $this->input ( $strKey, $mixDefault, $mixFilter, $sType );
        return $arrValue;
    }
    
    /**
     * 获取所有变量
     *
     * @param mixed $mixDefault            
     * @param string|array $mixFilter            
     * @param string $sType            
     * @param array $arrVar            
     * @return array
     */
    public function inputAll($mixDefault = null, $mixFilter = null, $sType = 'request', $arrVar = null) {
        if (is_null ( $arrVar )) {
            $arrVar = $this->parseVar ( $mixFilter, $sType );
        } else {
            $this->parseFilter ( $mixFilter );
        }
        
        if ($arrVar && $mixFilter) {
            foreach ( $arrVar as &$mixValue ) {
                $this->filterValue ( $mixValue, $mixFilter, $mixDefault );
            }
        }
        return $arrVar;
    }
    
    /**
     * 设置变量
     *
     * @param string $sKey            
     * @param mixed $mixValue            
     * @param string $sType            
     * @return $this
     */
    public function setInput($sKey, $mixValue, $sType = 'request') {
        if ($this->checkFlowControl ())
            return $this;
        $this->{'setGlobal' . ucfirst ( $sType )} ( $sKey, $mixValue );
        return $this;
    }
    
    /**
     * 批量设置变量
     *
     * @param array $arrValue            
     * @param string $sType            
     * @return $this
     */
    public function setInputs(array $arrValue, $sType = 'request') {
        if ($this->checkFlowControl ())
            return $this;
        foreach ( $arrValue as $strKey => $mixValue )
            $this->setInput ( $strKey, $mixValue, $sType );
        return $this;
    }
    
    /**
     * 文件
     *
     * @param string $sKey            
     * @return mixed
     */
    public function file($sKey) {
        $arrFiles = $this->globalFiles ();
        return isset ( $arrFiles [$sKey] ) ? $arrFiles [$sKey] : null;
    }
    
    /**
     * 批量获取文件
     *
     * @param array $arrKey            
     * @return array
     */
    public function files(array $arrKey) {
        $arrValue = [ ];
        foreach ( $arrKey as $strKey )
            $arrValue [] = $this->file ( $strKey );
        return $arrValue;
    }
    
    /**
     * 获取全部文件
     *
     * @return array
     */
    public function fileAll() {
        return $this->globalFiles ();
    }
    
    /**
     * 返回 header 参数
     *
     * @param string $sKey            
     * @param mixed $mixDefault            
     * @return string
     */
    public function header($sKey, $mixDefault = null) {
        $arrVar = $this->globalHeader ();
        $sKey = str_replace ( '_', '-', strtolower ( $sKey ) );
        return isset ( $arrVar [$sKey] ) ? $arrVar [$sKey] : $mixDefault;
    }
    
    /**
     * 批量 header 参数
     *
     * @param array $arrKey            
     * @param mixed $mixDefault            
     * @return array
     */
    public function headers(array $arrKey, $mixDefault = null) {
        $arrValue = [ ];
        foreach ( $arrKey as $strKey )
            $arrValue [] = $this->header ( $strKey, $mixDefault );
        return $arrValue;
    }
    
    /**
     * 获取全部 header 参数
     *
     * @return array
     */
    public function headerAll() {
        return $this->globalHeader ();
    }
    
    /**
     * PHP 运行模式命令行
     *
     * @see http://php.net/manual/zh/function.php-sapi-name.php
     * @return boolean
     */
    public function isCli() {
        return PHP_SAPI == 'cli';
    }
    
    /**
     * PHP 运行模式 cgi
     *
     * @see http://php.net/manual/zh/function.php-sapi-name.php
     * @return boolean
     */
    public function isCgi() {
        return substr ( PHP_SAPI, 0, 3 ) == 'cgi';
    }
    
    /**
     * 是否为 Ajax 请求行为
     *
     * @return boolean
     */
    public function isAjax() {
        return $this->all ( $this->getOption ( 'var_ajax' ) ) ?  : $this->isAjaxReal ();
    }
    
    /**
     * 是否为 Ajax 请求行为真实
     *
     * @return boolean
     */
    public function isAjaxReal() {
        return isset ( $_SERVER ['HTTP_X_REQUESTED_WITH'] ) && 'xmlhttprequest' == strtolower ( $_SERVER ['HTTP_X_REQUESTED_WITH'] );
    }
    
    /**
     * 是否为 Pjax 请求行为
     *
     * @return boolean
     */
    public function isPjax() {
        return $this->all ( $this->getOption ( 'var_pjax' ) ) ?  : $this->isPjaxReal ();
    }
    
    /**
     * 是否为 Pjax 请求行为真实
     *
     * @return boolean
     */
    public function isPjaxReal() {
        return isset ( $_SERVER ['HTTP_X_PJAX'] ) && ! is_null ( $_SERVER ['HTTP_X_PJAX'] );
    }
    
    /**
     * 是否为手机访问
     *
     * @return boolean
     */
    public function isMobile() {
        // Pre-final check to reset everything if the user is on Windows
        if (strpos ( strtolower ( $_SERVER ['HTTP_USER_AGENT'] ), 'windows' ) !== false)
            return false;
        
        $_SERVER ['ALL_HTTP'] = isset ( $_SERVER ['ALL_HTTP'] ) ? $_SERVER ['ALL_HTTP'] : '';
        
        if (preg_match ( '/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower ( $_SERVER ['HTTP_USER_AGENT'] ) ))
            return true;
        
        if ((isset ( $_SERVER ['HTTP_ACCEPT'] )) and (strpos ( strtolower ( $_SERVER ['HTTP_ACCEPT'] ), 'application/vnd.wap.xhtml+xml' ) !== false))
            return true;
        
        if (isset ( $_SERVER ['HTTP_X_WAP_PROFILE'] ))
            return true;
        
        if (isset ( $_SERVER ['HTTP_PROFILE'] ))
            return true;
        
        if (in_array ( strtolower ( substr ( $_SERVER ['HTTP_USER_AGENT'], 0, 4 ) ), [ 
                'w3c ',
                'acs-',
                'alav',
                'alca',
                'amoi',
                'audi',
                'avan',
                'benq',
                'bird',
                'blac',
                'blaz',
                'brew',
                'cell',
                'cldc',
                'cmd-',
                'dang',
                'doco',
                'eric',
                'hipt',
                'inno',
                'ipaq',
                'java',
                'jigs',
                'kddi',
                'keji',
                'leno',
                'lg-c',
                'lg-d',
                'lg-g',
                'lge-',
                'maui',
                'maxo',
                'midp',
                'mits',
                'mmef',
                'mobi',
                'mot-',
                'moto',
                'mwbp',
                'nec-',
                'newt',
                'noki',
                'oper',
                'palm',
                'pana',
                'pant',
                'phil',
                'play',
                'port',
                'prox',
                'qwap',
                'sage',
                'sams',
                'sany',
                'sch-',
                'sec-',
                'send',
                'seri',
                'sgh-',
                'shar',
                'sie-',
                'siem',
                'smal',
                'smar',
                'sony',
                'sph-',
                'symb',
                't-mo',
                'teli',
                'tim-',
                'tosh',
                'tsm-',
                'upg1',
                'upsi',
                'vk-v',
                'voda',
                'wap-',
                'wapa',
                'wapi',
                'wapp',
                'wapr',
                'webc',
                'winw',
                'winw',
                'xda',
                'xda-' 
        ] ))
            return true;
        
        if (strpos ( strtolower ( $_SERVER ['ALL_HTTP'] ), 'operamini' ) !== false) {
            return true;
        }
        
        // But WP7 is also Windows, with a slightly different characteristic
        if (strpos ( strtolower ( $_SERVER ['HTTP_USER_AGENT'] ), 'windows phone' ) !== false)
            return true;
        
        return false;
    }
    
    /**
     * 是否为 GET 请求行为
     *
     * @return boolean
     */
    public function isGet() {
        return $this->method () == 'GET';
    }
    
    /**
     * 是否为 POST 请求行为
     *
     * @return boolean
     */
    public function isPost() {
        return $this->method () == 'POST';
    }
    
    /**
     * 是否为 PUT 请求行为
     *
     * @return boolean
     */
    public function isPut() {
        return $this->method () == 'PUT';
    }
    
    /**
     * 是否为 DELETE 请求行为
     *
     * @return boolean
     */
    public function isDelete() {
        return $this->method () == 'DELETE';
    }
    
    /**
     * 是否为 HEAD 请求行为
     *
     * @return boolean
     */
    public function isHead() {
        return $this->method () == 'HEAD';
    }
    
    /**
     * 是否为 PATCH 请求行为
     *
     * @return boolean
     */
    public function isPatch() {
        return $this->method () == 'PATCH';
    }
    
    /**
     * 是否为 OPTIONS 请求行为
     *
     * @return boolean
     */
    public function isOptions() {
        return $this->method () == 'OPTIONS';
    }
    
    /**
     * 是否启用 https
     *
     * @return boolean
     */
    public function isSsl() {
        if (isset ( $_SERVER ['HTTPS'] ) && ('1' == $_SERVER ['HTTPS'] || 'on' == strtolower ( $_SERVER ['HTTPS'] ))) {
            return true;
        } elseif (isset ( $_SERVER ['SERVER_PORT'] ) && ('443' == $_SERVER ['SERVER_PORT'])) {
            return true;
        }
        return false;
    }
    
    /**
     * 获取 host
     *
     * @return boolean
     */
    public function host() {
        if (is_null ( $this->strHost )) {
            $this->strHost = isset ( $_SERVER ['HTTP_X_FORWARDED_HOST'] ) ? $_SERVER ['HTTP_X_FORWARDED_HOST'] : (isset ( $_SERVER ['HTTP_HOST'] ) ? $_SERVER ['HTTP_HOST'] : '');
        }
        return $this->strHost;
    }
    
    /**
     * 域名
     *
     * @return string
     */
    public function domain() {
        if (is_null ( $this->strDomain )) {
            $this->strDomain = $this->scheme () . '://' . $this->host ();
        }
        return $this->strDomain;
    }
    
    /**
     * 设置域名
     *
     * @param string $strDomain            
     * @return $this
     */
    public function setDomain($strDomain) {
        $this->strDomain = $strDomain;
        return $this;
    }
    
    /**
     * 返回当前 URL 地址
     *
     * @return string
     */
    public function url() {
        if (is_null ( $this->strUrl )) {
            $this->strUrl = $this->domain () . $this->requestUri ();
        }
        return $this->strUrl;
    }
    
    /**
     * 设置当前 URL 地址
     *
     * @param string $strUrl            
     * @return $this
     */
    public function setUrl($strUrl) {
        if ($this->checkFlowControl ())
            return $this;
        $this->strUrl = $strUrl;
        return $this;
    }
    
    /**
     * REQUEST_URI
     *
     * @return string
     */
    public function requestUri() {
        return $_SERVER ['REQUEST_URI'];
    }
    
    /**
     * QUERY_STRING
     *
     *
     * @return string
     */
    public function query() {
        return $_SERVER ['QUERY_STRING'];
    }
    
    /**
     * 服务器端口
     *
     * @return integer
     */
    public function port() {
        return $_SERVER ['SERVER_PORT'];
    }
    
    /**
     * 返回请求页面时通信协议的名称和版本
     *
     * @return integer
     */
    public function protocol() {
        return $this->server ( 'SERVER_PROTOCOL' );
    }
    
    /**
     * 返回用户机器上连接到 Web 服务器所使用的端口号
     *
     * @return integer
     */
    public function remotePort() {
        return $_SERVER ['REMOTE_PORT'];
    }
    
    /**
     * 返回 scheme
     *
     * @return string
     */
    public function scheme() {
        return $this->isSsl () ? 'https' : 'http';
    }
    
    /**
     * 获取 IP 地址
     *
     * @return string
     */
    public function ip() {
        static $sRealip = null;
        
        if ($sRealip !== null) {
            return $sRealip;
        }
        
        if (isset ( $_SERVER )) {
            if (isset ( $_SERVER ['HTTP_X_FORWARDED_FOR'] )) {
                $arrValue = explode ( ',', $_SERVER ['HTTP_X_FORWARDED_FOR'] );
                foreach ( $arrValue as $sIp ) { // 取 X-Forwarded-For 中第一个非 unknown 的有效 IP 字符串
                    $sIp = trim ( $sIp );
                    if ($sIp != 'unknown') {
                        $sRealip = $sIp;
                        break;
                    }
                }
            } elseif (isset ( $_SERVER ['HTTP_CLIENT_IP'] )) {
                $sRealip = $_SERVER ['HTTP_CLIENT_IP'];
            } else {
                if (isset ( $_SERVER ['REMOTE_ADDR'] )) {
                    $sRealip = $_SERVER ['REMOTE_ADDR'];
                } else {
                    $sRealip = '0.0.0.0';
                }
            }
        } else {
            if (getenv ( 'HTTP_X_FORWARDED_FOR' )) {
                $sRealip = getenv ( 'HTTP_X_FORWARDED_FOR' );
            } elseif (getenv ( 'HTTP_CLIENT_IP' )) {
                $sRealip = getenv ( 'HTTP_CLIENT_IP' );
            } else {
                $sRealip = getenv ( 'REMOTE_ADDR' );
            }
        }
        
        preg_match ( "/[\d\.]{7,15}/", $sRealip, $arrOnlineip );
        $sRealip = ! empty ( $arrOnlineip [0] ) ? $arrOnlineip [0] : '0.0.0.0';
        
        return $sRealip;
    }
    
    /**
     * 请求类型
     *
     * @return string
     */
    public function method() {
        if (! is_null ( $this->strMethod ))
            return $this->strMethod;
        
        if (isset ( $_POST [$this->getOption ( 'var_method' )] )) {
            $this->strMethod = strtoupper ( $_POST [$this->getOption ( 'var_method' )] );
            if ($this->strMethod != 'POST') {
                $this->{'set' . ucfirst ( strtolower ( $this->strMethod ) ) . 's'} ( $_POST );
            }
        } elseif (isset ( $_SERVER ['HTTP_X_HTTP_METHOD_OVERRIDE'] )) {
            $this->strMethod = strtoupper ( $_SERVER ['HTTP_X_HTTP_METHOD_OVERRIDE'] );
        } else {
            $this->strMethod = $this->methodReal ();
        }
        return $this->strMethod;
    }
    
    /**
     * 实际请求类型
     *
     * @return string
     */
    public function methodReal() {
        if (! is_null ( $this->strMethodReal ))
            return $this->strMethodReal;
        
        return $this->strMethodReal = $this->isCli () ? 'GET' : $_SERVER ['REQUEST_METHOD'];
    }
    
    /**
     * 请求时间
     *
     * @param boolean $booFloat            
     * @return integer|float
     */
    public function time($booFloat = false) {
        return $booFloat ? $_SERVER ['REQUEST_TIME_FLOAT'] : $_SERVER ['REQUEST_TIME'];
    }
    
    /**
     * 取回应用名
     *
     * @return string
     */
    public function app() {
        return $this->strApp;
    }
    
    /**
     * 取回控制器名
     *
     * @return string
     */
    public function controller() {
        return $this->strController;
    }
    
    /**
     * 取回方法名
     *
     * @return string
     */
    public function action() {
        return $this->strAction;
    }
    
    /**
     * 设置应用名
     *
     * @param string $strApp            
     * @return $this
     */
    public function setApp($strApp) {
        if ($this->checkFlowControl ())
            return $this;
        $this->strApp = $strApp;
        return $this;
    }
    
    /**
     * 设置控制器名
     *
     * @param string $strController            
     * @return $this
     */
    public function setController($strController) {
        if ($this->checkFlowControl ())
            return $this;
        $this->strController = $strController;
        return $this;
    }
    
    /**
     * 设置方法名
     *
     * @param string $strAction            
     * @return $this
     */
    public function setAction($strAction) {
        if ($this->checkFlowControl ())
            return $this;
        $this->strAction = $strAction;
        return $this;
    }
    
    /**
     * 返回当前的语言
     *
     * @return string|null
     */
    public function langset() {
        return $this->strLangset;
    }
    
    /**
     * 设置当前的语言
     *
     * @param string $strLangset            
     * @return $this
     */
    public function setLangset($strLangset) {
        if ($this->checkFlowControl ())
            return $this;
        $this->strLangset = $strLangset;
        return $this;
    }
    
    /**
     * 返回入口文件
     *
     * @return string
     */
    public function enter() {
        if (is_null ( $this->strEnter )) {
            if ($this->isCgi ()) {
                $arrTemp = explode ( '.php', $_SERVER ["PHP_SELF"] ); // CGI/FASTCGI模式下
                $this->strEnter = rtrim ( str_replace ( $this->host (), '', $arrTemp [0] . '.php' ), '/' );
            } else {
                $this->strEnter = rtrim ( $_SERVER ["SCRIPT_NAME"], '/' );
            }
        }
        return $this->strEnter;
    }
    
    /**
     * 返回入口文件 rewrite
     *
     * @return string
     */
    public function enterRewrite() {
        $strEnter = dirname ( $this->strEnter );
        if ($strEnter == '\\') {
            $strEnter = '/';
        }
        return $strEnter;
    }
    
    /**
     * 设置 enter
     *
     * @param string $strEnter            
     * @return $this
     */
    public function setEnter($strEnter) {
        if ($this->checkFlowControl ())
            return $this;
        $this->strEnter = $strEnter;
        return $this;
    }
    
    /**
     * 返回 root
     *
     * @return string
     */
    public function root() {
        if (is_null ( $this->strRoot )) {
            $this->strRoot = dirname ( $this->enter () );
            $this->strRoot = ($this->strRoot == '/' || $this->strRoot == '\\') ? '' : $this->strRoot;
        }
        return $this->strRoot;
    }
    
    /**
     * 设置 root
     *
     * @param string $strRoot            
     * @return $this
     */
    public function setRoot($strRoot) {
        if ($this->checkFlowControl ())
            return $this;
        $this->strRoot = $strRoot;
        return $this;
    }
    
    /**
     * 返回网站公共文件目录
     *
     * @return string
     */
    public function publics() {
        return $this->strPublic;
    }
    
    /**
     * 设置网站公共文件目录
     *
     * @param string $strPublic            
     * @return $this
     */
    public function setPublics($strPublic) {
        if ($this->checkFlowControl ())
            return $this;
        $this->strPublic = $strPublic;
        return $this;
    }
    
    /**
     * pathinfo 兼容性分析
     *
     * @return string
     */
    public function pathinfo() {
        if (! empty ( $_SERVER ['PATH_INFO'] )) {
            return $_SERVER ['PATH_INFO'];
        }
        
        // 分析基础 url
        $sBaseUrl = $this->baseUrl ();
        
        // 分析请求参数
        if (null === ($sRequestUrl = $this->requestUrl ())) {
            return '';
        }
        
        if (($nPos = strpos ( $sRequestUrl, '?' )) > 0) {
            $sRequestUrl = substr ( $sRequestUrl, 0, $nPos );
        }
        
        if ((null !== $sBaseUrl) && (false === ($sPathinfo = substr ( $sRequestUrl, strlen ( $sBaseUrl ) )))) {
            $sPathinfo = '';
        } elseif (null === $sBaseUrl) {
            $sPathinfo = $sRequestUrl;
        }
        
        return $sPathinfo;
    }
    
    /**
     * 分析基础 url
     *
     * @return string
     */
    public function baseUrl() {
        if (! is_null ( $this->sBaseUrl )) {
            return $this->sBaseUrl;
        }
        
        // 兼容分析
        $sFileName = basename ( $_SERVER ['SCRIPT_FILENAME'] );
        if (basename ( $_SERVER ['SCRIPT_NAME'] ) === $sFileName) {
            $sUrl = $_SERVER ['SCRIPT_NAME'];
        } elseif (basename ( $_SERVER ['PHP_SELF'] ) === $sFileName) {
            $sUrl = $_SERVER ['PHP_SELF'];
        } elseif (isset ( $_SERVER ['ORIG_SCRIPT_NAME'] ) && basename ( $_SERVER ['ORIG_SCRIPT_NAME'] ) === $sFileName) {
            $sUrl = $_SERVER ['ORIG_SCRIPT_NAME'];
        } else {
            $sPath = $_SERVER ['PHP_SELF'];
            $arrSegs = explode ( '/', trim ( $_SERVER ['SCRIPT_FILENAME'], '/' ) );
            $arrSegs = array_reverse ( $arrSegs );
            $nIndex = 0;
            $nLast = count ( $arrSegs );
            $sUrl = '';
            do {
                $sSeg = $arrSegs [$nIndex];
                $sUrl = '/' . $sSeg . $sUrl;
                ++ $nIndex;
            } while ( ($nLast > $nIndex) && (false !== ($nPos = strpos ( $sPath, $sUrl ))) && (0 != $nPos) );
        }
        
        // 比对请求
        $sRequestUrl = $this->requestUrl ();
        if (0 === strpos ( $sRequestUrl, $sUrl )) {
            return $this->sBaseUrl = $sUrl;
        }
        
        if (0 === strpos ( $sRequestUrl, dirname ( $sUrl ) )) {
            return $this->sBaseUrl = rtrim ( dirname ( $sUrl ), '/' ) . '/';
        }
        
        if (! strpos ( $sRequestUrl, basename ( $sUrl ) )) {
            return '';
        }
        
        if ((strlen ( $sRequestUrl ) >= strlen ( $sUrl )) && ((false !== ($nPos = strpos ( $sRequestUrl, $sUrl ))) && ($nPos !== 0))) {
            $sUrl = substr ( $sRequestUrl, 0, $nPos + strlen ( $sUrl ) );
        }
        
        return $this->sBaseUrl = rtrim ( $sUrl, '/' ) . '/';
    }
    
    /**
     * 请求参数
     *
     * @return string
     */
    public function requestUrl() {
        if (! is_null ( $this->sRequestUrl )) {
            return $this->sRequestUrl;
        }
        
        // For IIS
        $_SERVER ['REQUEST_URI'] = isset ( $_SERVER ['REQUEST_URI'] ) ? $_SERVER ['REQUEST_URI'] : $_SERVER ["HTTP_X_REWRITE_URL"];
        
        if (isset ( $_SERVER ['HTTP_X_REWRITE_URL'] )) {
            $sUrl = $_SERVER ['HTTP_X_REWRITE_URL'];
        } elseif (isset ( $_SERVER ['REQUEST_URI'] )) {
            $sUrl = $_SERVER ['REQUEST_URI'];
        } elseif (isset ( $_SERVER ['ORIG_PATH_INFO'] )) {
            $sUrl = $_SERVER ['ORIG_PATH_INFO'];
            if (! empty ( $_SERVER ['QUERY_STRING'] )) {
                $sUrl .= '?' . $_SERVER ['QUERY_STRING'];
            }
        } else {
            $sUrl = '';
        }
        
        return $this->sRequestUrl = $sUrl;
    }
    
    /**
     * 判断字符串是否为数字
     *
     * @param string $strSearch            
     * @since bool
     */
    protected function isInteger($mixValue) {
        if (is_int ( $mixValue ))
            return true;
        return ctype_digit ( strval ( $mixValue ) );
    }
    
    /**
     * 返回全局变量 GET
     *
     * @return array
     */
    protected function globalGet() {
        $this->initGlobalGet ();
        return $this->arrGet;
    }
    
    /**
     * 设置全局变量 GET
     *
     * @param string $sKey            
     * @param mixed $mixValue            
     * @return $this
     */
    protected function setGlobalGet($sKey, $mixValue) {
        $this->initGlobalGet ();
        $this->arrGet [$sKey] = $mixValue;
        return $this;
    }
    
    /**
     * 初始化 GET
     *
     * @return void
     */
    protected function initGlobalGet() {
        if (is_null ( $this->arrGet ))
            $this->arrGet = &$_GET;
    }
    
    /**
     * 返回全局变量 POST
     *
     * @return array
     */
    protected function globalPost() {
        $this->initGlobalPost ();
        return $this->arrPost;
    }
    
    /**
     * 设置全局变量 POST
     *
     * @param string $sKey            
     * @param mixed $mixValue            
     * @return $this
     */
    protected function setGlobalPost($sKey, $mixValue) {
        $this->initGlobalPost ();
        $this->arrPost [$sKey] = $mixValue;
        return $this;
    }
    
    /**
     * 初始化 POST
     *
     * @return void
     */
    protected function initGlobalPost() {
        if (is_null ( $this->arrPost ))
            $this->arrPost = &$_POST;
    }
    
    /**
     * 返回全局变量 REQUEST
     *
     * @return array
     */
    protected function globalRequest() {
        $this->initGlobalRequest ();
        return $this->arrRequest;
    }
    
    /**
     * 设置全局变量 REQUEST
     *
     * @param string $sKey            
     * @param mixed $mixValue            
     * @return $this
     */
    protected function setGlobalRequest($sKey, $mixValue) {
        $this->initGlobalRequest ();
        $this->arrRequest [$sKey] = $mixValue;
        return $this;
    }
    
    /**
     * 初始化 REQUEST
     *
     * @return void
     */
    protected function initGlobalRequest() {
        if (is_null ( $this->arrRequest ))
            $this->arrRequest = &$_REQUEST;
    }
    
    /**
     * 返回全局变量 COOKIE
     *
     * @return array
     */
    protected function globalCookie() {
        $this->initGlobalCookie ();
        return $this->arrCookie;
    }
    
    /**
     * 设置全局变量 COOKIE
     *
     * @param string $sKey            
     * @param mixed $mixValue            
     * @return $this
     */
    protected function setGlobalCookie($sKey, $mixValue) {
        $this->initGlobalCookie ();
        $this->objCookie->set ( $sKey, $mixValue, [ 
                'prefix' => '' 
        ] );
        $this->arrCookie [$sKey] = $mixValue;
        return $this;
    }
    
    /**
     * 初始化 COOKIE
     *
     * @return void
     */
    protected function initGlobalCookie() {
        if (! $this->hasCookieStore ()) {
            throw new RuntimeException ( 'Cookie store is not set' );
        }
        
        if (is_null ( $this->arrCookie ))
            $this->arrCookie = &$_COOKIE;
    }
    
    /**
     * 返回全局变量 SESSION
     *
     * @return array
     */
    protected function globalSession() {
        $this->initGlobalSession ();
        return $this->arrSession;
    }
    
    /**
     * 设置全局变量 SESSION
     *
     * @param string $sKey            
     * @param mixed $mixValue            
     * @return $this
     */
    protected function setGlobalSession($sKey, $mixValue) {
        $this->initGlobalSession ();
        $strOldPrefix = $this->objSession->getOption ( 'prefix' );
        $this->objSession->option ( 'prefix', '' )->set ( $sKey, $mixValue );
        $this->objSession->option ( 'prefix', $strOldPrefix );
        $this->arrSession [$sKey] = $mixValue;
        return $this;
    }
    
    /**
     * 初始化 SESSION
     *
     * @return void
     */
    protected function initGlobalSession() {
        if (! $this->hasSessionStore ()) {
            throw new RuntimeException ( 'Session store is not set' );
        }
        
        if (is_null ( $this->arrSession ))
            $this->arrSession = &$_SESSION;
    }
    
    /**
     * 返回全局变量 SERVER
     *
     * @return array
     */
    protected function globalServer() {
        $this->initGlobalServer ();
        return $this->arrServer;
    }
    
    /**
     * 设置全局变量 SERVER
     *
     * @param string $sKey            
     * @param mixed $mixValue            
     * @return $this
     */
    protected function setGlobalServer($sKey, $mixValue) {
        $this->initGlobalServer ();
        $this->arrServer [$sKey] = $mixValue;
        return $this;
    }
    
    /**
     * 初始化 SERVER
     *
     * @return void
     */
    protected function initGlobalServer() {
        if (is_null ( $this->arrServer ))
            $this->arrServer = &$_SERVER;
    }
    
    /**
     * 返回全局变量 ENV
     *
     * @return array
     */
    protected function globalEnv() {
        $this->initGlobalEnv ();
        return $this->arrEnv;
    }
    
    /**
     * 设置全局变量 ENV
     *
     * @param string $sKey            
     * @param mixed $mixValue            
     * @return $this
     */
    protected function setGlobalEnv($sKey, $mixValue) {
        $this->initGlobalEnv ();
        $this->arrEnv [$sKey] = $mixValue;
        $this->setEnvironmentVariable ( $sKey, $mixValue );
        $this->setGlobalServer ( $sKey, $mixValue );
        return $this;
    }
    
    /**
     * 初始化 ENV
     *
     * @return void
     */
    protected function initGlobalEnv() {
        if (is_null ( $this->arrEnv ))
            $this->arrEnv = &$_ENV;
    }
    
    /**
     * 返回全局变量 Put
     *
     * @return array
     */
    protected function globalPut() {
        $this->initGlobalPut ();
        return $this->arrPut;
    }
    
    /**
     * 设置全局变量 PUT
     *
     * @param string $sKey            
     * @param mixed $mixValue            
     * @return $this
     */
    protected function setGlobalPut($sKey, $mixValue) {
        $this->initGlobalPut ();
        $this->arrPut [$sKey] = $mixValue;
        return $this;
    }
    
    /**
     * 返回全局变量 PATCH
     *
     * @return array
     */
    protected function globalPatch() {
        return $this->globalPut ();
    }
    
    /**
     * 设置全局变量 PATCH
     *
     * @param string $sKey            
     * @param mixed $mixValue            
     * @return $this
     */
    protected function setGlobalPatch($sKey, $mixValue) {
        return $this->setGlobalPut ( $sKey, $mixValue );
    }
    
    /**
     * 返回全局变量 DELETE
     *
     * @return array
     */
    protected function globalDelete() {
        return $this->globalPut ();
    }
    
    /**
     * 设置全局变量 DELETE
     *
     * @param string $sKey            
     * @param mixed $mixValue            
     * @return $this
     */
    protected function setGlobalDelete($sKey, $mixValue) {
        return $this->setGlobalPut ( $sKey, $mixValue );
    }
    
    /**
     * 初始化 PUT
     *
     * @return void
     */
    protected function initGlobalPut() {
        if (is_null ( $this->arrPut )) {
            $this->arrPut = file_get_contents ( 'php://input' );
            if (strpos ( $this->arrPut, '":' )) {
                $this->arrPut = json_decode ( $this->arrPut, true );
            } else {
                parse_str ( $this->arrPut, $this->arrPut );
            }
        }
    }
    
    /**
     * 返回全局变量 FILES
     *
     * @return array
     */
    protected function globalFiles() {
        if (is_null ( $this->arrFiles ))
            $this->arrFiles = isset ( $_FILES ) ? $_FILES : [ ];
        return $this->arrFiles;
    }
    
    /**
     * 返回所有变量
     *
     * @param boolean $booFile            
     * @return array
     */
    protected function globalAll($booFile = false) {
        if (is_null ( $this->arrAll )) {
            $this->arrAll = array_merge ( $this->requestAll (), $this->putAll () );
        }
        return $booFile ? array_merge ( $this->arrAll, $this->fileAll () ) : $this->arrAll;
    }
    
    /**
     * 返回所有 HEADER
     *
     * @return array
     */
    protected function globalHeader() {
        if (is_null ( $this->arrHeader )) {
            if (! function_exists ( 'apache_request_headers' ) || ! ($this->arrHeader = array_change_key_case ( apache_request_headers () ))) {
                foreach ( $_SERVER as $strKey => $mixValue ) {
                    if (substr ( $strKey, 0, 5 ) === 'HTTP_') {
                        $strKey = strtolower ( str_replace ( ' ', '-', str_replace ( '_', ' ', substr ( $strKey, 5 ) ) ) );
                        $this->arrHeader [$strKey] = $mixValue;
                    }
                }
            }
        }
        return $this->arrHeader;
    }
    
    /**
     * 过滤值
     *
     * @param mixed $mixValue            
     * @param array $arrFilter            
     * @param array $mixDefault            
     * @return mixed
     */
    protected function filterValue(&$mixValue, $arrFilter, $mixDefault = null) {
        foreach ( $arrFilter as $mixFilter ) {
            if (strpos ( $mixFilter, '=' ) !== false) {
                list ( $mixFilter, $strExtend ) = explode ( '=', $mixFilter );
                
                if ($mixFilter == 'default') {
                    $mixFilter = '$mixValue = ' . $mixValue . ' ?  : $mixDefault;';
                } elseif ($strExtend) {
                    if (strstr ( $strExtend, '**' )) {
                        $strExtend = str_replace ( '**', '$mixValue', $strExtend );
                        $mixFilter = "\$mixValue = {$mixFilter} ( {$strExtend} );";
                    } else {
                        $mixFilter = "\$mixValue = {$mixFilter} ( \$mixValue, {$strExtend} );";
                    }
                }
                eval ( $mixFilter );
            } elseif (is_callable ( $mixFilter )) {
                $mixValue = call_user_func ( $mixFilter, $mixValue );
            } elseif (is_scalar ( $mixValue ) && ! empty ( $mixFilter )) {
                $mixValue = filter_var ( $mixValue, $this->isInteger ( $mixFilter ) ? $mixFilter : filter_id ( $mixFilter ) );
                if (false === $mixValue) {
                    $mixValue = $mixDefault;
                    break;
                }
            }
        }
        return $mixValue;
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
    }
    
    /**
     * 分析变量
     *
     * @param string|array $mixFilter            
     * @param string $sType            
     * @return mixed
     */
    protected function parseVar(&$mixFilter = null, &$sType = 'request') {
        if (is_null ( $sType ))
            $sType = strtolower ( $this->method () );
        $this->parseFilter ( $mixFilter );
        return $this->{'global' . ucfirst ( $sType )} ();
    }
    
    /**
     * 分析过滤器
     *
     * @param string|array $mixFilter            
     * @return mixed
     */
    protected function parseFilter(&$mixFilter = null) {
        if (! $mixFilter) {
            $mixFilter = [ ];
        } else {
            $mixFilter = is_array ( $mixFilter ) ? $mixFilter : [ 
                    $mixFilter 
            ];
        }
    }
    
    /**
     * 对象转数组
     *
     * @return array
     */
    public function toArray() {
        return $this->allAll ();
    }
    
    /**
     * 实现 isset( $obj['hello'] )
     *
     * @param string $strKey            
     * @return mixed
     */
    public function offsetExists($strKey) {
        return array_key_exists ( $strKey, $this->allAll () );
    }
    
    /**
     * 实现 $strHello = $obj['hello']
     *
     * @param string $strKey            
     * @return mixed
     */
    public function offsetGet($strKey) {
        return data_get ( $this->allAll (), $strKey );
    }
    
    /**
     * 实现 $obj['hello'] = 'world'
     *
     * @param string $strKey            
     * @param mixed $mixValue            
     * @return mixed
     */
    public function offsetSet($strKey, $mixValue) {
        $this->method () == 'GET' ? $this->setGet ( $strKey, $mixValue ) : $this->setRequest ( $strKey, $mixValue );
    }
    
    /**
     * 实现 unset($obj['hello'])
     *
     * @param string $strKey            
     * @return void
     */
    public function offsetUnset($strKey) {
    }
    
    /**
     * 是否存在输入值
     *
     * @param string $strKey            
     * @return bool
     */
    public function __isset($strKey) {
        return ! is_null ( $this->__get ( $strKey ) );
    }
    
    /**
     * 获取输入值
     *
     * @param string $strKey            
     * @return mixed
     */
    public function __get($strKey) {
        $arrAll = $this->allAll ();
        return isset ( $arrAll [$strKey] ) ? $arrAll [$strKey] : null;
    }
}
