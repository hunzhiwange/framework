<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\page\abstracts;

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
use queryyetsimple\classs\option;
use queryyetsimple\assert\assert;
use queryyetsimple\support\interfaces\htmlable;

/**
 * 分页处理
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.07.14
 * @version 1.0
 */
abstract class page implements htmlable {
    
    use option;
    
    /**
     * 总记录数量
     *
     * @var int
     */
    protected $intTotalRecord;
    
    /**
     * 每页分页数量
     *
     * @var int
     */
    protected $intPerPage;
    
    /**
     * 当前分页页码
     *
     * @var int
     */
    protected $intCurrentPage;
    
    /**
     * 总页数
     *
     * @var int
     */
    protected $intTotalPage;
    
    /**
     * 分页开始位置
     *
     * @var int
     */
    protected $intPageStart;
    
    /**
     * 分页结束位置
     *
     * @var int
     */
    protected $intPageEnd;
    
    /**
     * 解析后参数变量
     *
     * @var array
     */
    protected $arrResolveParameter;
    
    /**
     * 解析后 url 地址
     *
     * @var array
     */
    protected $strResolveUrl;
    
    /**
     * 解析 url
     *
     * @var callable
     */
    protected static $calUrlResolver;
    
    /**
     * 默认每页分页数量
     *
     * @var int
     */
    const PER_PAGE = 15;
    
    /**
     * 无穷大记录数
     *
     * @var int
     */
    const INFINITY = 999999999;
    
    /**
     * 默认分页渲染
     *
     * @var int
     */
    const RENDER = 'defaults';
    
    /**
     * 默认范围
     *
     * @var int
     */
    const RANGE = 2;
    
    /**
     * 配置
     *
     * @var array
     */
    protected $arrOption = [ 
            'page' => 'page',
            'per_page' => 15,
            'range' => 2,
            'render' => 'defaults',
            'render_option' => [ ],
            'url' => null,
            'parameter' => [ ],
            'fragment' => null 
    ];
    
    /**
     * 转化输出 HTML
     *
     * @return string
     */
    public function toHtml() {
        return ( string ) $this->render ();
    }
    
    /**
     * 追加分页条件
     *
     * @param string $strKey            
     * @param string $strValue            
     * @return $this
     */
    public function append($strKey, $strValue) {
        return $this->addParameter ( $mixKey, $mixValue );
    }
    
    /**
     * 批量追加分页条件
     *
     * @param array $arrValue            
     * @return $this
     */
    public function appends(array $arrValue) {
        foreach ( $arrValue as $strKey => $strValue ) {
            $this->addParameter ( $strKey, $strValue );
        }
        return $this;
    }
    
    /**
     * 设置分页条件
     *
     * @param array $arrParameter            
     * @return $this
     */
    public function parameter(array $arrParameter) {
        return $this->option ( 'parameter', $arrParameter );
        ;
    }
    
    /**
     * 添加分页条件
     *
     * @param string $strKey            
     * @param string $strValue            
     * @return $this
     */
    public function addParameter($strKey, $strValue) {
        if ($strKey !== $this->getPageName ()) {
            $this->optionArray ( 'parameter', [ 
                    $strKey => $strValue 
            ] );
        }
        return $this;
    }
    
    /**
     * 设置渲染参数
     *
     * @param string $strKey            
     * @param string $strValue            
     * @return $this
     */
    public function renderOption($strKey, $strValue) {
        return $this->optionArray ( 'render_option', [ 
                $strKey => $strValue 
        ] );
    }
    
    /**
     * 批量设置渲染参数
     *
     * @param string $strKey            
     * @param string $strValue            
     * @return $this
     */
    public function renderOptions(array $arrOption) {
        foreach ( $arrOption as $strKey => $strValue ) {
            $this->renderOption ( $strKey, $strValue );
        }
        return $this;
    }

    /**
     * 是否启用 CSS
     *
     * @param boolean $booOn                      
     * @return $this
     */
    public function css($booOn=true) {
        return $this->renderOption ( 'css', $booOn );
    }
    
    /**
     * 获取渲染参数
     *
     * @return $this
     */
    public function getRenderOption() {
        return $this->getOption ( 'render_option' );
    }
    
