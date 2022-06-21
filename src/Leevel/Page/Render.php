<?php

declare(strict_types=1);

namespace Leevel\Page;

use Leevel\I18n\Gettext;

/**
 * 默认分页渲染.
 */
class Render implements IRender
{
    /**
     * 分页.
     */
    protected Page $page;

    /**
     * 配置.
     */
    protected array $option = [
        'small'          => false,
        'template'       => '{header} {total} {prev} {ul} {first} {main} {last} {endul} {next} {jump} {footer}',
        'small_template' => false,
    ];

    /**
     * 构造函数.
     */
    public function __construct(Page $page, array $option = [])
    {
        $this->page = $page;
        if ($option) {
            $this->option = array_merge($this->option, $option);
            $this->intOption();
        }
    }

    /**
     * 简单渲染.
     */
    public function setSimpleTemplate(): IRender
    {
        $this->option['template'] = '{header} {prev} {ul} {first} {main} {last} {endul} {next} {footer}';

        return $this;
    }

    /**
     * {@inheritDoc}
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
     */
    public function replace(int|string $page): string
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
            $content = '';
            if ($this->page->canLastRenderNext()) {
                $content = sprintf(
                    '<li class="btn-quicknext" '.
                    'onclick="window.location.href=\'%s\';" '.
                    'onmouseenter="this.innerHTML=\'&raquo;\';" '.
                    'onmouseleave="this.innerHTML=\'...\';">...</li>',
                    $this->replace($this->page->parseLastRenderNext())
                );
            }

            return $content.sprintf(
                '<li><a href="%s">%d</a></li>',
                $this->replace((int) $this->page->getTotalPage()),
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

if (!function_exists('__')) {
    function __(string $text, ...$data): string
    {
        return Gettext::handle($text, ...$data);
    }
}
