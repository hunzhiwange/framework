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
namespace queryyetsimple\page;

use queryyetsimple\support\option;

/**
 * 默认分页渲染
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.07.14
 * @version 1.0
 */
class defaults implements irender
{
    use option;
    
    /**
     * 分页
     *
     * @var \queryyetsimple\page\ipage
     */
    protected $objPage;
    
    /**
     * 配置
     *
     * @var array
     */
    protected $arrOption = [
        'small' => false, 
        'template' => '{header} {total} {prev} {ul} {first} {main} {last} {endul} {next} {jump} {footer}', 
        'css' => true
    ];
    
    /**
     * 构造函数
     *
     * @param \queryyetsimple\page\ipage $objPage
     * @param array $arrOption
     * @return void
     */
    public function __construct(ipage $objPage, array $arrOption = [])
    {
        $this->objPage = $objPage;
        $this->options($arrOption);
        if ($this->objPage->getRenderOption()) {
            $this->options($this->objPage->getRenderOption('render'));
        }
    }
    
    /**
     * 渲染
     *
     * @return string
     */
    public function render()
    {
        return ($this->getOption('css') ? $this->css() : '') . preg_replace_callback("/{(.+?)}/", function ($arrMatche)
        {
            return $this->{'get' . ucwords($arrMatche[1]) . 'Render'}();
        }, $this->getOption('template'));
    }
    
    /**
     * 返回渲染 CSS
     *
     * @return string
     */
    protected function css()
    {
        return sprintf('<style type="text/css">%s</style>', file_get_contents(__DIR__ . '/defaults.css'));
    }
    
    /**
     * 返回渲染 header
     *
     * @return string
     */
    protected function getHeaderRender()
    {
        return sprintf('<div class="pagination%s">', $this->getOption('small') ? ' pagination-small' : '');
    }
    
    /**
     * 返回渲染 pager.ul
     *
     * @return string
     */
    protected function getUlRender()
    {
        return '<ul class="pager">';
    }
    
    /**
     * 返回渲染 total
     *
     * @return string
     */
    protected function getTotalRender()
    {
        if (! $this->objPage->canTotalRender()) {
            return;
        }
        return sprintf('<span class="pagination-total">%s</span>', __('共 %d 条', $this->objPage->getTotalRecord() ?  : 0));
    }
    
    /**
     * 返回渲染 first
     *
     * @return string
     */
    protected function getFirstRender()
    {
        if (! $this->objPage->canFirstRender()) {
            return;
        }
        return sprintf('<li class=""><a href="%s" >1</a></li><li onclick="window.location.href=\'%s\';" class="btn-quickprev" onmouseenter="this.innerHTML=\'&laquo;\';" onmouseleave="this.innerHTML=\'...\';">...</li>', $this->replace(1), $this->replace($this->objPage->parseFirstRenderPrev()));
    }
    
    /**
     * 返回渲染 prev
     *
     * @return string
     */
    protected function getPrevRender()
    {
        if ($this->objPage->canPrevRender()) {
            return sprintf('<button class="btn-prev" onclick="window.location.href=\'%s\';">&#8249;</button>', $this->replace($this->objPage->parsePrevRenderPrev()));
        } else {
            return '<button class="btn-prev disabled">&#8249;</button>';
        }
    }
    
    /**
     * 返回渲染 main
     *
     * @return string
     */
    protected function getMainRender()
    {
        if (! $this->objPage->canMainRender()) {
            return;
        }
        
        $strMain = '';
        for ($nI = $this->objPage->getPageStart(); $nI <= $this->objPage->getPageEnd(); $nI ++) {
            $booActive = $this->objPage->getCurrentPage() == $nI;
            $strMain .= sprintf('<li class="number%s"><a%s>%d</a></li>', $booActive ? ' active' : '', $booActive ? '' : sprintf(' href="%s"', $this->replace($nI)), $nI);
        }
        return $strMain;
    }
    
    /**
     * 返回渲染 next
     *
     * @return string
     */
    protected function getNextRender()
    {
        if ($this->objPage->canNextRender()) {
            return sprintf('<button class="btn-next" onclick="window.location.href=\'%s\';">&#8250;</button>', $this->replace($this->objPage->getCurrentPage() + 1));
        } else {
            return '<button class="btn-next disabled">&#8250;</button>';
        }
    }
    
    /**
     * 返回渲染 last
     *
     * @return string
     */
    protected function getLastRender()
    {
        if ($this->objPage->isTotalInfinity()) {
            return sprintf('<li class="btn-quicknext" onclick="window.location.href=\'%s\';" onmouseenter="this.innerHTML=\'&raquo;\';" onmouseleave="this.innerHTML=\'...\';">...</li>', $this->replace($this->objPage->parseLastRenderNext()));
        }
        
        if ($this->objPage->canLastRender()) {
            return ($this->objPage->canLastRenderNext() ? sprintf('<li class="btn-quicknext" onclick="window.location.href=\'%s\';" onmouseenter="this.innerHTML=\'&raquo;\';" onmouseleave="this.innerHTML=\'...\';">...</li>', $this->replace($this->objPage->parseLastRenderNext())) : '') . sprintf('<li><a href="%s">%d</a></li>', $this->replace($this->objPage->getTotalPage()), $this->objPage->getTotalPage());
        }
    }
    
    /**
     * 返回渲染 pager.endul
     *
     * @return string
     */
    protected function getEndulRender()
    {
        return '</ul>';
    }
    
    /**
     * 返回渲染 jump
     *
     * @return string
     */
    protected function getJumpRender()
    {
        return sprintf('<span class="pagination-jump">%s<input type="number" link="%s" onkeydown="var event = event || window.event; if (event.keyCode == 13) { window.location.href = this.getAttribute(\'link\').replace( \'{jump}\', this.value); }" onfocus="this.select();" min="1" value="1" number="true" class="pagination-editor">%s</span>', __('前往'), $this->replace('{jump}'), __('页'));
    }
    
    /**
     * 返回渲染 footer
     *
     * @return string
     */
    protected function getFooterRender()
    {
        return '</div>';
    }
    
    /**
     * 替换分页变量
     *
     * @param mixed $mixPage
     * @return string
     */
    public function replace($mixPage)
    {
        return $this->objPage->pageReplace($mixPage);
    }
}
