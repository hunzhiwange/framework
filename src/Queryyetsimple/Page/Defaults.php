<?php

declare(strict_types=1);

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

namespace Leevel\Page;

use Leevel\Option\TClass;

/**
 * 默认分页渲染.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.07.14
 *
 * @version 1.0
 */
class Defaults implements IRender
{
    use TClass;

    /**
     * 分页.
     *
     * @var \Leevel\Page\IPage
     */
    protected $page;

    /**
     * 配置.
     *
     * @var array
     */
    protected $option = [
        'small'    => false,
        'template' => '{header} {total} {prev} {ul} {first} {main} {last} {endul} {next} {jump} {footer}',
        'css'      => true,
    ];

    /**
     * 构造函数.
     *
     * @param \Leevel\Page\IPage $page
     * @param array              $option
     */
    public function __construct(IPage $page, array $option = [])
    {
        $this->page = $page;

        $this->options($option);

        if ($this->page->getRenderOption()) {
            $this->options(
                $this->page->getRenderOption('render')
            );
        }
    }

    /**
     * 渲染.
     *
     * @return string
     */
    public function render()
    {
        return ($this->getOption('css') ? $this->css() : '').
            preg_replace_callback(
                '/{(.+?)}/',
                function ($matches) {
                    return $this->{'get'.ucwords($matches[1]).'Render'}();
                },
                $this->getOption('template')
            );
    }

    /**
     * 替换分页变量.
     *
     * @param mixed $page
     *
     * @return string
     */
    public function replace($page)
    {
        return $this->page->pageReplace($page);
    }

    /**
     * 返回渲染 CSS.
     *
     * @return string
     */
    protected function css()
    {
        return sprintf(
            '<style type="text/css">%s</style>',
            file_get_contents(__DIR__.'/defaults.css')
        );
    }

    /**
     * 返回渲染 header.
     *
     * @return string
     */
    protected function getHeaderRender()
    {
        return sprintf(
            '<div class="pagination%s">',
            $this->getOption('small') ? ' pagination-small' : ''
        );
    }

    /**
     * 返回渲染 pager.ul.
     *
     * @return string
     */
    protected function getUlRender()
    {
        return '<ul class="pager">';
    }

    /**
     * 返回渲染 total.
     *
     * @return string
     */
    protected function getTotalRender()
    {
        if (!$this->page->canTotalRender()) {
            return;
        }

        return sprintf(
            '<span class="pagination-total">%s</span>',
            __('共 %d 条', $this->page->getTotalRecord() ?: 0)
        );
    }

    /**
     * 返回渲染 first.
     *
     * @return string
     */
    protected function getFirstRender()
    {
        if (!$this->page->canFirstRender()) {
            return;
        }

        return sprintf(
            '<li class=""><a href="%s" >1</a></li>'.
                '<li onclick="window.location.href=\'%s\';" '.
                'class="btn-quickprev" onmouseenter="this.innerHTML=\'&laquo;\';" '.
                'onmouseleave="this.innerHTML=\'...\';">...</li>',
            $this->replace(1),
            $this->replace(
                $this->page->parseFirstRenderPrev()
            )
        );
    }

    /**
     * 返回渲染 prev.
     *
     * @return string
     */
    protected function getPrevRender()
    {
        if ($this->page->canPrevRender()) {
            return sprintf(
                '<button class="btn-prev" '.
                    'onclick="window.location.href=\'%s\';">&#8249;</button>',
                $this->replace(
                    $this->page->parsePrevRenderPrev()
                )
            );
        }

        return '<button class="btn-prev disabled">'.
            '&#8249;</button>';
    }

    /**
     * 返回渲染 main.
     *
     * @return string
     */
    protected function getMainRender()
    {
        if (!$this->page->canMainRender()) {
            return;
        }

        $result = '';

        for ($i = $this->page->getPageStart();
            $i <= $this->page->getPageEnd();
            $i++) {
            $active = $this->page->getCurrentPage() === $i;

            $result .= sprintf(
                '<li class="number%s"><a%s>%d</a></li>',
                $active ? ' active' : '',
                $active ? '' : sprintf(' href="%s"', $this->replace($i)),
                $i
            );
        }

        return $result;
    }

    /**
     * 返回渲染 next.
     *
     * @return string
     */
    protected function getNextRender()
    {
        if ($this->page->canNextRender()) {
            return sprintf(
                '<button class="btn-next" '.
                    'onclick="window.location.href=\'%s\';">&#8250;</button>',
                $this->replace($this->page->getCurrentPage() + 1)
            );
        }

        return '<button class="btn-next disabled">'.
            '&#8250;</button>';
    }

    /**
     * 返回渲染 last.
     *
     * @return string
     */
    protected function getLastRender()
    {
        if ($this->page->isTotalMacro()) {
            return sprintf(
                '<li class="btn-quicknext" '.
                    'onclick="window.location.href=\'%s\';" '.
                    'onmouseenter="this.innerHTML=\'&raquo;\';" '.
                    'onmouseleave="this.innerHTML=\'...\';">...</li>',
                $this->replace(
                    $this->page->parseLastRenderNext()
                )
            );
        }

        if ($this->page->canLastRender()) {
            return ($this->page->canLastRenderNext() ?
                    sprintf(
                        '<li class="btn-quicknext" '.
                            'onclick="window.location.href=\'%s\';" '.
                            'onmouseenter="this.innerHTML=\'&raquo;\';" '.
                            'onmouseleave="this.innerHTML=\'...\';">...</li>',
                        $this->replace(
                            $this->page->parseLastRenderNext()
                        )
                    ) :
                    '').
                sprintf(
                    '<li><a href="%s">%d</a></li>',
                    $this->replace($this->page->getTotalPage()),
                    $this->page->getTotalPage()
                );
        }
    }

    /**
     * 返回渲染 pager.endul.
     *
     * @return string
     */
    protected function getEndulRender()
    {
        return '</ul>';
    }

    /**
     * 返回渲染 jump.
     *
     * @return string
     */
    protected function getJumpRender()
    {
        return sprintf(
            '<span class="pagination-jump">%s'.
                '<input type="number" link="%s" '.
                'onkeydown="var event = event || window.event; '.
                'if (event.keyCode == 13) { window.location.href = '.
                'this.getAttribute(\'link\').replace( \'{jump}\', this.value); }" '.
                'onfocus="this.select();" min="1" value="1" number="true" '.
                'class="pagination-editor">%s</span>',
            __('前往'),
            $this->replace('{jump}'),
            __('页')
        );
    }

    /**
     * 返回渲染 footer.
     *
     * @return string
     */
    protected function getFooterRender()
    {
        return '</div>';
    }
}
