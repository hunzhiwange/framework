<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\page;

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

use queryyetsimple\classs\option;
use queryyetsimple\page\interfaces\render;
use queryyetsimple\page\interfaces\page as interfaces_page;

/**
 * bootstrap 分页渲染
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.07.14
 * @version 1.0
 */
class bootstrap implements render {
    
    use option;
    
    /**
     * 分页
     *
     * @var \queryyetsimple\page\interfaces\page $objPage
     */
    protected $objPage;
    
    /**
     * 配置
     *
     * @var array
     */
    protected $arrOption = [
            // lg sm
            'size' => '',
            'template' => '{header} {ul} {prev} {first} {main} {last} {next} {endul} {footer}',
            'css' => true 
    ];
    
    /**
     * 构造函数
     *
     * @param \queryyetsimple\page\interfaces\page $objPage            
     * @param array $arrOption            
     * @return void
     */
    public function __construct(interfaces_page $objPage, array $arrOption = []) {
        $this->objPage = $objPage;
        $this->options ( $arrOption );
        if ($this->objPage->getRenderOption ()) {
            $this->options ( $this->objPage->getRenderOption ( 'render' ) );
        }
    }
    
    /**
     * 渲染
     *
     * @return string
     */
    public function render() {
        return ($this->getOption ( 'css' ) ? $this->css () : '') . preg_replace_callback ( "/{(.+?)}/", function ($arrMatche) {
            return $this->{'get' . ucwords ( $arrMatche [1] ) . 'Render'} ();
        }, $this->getOption ( 'template' ) );
    }
    
    /**
     * 返回渲染 CSS
     *
     * @return string
     */
    protected function css() {
        return '<link href="http://v3.bootcss.com/dist/css/bootstrap.min.css" rel="stylesheet">';
    }
    
    /**
     * 返回渲染 header
     *
     * @return string
     */
    protected function getHeaderRender() {
        return '<nav aria-label="navigation">';
    }
    
    /**
     * 返回渲染 pager.ul
     *
     * @return string
     */
    protected function getUlRender() {
        return sprintf ( '<ul class="pagination%s">', $this->getOption ( 'size' ) ? ' pagination-' . $this->getOption ( 'size' ) : '' );
    }
    
    /**
     * 返回渲染 first
     *
     * @return string
     */
    protected function getFirstRender() {
        if (! $this->objPage->canFirstRender ()) {
            return;
        }
        return sprintf ( '<li class=""><a href="%s" >1</a></li><li><a href="%s">...</a></li>', $this->replace ( 1 ), $this->replace ( $this->objPage->parseFirstRenderPrev () ) );
    }
    
    /**
     * 返回渲染 prev
     *
     * @return string
     */
    protected function getPrevRender() {
        if ($this->objPage->canPrevRender ()) {
            return sprintf ( '<li><a aria-label="Previous" href="%s"><span aria-hidden="true">&laquo;</span></a></li>', $this->replace ( $this->objPage->parsePrevRenderPrev () ) );
        } else {
            return '<li class="disabled"><a aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
        }
    }
    
    /**
     * 返回渲染 main
     *
     * @return string
     */
    protected function getMainRender() {
        if (! $this->objPage->canMainRender ()) {
            return;
        }
        
        $strMain = '';
        for($nI = $this->objPage->getPageStart (); $nI <= $this->objPage->getPageEnd (); $nI ++) {
            $booActive = $this->objPage->getCurrentPage () == $nI;
            $strMain .= sprintf ( '<li class="%s"><a%s>%d</a></li>', $booActive ? ' active' : '', $booActive ? '' : sprintf ( ' href="%s"', $this->replace ( $nI ) ), $nI );
        }
        return $strMain;
    }
    
    /**
     * 返回渲染 next
     *
     * @return string
     */
    protected function getNextRender() {
        if ($this->objPage->canNextRender ()) {
            return sprintf ( '<li><a aria-label="Next" href="%s"><span aria-hidden="true">&raquo;</span></a></li>', $this->replace ( $this->objPage->getCurrentPage () + 1 ) );
        } else {
            return '<li class="disabled"><a aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
        }
    }
    
    /**
     * 返回渲染 last
     *
     * @return string
     */
    protected function getLastRender() {
        if ($this->objPage->isTotalInfinity ()) {
            return sprintf ( '<li><a href="%s">...</a></li>', $this->replace ( $this->objPage->parseLastRenderNext () ) );
        }
        
        if ($this->objPage->canLastRender ()) {
            return ($this->objPage->canLastRenderNext () ? sprintf ( '<li><a href="%s">...</a></li>', $this->replace ( $this->objPage->parseLastRenderNext () ) ) : '') . sprintf ( '<li><a href="%s">%d</a></li>', $this->replace ( $this->objPage->getTotalPage () ), $this->objPage->getTotalPage () );
        }
    }
    
    /**
     * 返回渲染 pager.endul
     *
     * @return string
     */
    protected function getEndulRender() {
        return '</ul>';
    }
    
    /**
     * 返回渲染 footer
     *
     * @return string
     */
    protected function getFooterRender() {
        return '</nav>';
    }
    
    /**
     * 替换分页变量
     *
     * @param mixed $mixPage            
     * @return string
     */
    public function replace($mixPage) {
        return $this->objPage->pageReplace ( $mixPage );
    }
}