    /**
     * 设置 url
     *
     * @param string|null $mixUrl            
     * @return $this
     */
    public function url($mixUrl = null) {
        return $this->option ( 'url', $mixUrl );
    }
    
    /**
     * 设置 render
     *
     * @param string|null $mixRender            
     * @return $this
     */
    public function renders($mixRender = null) {
        return $this->option ( 'render', $mixRender );
    }
    
    /**
     * 获取 render
     *
     * @return string|null
     */
    public function getRender() {
        return $this->getOption ( 'render' ) ?  : static::RENDER;
    }
    
    /**
     * 设置 range
     *
     * @param int|null $intRange            
     * @return $this
     */
    public function range($mixRange = null) {
        return $this->option ( 'range', $mixRange );
    }
    
    /**
     * 获取 range
     *
     * @return int
     */
    public function getRange() {
        return $this->getOption ( 'range' ) ? intval ( $this->getOption ( 'range' ) ) : static::RANGE;
    }
    
    /**
     * 设置 url 描点
     *
     * @param string|null $mixFragment            
     * @return $this
     */
    public function fragment($mixFragment = null) {
        return $this->option ( 'fragment', $mixFragment );
    }
    
    /**
     * 获取 url 描点
     *
     * @return string|null
     */
    public function getFragment() {
        return $this->getOption ( 'fragment' );
    }
    
    /**
     * 设置每页分页数量
     *
     * @param string $strPageName            
     * @return $this
     */
    public function perPage($strPageName) {
        return $this->option ( 'per_page' );
    }
    
    /**
     * 返回每页数量
     *
     * @return int
     */
    public function getPerPage() {
        if (is_null ( $this->intPerPage )) {
            $this->intPerPage = $this->getOption ( 'per_page' ) ?  : static::PER_PAGE;
        }
        return $this->intPerPage;
    }
    
    /**
     * 设置分页名字
     *
     * @param string $strPageName            
     * @return $this
     */
    public function pageName($strPageName) {
        return $this->option ( 'page' );
    }
    
    /**
     * 获取分页名字
     *
     * @return string
     */
    public function getPageName() {
        return $this->getOption ( 'page' );
    }
    
    /**
     * 返回总记录数量
     *
     * @return int
     */
    public function getTotalRecord() {
        return $this->intTotalRecord === true ? static::INFINITY : $this->intTotalRecord;
    }
    
    /**
     * 是否为无限分页
     *
     * @return boolean
     */
    public function isTotalInfinity() {
        return $this->getTotalRecord () === static::INFINITY;
    }
    
    /**
     * 取得第一个记录的编号
     *
     * @return int
     */
    public function getFirstRecord() {
        if (! $this->canTotalRender ()) {
            return;
        }
        return ($this->getCurrentPage () - 1) * $this->getPerPage () + 1;
    }
    
    /**
     * 取得最后一个记录的编号
     *
     * @return int
     */
    public function getLastRecord() {
        if (! $this->canTotalRender ()) {
            return;
        }
        return $this->getFirstRecord () + $this->getTotalRecord () - 1;
    }
    
    /**
     * 返回当前分页
     *
     * @return int
     */
    public function getCurrentPage() {
        if (is_null ( $this->intCurrentPage )) {
            if (isset ( $_GET [$this->getOption ( 'page' )] )) {
                $this->intCurrentPage = abs ( intval ( $_GET [$this->getOption ( 'page' )] ) );
                if ($this->intCurrentPage < 1) {
                    $this->intCurrentPage = 1;
                }
            } else {
                $this->intCurrentPage = 1;
            }
        }
        
        return $this->intCurrentPage;
    }
    
    /**
     * 返回分页视图开始页码
     *
     * @return int
     */
    public function getPageStart() {
        if (is_null ( $this->intPageStart )) {
            $this->intPageStart = $this->getCurrentPage () - $this->getRange ();
            if ($this->intPageStart < $this->getRange () * 2) {
                $this->intPageStart = 1;
            }
        }
        
        return $this->intPageStart;
    }
    
