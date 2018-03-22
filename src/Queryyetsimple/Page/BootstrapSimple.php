<?php
/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Queryyetsimple\Page;

/**
 * BootstrapSimple 分页渲染
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.07.14
 * @version 1.0
 */
class BootstrapSimple extends Bootstrap
{

    /**
     * 配置
     *
     * @var array
     */
    protected $arrOption = [
        // center,justify
        'align' => 'center',
        'template' => '{header} {ul} {prev} {next} {endul} {footer}',
        'css' => true
    ];

    /**
     * 返回渲染 header
     *
     * @return string
     */
    protected function getHeaderRender()
    {
        return '<nav aria-label="...">';
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
     * 返回渲染 prev
     *
     * @return string
     */
    protected function getPrevRender()
    {
        if ($this->objPage->canPrevRender()) {
            return sprintf('<li class="%s"><a aria-label="Previous" href="%s"><span aria-hidden="true">%s</span></a></li>', $this->getOption('align') == 'justify' ? 'previous' : '', $this->replace($this->objPage->parsePrevRenderPrev()), __('上一页'));
        } else {
            return sprintf('<li class="disabled%s"><a aria-label="Previous"><span aria-hidden="true">%s</span></a></li>', $this->getOption('align') == 'justify' ? ' previous' : '', __('上一页'));
        }
    }

    /**
     * 返回渲染 next
     *
     * @return string
     */
    protected function getNextRender()
    {
        if ($this->objPage->canNextRender()) {
            return sprintf('<li class="%s"><a aria-label="Next" href="%s"><span aria-hidden="true">%s</span></a></li>', $this->getOption('align') == 'justify' ? 'next' : '', $this->replace($this->objPage->getCurrentPage() + 1), __('下一页'));
        } else {
            return sprintf('<li class="disabled%s"><a aria-label="Next"><span aria-hidden="true">%s</span></a></li>', $this->getOption('align') == 'justify' ? ' next' : '', __('下一页'));
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
     * 返回渲染 footer
     *
     * @return string
     */
    protected function getFooterRender()
    {
        return '</nav>';
    }
}
