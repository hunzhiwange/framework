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
 * bootstrap 分页渲染.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.07.14
 *
 * @version 1.0
 */
class Bootstrap implements IRender
{
    use TClass;

    /**
     * 分页.
     *
     * @var \Leevel\Page\IPage
     */
    protected $objPage;

    /**
     * 配置.
     *
     * @var array
     */
    protected $arrOption = [
        // lg sm
        'size' => '',
        'template' => '{header} {ul} {prev} {first} {main} {last} {next} {endul} {footer}',
        'css' => true,
    ];

    /**
     * 构造函数.
     *
     * @param \Leevel\Page\IPage $objPage
     * @param array              $arrOption
     */
    public function __construct(IPage $objPage, array $arrOption = [])
    {
        $this->objPage = $objPage;
        $this->options($arrOption);
        if ($this->objPage->getRenderOption()) {
            $this->options($this->objPage->getRenderOption('render'));
        }
    }

    /**
     * 渲染.
     *
     * @return string
     */
    public function render()
    {
        return ($this->getOption('css') ? $this->css() : '') . preg_replace_callback('/{(.+?)}/', function ($arrMatche) {
            return $this->{'get' . ucwords($arrMatche[1]) . 'Render'}();
        }, $this->getOption('template'));
    }

    /**
     * 返回渲染 CSS.
     *
     * @return string
     */
    protected function css()
    {
        return '<link href="http://v3.bootcss.com/dist/css/bootstrap.min.css" rel="stylesheet">';
    }

    /**
     * 返回渲染 header.
     *
     * @return string
     */
    protected function getHeaderRender()
    {
        return '<nav aria-label="navigation">';
    }

    /**
     * 返回渲染 pager.ul.
     *
     * @return string
     */
    protected function getUlRender()
    {
        return sprintf('<ul class="pagination%s">', $this->getOption('size') ? ' pagination-' . $this->getOption('size') : '');
    }

    /**
     * 返回渲染 first.
     *
     * @return string
     */
    protected function getFirstRender()
    {
        if (! $this->objPage->canFirstRender()) {
            return;
        }

        return sprintf('<li class=""><a href="%s" >1</a></li><li><a href="%s">...</a></li>', $this->replace(1), $this->replace($this->objPage->parseFirstRenderPrev()));
    }

    /**
     * 返回渲染 prev.
     *
     * @return string
     */
    protected function getPrevRender()
    {
        if ($this->objPage->canPrevRender()) {
            return sprintf('<li><a aria-label="Previous" href="%s"><span aria-hidden="true">&laquo;</span></a></li>', $this->replace($this->objPage->parsePrevRenderPrev()));
        } else {
            return '<li class="disabled"><a aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
        }
    }

    /**
     * 返回渲染 main.
     *
     * @return string
     */
    protected function getMainRender()
    {
        if (! $this->objPage->canMainRender()) {
            return;
        }

        $strMain = '';
        for ($nI = $this->objPage->getPageStart(); $nI <= $this->objPage->getPageEnd(); ++$nI) {
            $booActive = $this->objPage->getCurrentPage() == $nI;
            $strMain .= sprintf('<li class="%s"><a%s>%d</a></li>', $booActive ? ' active' : '', $booActive ? '' : sprintf(' href="%s"', $this->replace($nI)), $nI);
        }

        return $strMain;
    }

    /**
     * 返回渲染 next.
     *
     * @return string
     */
    protected function getNextRender()
    {
        if ($this->objPage->canNextRender()) {
            return sprintf('<li><a aria-label="Next" href="%s"><span aria-hidden="true">&raquo;</span></a></li>', $this->replace($this->objPage->getCurrentPage() + 1));
        } else {
            return '<li class="disabled"><a aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
        }
    }

    /**
     * 返回渲染 last.
     *
     * @return string
     */
    protected function getLastRender()
    {
        if ($this->objPage->isTotalMacro()) {
            return sprintf('<li><a href="%s">...</a></li>', $this->replace($this->objPage->parseLastRenderNext()));
        }

        if ($this->objPage->canLastRender()) {
            return ($this->objPage->canLastRenderNext() ? sprintf('<li><a href="%s">...</a></li>', $this->replace($this->objPage->parseLastRenderNext())) : '') . sprintf('<li><a href="%s">%d</a></li>', $this->replace($this->objPage->getTotalPage()), $this->objPage->getTotalPage());
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
     * 返回渲染 footer.
     *
     * @return string
     */
    protected function getFooterRender()
    {
        return '</nav>';
    }

    /**
     * 替换分页变量.
     *
     * @param mixed $mixPage
     *
     * @return string
     */
    public function replace($mixPage)
    {
        return $this->objPage->pageReplace($mixPage);
    }
}
