<?php

declare(strict_types=1);

namespace Leevel\Page;

/**
 * bootstrap 分页渲染.
 */
class Bootstrap implements IRender
{
    /**
     * 分页.
     */
    protected Page $page;

    /**
     * 配置.
     */
    protected array $config = [
        // lg sm
        'size' => '',
        'template' => '{header} {ul} {prev} {first} {main} {last} {next} {endul} {footer}',
        'large_size' => false,
        'small_size' => false,
    ];

    /**
     * 构造函数.
     */
    public function __construct(Page $page, array $config = [])
    {
        $this->page = $page;
        if ($config) {
            $this->config = array_merge($this->config, $config);
            $this->intConfig();
        }
    }

    /**
     * 大尺寸样式.
     */
    public function setLargeSize(): IRender
    {
        $this->config['size'] = 'lg';

        return $this;
    }

    /**
     * 小尺寸样式.
     */
    public function setSmallSize(): IRender
    {
        $this->config['size'] = 'sm';

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function render(array $config = []): string
    {
        if ($config) {
            $this->config = array_merge($this->config, $config);
            $this->intConfig();
        }

        return preg_replace_callback(
            '/{(.+?)}/',
            function ($matches) {
                return $this->{'get'.ucwords($matches[1]).'Render'}();
            },
            $this->config['template']
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
    protected function intConfig(): void
    {
        if ($this->config['large_size']) {
            $this->setLargeSize();
        } elseif ($this->config['small_size']) {
            $this->setSmallSize();
        }
    }

    /**
     * 返回渲染 header.
     */
    protected function getHeaderRender(): string
    {
        return '<nav aria-label="navigation">';
    }

    /**
     * 返回渲染 pager.ul.
     */
    protected function getUlRender(): string
    {
        return sprintf(
            '<ul class="pagination%s">',
            $this->config['size'] ?
                ' pagination-'.$this->config['size'] :
                ''
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
                '<li><a href="%s">...</a></li>',
            $this->replace(1),
            $this->replace($this->page->parseFirstRenderPrev())
        );
    }

    /**
     * 返回渲染 prev.
     */
    protected function getPrevRender(): string
    {
        if ($this->page->canPrevRender()) {
            return sprintf(
                '<li><a aria-label="Previous" href="%s">'.
                    '<span aria-hidden="true">&laquo;</span></a></li>',
                $this->replace($this->page->parsePrevRenderPrev())
            );
        }

        return '<li class="disabled"><a aria-label="Previous">'.
            '<span aria-hidden="true">&laquo;</span></a></li>';
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
            $i <= $this->page->getPageEnd(); ++$i) {
            $active = $this->page->getCurrentPage() === $i;
            $result .= sprintf(
                '<li class="%s"><a%s>%d</a></li>',
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
                '<li><a aria-label="Next" href="%s">'.
                    '<span aria-hidden="true">&raquo;</span></a></li>',
                $this->replace($this->page->getCurrentPage() + 1)
            );
        }

        return '<li class="disabled"><a aria-label="Next">'.
            '<span aria-hidden="true">&raquo;</span></a></li>';
    }

    /**
     * 返回渲染 last.
     */
    protected function getLastRender(): string
    {
        if ($this->page->isTotalMacro()) {
            return sprintf(
                '<li><a href="%s">...</a></li>',
                $this->replace($this->page->parseLastRenderNext())
            );
        }

        if ($this->page->canLastRender()) {
            $content = '';
            if ($this->page->canLastRenderNext()) {
                $content = sprintf('<li><a href="%s">...</a></li>', $this->replace($this->page->parseLastRenderNext()));
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
     * 返回渲染 footer.
     */
    protected function getFooterRender(): string
    {
        return '</nav>';
    }
}
