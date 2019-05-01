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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Page;

use Leevel\I18n\Helper\gettext;
use function Leevel\I18n\Helper\gettext as __;

/**
 * BootstrapSimple 分页渲染.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.07.14
 *
 * @version 1.0
 */
class BootstrapSimple extends Bootstrap
{
    /**
     * 配置.
     *
     * @var array
     */
    protected $option = [
        // center,justify
        'align'         => 'center',
        'template'      => '{header} {ul} {prev} {next} {endul} {footer}',
        'justify_align' => false,
    ];

    /**
     * 设置两端对齐.
     *
     * @return $this
     */
    public function setJustifyAlign(): IRender
    {
        $this->setOption('align', 'justify');
    }

    /**
     * 初始化配置.
     */
    protected function intOption(): void
    {
        if ($this->option['justify_align']) {
            $this->setJustifyAlign();
        }
    }

    /**
     * 返回渲染 header.
     *
     * @return string
     */
    protected function getHeaderRender(): string
    {
        return '<nav aria-label="...">';
    }

    /**
     * 返回渲染 pager.ul.
     *
     * @return string
     */
    protected function getUlRender(): string
    {
        return '<ul class="pager">';
    }

    /**
     * 返回渲染 prev.
     *
     * @return string
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
     *
     * @return string
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
     *
     * @return string
     */
    protected function getEndulRender(): string
    {
        return '</ul>';
    }

    /**
     * 返回渲染 footer.
     *
     * @return string
     */
    protected function getFooterRender(): string
    {
        return '</nav>';
    }
}

fns(gettext::class);