    /**
     * 返回分页视图结束页码
     *
     * @return int
     */
    public function getPageEnd() {
        if (is_null ( $this->intPageEnd )) {
            $this->intPageEnd = $this->getCurrentPage () + $this->getRange ();
            if ($this->getPageStart () == 1) {
                $this->intPageEnd = $this->getRange () * 2 + 2;
            }
            
            if ($this->getTotalPage () && $this->intPageEnd > $this->getTotalPage ()) {
                $this->intPageEnd = $this->getTotalPage ();
            }
        }
        
        return $this->intPageEnd;
    }
    
    /**
     * 返回总分页数量
     *
     * @return int
     */
    public function getTotalPage() {
        if (is_null ( $this->intTotalPage ) && $this->getTotalRecord ()) {
            $this->intTotalPage = ceil ( $this->getTotalRecord () / $this->getPerPage () );
            if ($this->intTotalPage < 1) {
                $this->intTotalPage = 1;
            }
        }
        
        return $this->intTotalPage;
    }
    
    /**
     * 是否渲染 total
     *
     * @return boolean
     */
    public function canTotalRender() {
        return ! is_null ( $this->getTotalRecord () ) && ! $this->isTotalInfinity ();
    }
    
    /**
     * 是否渲染 first
     *
     * @return boolean
     */
    public function canFirstRender() {
        return $this->getTotalPage () > 1 && $this->getCurrentPage () >= ($this->getRange () * 2 + 2);
    }
    
    /**
     * 返回渲染 first.prev
     *
     * @return int
     */
    public function parseFirstRenderPrev() {
        return $this->getCurrentPage () - ($this->getRange () * 2 + 1);
    }
    
    /**
     * 是否渲染 prev
     *
     * @return boolean
     */
    public function canPrevRender() {
        return (is_null ( $this->getTotalPage () ) || $this->getTotalPage () > 1) && $this->getCurrentPage () != 1;
    }
    
    /**
     * 返回渲染 prev.prev
     *
     * @return int
     */
    public function parsePrevRenderPrev() {
        return $this->getCurrentPage () - 1;
    }
    
    /**
     * 是否渲染 main
     *
     * @return boolean
     */
    public function canMainRender() {
        return $this->getTotalPage () > 1;
    }
    
    /**
     * 是否渲染 next
     *
     * @return string
     */
    public function canNextRender() {
        return is_null ( $this->getTotalPage () ) || ($this->getTotalPage () > 1 && $this->getCurrentPage () != $this->getTotalPage ());
    }
    
    /**
     * 是否渲染 last
     *
     * @return string
     */
    public function canLastRender() {
        return $this->getTotalPage () > 1 && $this->getCurrentPage () != $this->getTotalPage () && $this->getTotalPage () > $this->getPageEnd ();
    }
    
    /**
     * 是否渲染 last
     *
     * @return string
     */
    public function canLastRenderNext() {
        return $this->getTotalPage () > $this->getPageEnd () + 1;
    }
    
    /**
     * 返回渲染 last.next
     *
     * @return int
     */
    public function parseLastRenderNext() {
        $intNext = $this->getCurrentPage () + $this->getRange () * 2 + 1;
        if (! $this->isTotalInfinity () && $intNext > $this->getTotalPage ()) {
            $intNext = $this->getTotalPage ();
        }
        return $intNext;
    }
    
    /**
     * 解析 url
     *
     * @return string
     */
    public function resolverUrl() {
        if (! static::$calUrlResolver)
            throw new RuntimeException ( 'Page not set url resolver' );
        return call_user_func_array ( static::$calUrlResolver, func_get_args () );
    }
    
    /**
     * 设置 url 解析回调
     *
     * @param callable $calUrlResolver            
     * @return void
     */
    public static function setUrlResolver($calUrlResolver) {
        assert::callback ( $calUrlResolver );
        static::$calUrlResolver = $calUrlResolver;
    }
    
    /**
     * 替换分页变量
     *
     * @param mixed $mixPage            
     * @return string
     */
    public function pageReplace($mixPage) {
        return str_replace ( [ 
                urlencode ( '{page}' ),
                '{page}' 
        ], $mixPage, $this->getUrl () );
    }
    
