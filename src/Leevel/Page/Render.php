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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Page;

use Leevel\I18n\Helper\gettext;
use function Leevel\I18n\Helper\gettext as __;

/**
 * 默认分页渲染.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.07.14
 *
 * @version 1.0
 */
class Render implements IRender
{
    /**
     * 分页.
     *
     * @var \Leevel\Page\IPage
     */
    protected IPage $page;

    /**
     * 配置.
     *
     * @var array
     */
    protected array $option = [
        'small'          => false,
        'template'       => '{header} {total} {prev} {ul} {first} {main} {last} {endul} {next} {jump} {footer}',
        'small_template' => false,
    ];

    /**
     * 构造函数.
     *
     * @param \Leevel\Page\IPage $page
     */
    public function __construct(IPage $page, array $option = [])
    {
        $this->page = $page;
        if ($option) {
            $this->option = array_merge($this->option, $option);
            $this->intOption();
        }
    }

    /**
     * 设置配置.
     *
     * @param mixed $value
     *
     * @return \Leevel\Page\IRender
     */
    public function setOption(string $name, $value): IRender
    {
        $this->option[$name] = $value;

        return $this;
    }

    /**
     * 简单渲染.
     *
     * @return \Leevel\Page\IRender
     */
    public function setSimpleTemplate(): IRender
    {
        return $this->setOption(
            'template',
            '{header} {prev} {ul} {first} {main} {last} {endul} {next} {footer}'
        );
    }

    /**
     * 渲染.
     */
    public function render(array $option = []): string
    {
        if ($option) {
            $this->option = array_merge($this->option, $option);
            $this->intOption();
        }

        return preg_replace_callback(
            '/{(.+?)}/',
            function ($matches) {
                return $this->{'get'.ucwords($matches[1]).'Render'}();
            },
            $this->option['template']
        );
    }

    /**
     * 替换分页变量.
     *
     * @param int|string $page
     */
    public function replace($page): string
    {
        return $this->page->pageReplace($page);
    }

    /**
     * 初始化配置.
     */
    protected function intOption(): void
    {
        if ($this->option['small_template']) {
            $this->setSimpleTemplate();
        }
    }

    /**
     * 返回渲染 header.
     */
    protected function getHeaderRender(): string
    {
        return sprintf(
            '<div class="pagination%s">',
            $this->option['small'] ? ' pagination-small' : ''
        );
    }

    /**
     * 返回渲染 pager.ul.
     */
    protected function getUlRender(): string
    {
        return '<ul class="pager">';
    }

    /**
     * 返回渲染 total.
     */
    protected function getTotalRender(): string
    {
        if (!$this->page->canTotalRender()) {
            return '';
        }

        return sprintf(
            '<span class="pagination-total">%s</span>',
            __('共 %d 条', $this->page->getTotalRecord() ?: 0)
        );
    }

    /**
     * 返回渲染 first.
     */
    protected function getFirstRender(): string
    {
        if (!$this->page->canFirstRender()) {
            return '';
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
     */
    protected function getPrevRender(): string
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
     */
    protected function getMainRender(): string
    {
        if (!$this->page->canMainRender()) {
            return '';
        }

        $result = '';
        for ($i = $this->page->getPageStart();
            $i <= $this->page->getPageEnd(); $i++) {
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
     */
    protected function getNextRender(): string
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
     */
    protected function getLastRender(): string
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
                    sprintf('<li class="btn-quicknext" '.
                        'onclick="window.location.href=\'%s\';" '.
                        'onmouseenter="this.innerHTML=\'&raquo;\';" '.
                        'onmouseleave="this.innerHTML=\'...\';">...</li>',
                        $this->replace($this->page->parseLastRenderNext())) : '').
                sprintf(
                    '<li><a href="%s">%d</a></li>',
                    $this->replace($this->page->getTotalPage()),
                    $this->page->getTotalPage()
                );
        }

        return '';
    }

    /**
     * 返回渲染 pager.endul.
     */
    protected function getEndulRender(): string
    {
        return '</ul>';
    }

    /**
     * 返回渲染 jump.
     */
    protected function getJumpRender(): string
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
     */
    protected function getFooterRender(): string
    {
        return '</div>';
    }
}

// import fn.
class_exists(gettext::class);
