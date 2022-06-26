<?php

declare(strict_types=1);

namespace Leevel\Page;

use Leevel\I18n\Gettext;

/**
 * BootstrapSimple 分页渲染.
 */
class BootstrapSimple extends Bootstrap
{
    /**
     * 配置.
     */
    protected array $option = [
        // center,justify
        'align'         => 'center',
        'template'      => '{header} {ul} {prev} {next} {endul} {footer}',
    ];

    /**
     * 初始化配置.
     */
    protected function intOption(): void
    {
    }

    /**
     * 返回渲染 header.
     */
    protected function getHeaderRender(): string
    {
        return '<nav aria-label="...">';
    }

    /**
     * 返回渲染 pager.ul.
     */
    protected function getUlRender(): string
    {
        return '<ul class="pager">';
    }

    /**
     * 返回渲染 prev.
     */
    protected function getPrevRender(): string
    {
        if ($this->page->canPrevRender()) {
            return sprintf(
                '<li class="%s"><a aria-label="Previous" href="%s">'.
                    '<span aria-hidden="true">%s</span></a></li>',
                'justify' === $this->option['align'] ? 'previous' : '',
                $this->replace(
                    $this->page->parsePrevRenderPrev()
                ),
                __('上一页')
            );
        }

        return sprintf(
            '<li class="disabled%s"><a aria-label="Previous">'.
                '<span aria-hidden="true">%s</span></a></li>',
            'justify' === $this->option['align'] ? ' previous' : '',
            __('上一页')
        );
    }

    /**
     * 返回渲染 next.
     */
    protected function getNextRender(): string
    {
        if ($this->page->canNextRender()) {
            return sprintf(
                '<li class="%s"><a aria-label="Next" href="%s">'.
                    '<span aria-hidden="true">%s</span></a></li>',
                'justify' === $this->option['align'] ? 'next' : '',
                $this->replace(
                    $this->page->getCurrentPage() + 1
                ),
                __('下一页')
            );
        }

        return sprintf(
            '<li class="disabled%s"><a aria-label="Next">'.
                '<span aria-hidden="true">%s</span></a></li>',
            'justify' === $this->option['align'] ? ' next' : '',
            __('下一页')
        );
    }

    /**
     * 返回渲染 pager.endul.
     */
    protected function getEndulRender(): string
    {
        return '</ul>';
    }

    /**
     * 返回渲染 footer.
     */
    protected function getFooterRender(): string
    {
        return '</nav>';
    }
}

if (!function_exists(__NAMESPACE__.'\\__')) {
    function __(string $text, ...$data): string
    {
        return Gettext::handle($text, ...$data);
    }
}