    /**
     * 分析分页 url 地址
     *
     * {page} 表示自定义分页变量替换
     * 带有 @ 表示使用 url 函数进行二次解析
     * foo@ 表示具有子域名 subdomain@blog://list/{page},subdomain@blog://list/index
     * @/ 表示自定义域名格式 @/list-{page},@/list/index,subdomain@/list-{page}
     * 不带有 @ 表示不使用 url 进行二次解析
     * 空表示基于 $_GET 分析 url
     *
     * @return string
     */
    protected function getUrl() {
        if (! is_null ( $this->strResolveUrl ))
            return $this->strResolveUrl;
        
        $booWithUrl = false;
        $strSubdomain = 'www';
        
        if (strpos ( $this->getOption ( 'url' ), '@' ) !== false) {
            $booWithUrl = true;
            if (strpos ( $this->getOption ( 'url' ), '@' ) !== 0) {
                $arrTemp = explode ( '@', $this->getOption ( 'url' ) );
                $this->option ( 'url', $arrTemp [1] );
                $strSubdomain = $arrTemp [0];
                unset ( $arrTemp );
            }
        }
        
        // 当前URL分析
        if (! empty ( $this->getOption ( 'url' ) )) {
            if ($booWithUrl) {
                $this->strResolveUrl = $this->resolverUrl ( $this->getOption ( 'url' ), $this->getParseParameter ( false === strpos ( $this->getOption ( 'url' ), '{page}' ) ), [ 
                        'subdomain' => $strSubdomain 
                ] );
            } else {
                if (false === strpos ( $this->getOption ( 'url' ), '{page}' )) {
                    $this->strResolveUrl = (strpos ( $this->getOption ( 'url' ), '?' ) === false ? '?' : '&') . $this->getOption ( 'page' ) . '={page}';
                }
            }
        } else {
            $this->strResolveUrl = $this->resolverUrl ( '', $this->getParseParameter (), [ 
                    'subdomain' => $strSubdomain 
            ] );
        }
        
        $this->strResolveUrl = $this->strResolveUrl . $this->buildFragment ();
        
        unset ( $booWithUrl, $strSubdomain );
        return $this->strResolveUrl;
    }
    
    /**
     * 返回分析后的参数
     *
     * @param boolean $booWithDefault            
     * @return array
     */
    protected function getParseParameter($booWithDefault = true) {
        return $this->getParameter ( $booWithDefault ? $this->getDefaultPageParameter () : [ ] );
    }
    
    /**
     * 默认分页参数
     *
     * @return array
     */
    protected function getDefaultPageParameter() {
        return [ 
                $this->getOption ( 'page' ) => '{page}' 
        ];
    }
    
    /**
     * 解析参数
     *
     * @param array $arrExtend            
     * @return array
     */
    protected function getParameter(array $arrExtend) {
        if (is_null ( $this->arrResolveParameter )) {
            if ($this->getOption ( 'parameter' )) {
                if (is_string ( $this->getOption ( 'parameter' ) )) {
                    parse_str ( $this->getOption ( 'parameter' ), $this->arrResolveParameter );
                } elseif (is_array ( $this->getOption ( 'parameter' ) )) {
                    $this->arrResolveParameter = $this->getOption ( 'parameter' );
                }
            } else {
                $this->arrResolveParameter = $_GET;
            }
        }
        return array_merge ( $this->arrResolveParameter, $arrExtend );
    }
    
    /**
     * 创建描点
     *
     * @return string
     */
    protected function buildFragment() {
        return $this->getFragment () ? '#' . $this->getFragment () : '';
    }
    
    /**
     * 统计元素数量 count($obj)
     *
     * @return int
     */
    public function count() {
    }
    
    /**
     * 实现 isset( $obj['hello'] )
     *
     * @param string $strKey            
     * @return mixed
     */
    public function offsetExists($strKey) {
    }
    
    /**
     * 实现 $strHello = $obj['hello']
     *
     * @param string $strKey            
     * @return mixed
     */
    public function offsetGet($strKey) {
    }
    
    /**
     * 实现 $obj['hello'] = 'world'
     *
     * @param string $strKey            
     * @param mixed $mixValue            
     * @return void
     */
    public function offsetSet($strKey, $mixValue) {
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
     * 转化为字符串
     *
     * @return string
     */
    public function __toString() {
        return ( string ) $this->render ();
    }
}
