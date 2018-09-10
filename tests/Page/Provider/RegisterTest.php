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

namespace Tests\Page\Provider;

use Leevel\Di\Container;
use Leevel\Http\Request;
use Leevel\Page\IPage;
use Leevel\Page\IPageFactory;
use Leevel\Page\Page;
use Leevel\Page\Provider\Register;
use Leevel\Router\Url;
use Tests\TestCase;

/**
 * register test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.26
 *
 * @version 1.0
 */
class RegisterTest extends TestCase
{
    public function testBaseUse()
    {
        $test = new Register($container = $this->createContainer());

        $test->register();

        $pageFactory = $container->make('page');

        $this->assertInstanceof(IPageFactory::class, $pageFactory);

        $page = $pageFactory->make(5, 17);

        $this->assertInstanceof(IPage::class, $page);
        $this->assertInstanceof(Page::class, $page);

        $data = <<<'eot'
<div class="pagination"> <span class="pagination-total">共 17 条</span> <button class="btn-prev disabled">&#8249;</button> <ul class="pager">  <li class="number active"><a>1</a></li><li class="number"><a href="/?page=2">2</a></li><li class="number"><a href="/?page=3">3</a></li><li class="number"><a href="/?page=4">4</a></li>  </ul> <button class="btn-next" onclick="window.location.href='/?page=2';">&#8250;</button> <span class="pagination-jump">前往<input type="number" link="/?page={jump}" onkeydown="var event = event || window.event; if (event.keyCode == 13) { window.location.href = this.getAttribute('link').replace( '{jump}', this.value); }" onfocus="this.select();" min="1" value="1" number="true" class="pagination-editor">页</span> </div>
eot;

        $this->assertSame(
            $data,
            $page->render()
        );

        $page = $pageFactory->makeMacro(5);

        $data = <<<'eot'
<div class="pagination">  <button class="btn-prev disabled">&#8249;</button> <ul class="pager">  <li class="number active"><a>1</a></li><li class="number"><a href="/?page=2">2</a></li><li class="number"><a href="/?page=3">3</a></li><li class="number"><a href="/?page=4">4</a></li><li class="number"><a href="/?page=5">5</a></li><li class="number"><a href="/?page=6">6</a></li> <li class="btn-quicknext" onclick="window.location.href='/?page=6';" onmouseenter="this.innerHTML='&raquo;';" onmouseleave="this.innerHTML='...';">...</li> </ul> <button class="btn-next" onclick="window.location.href='/?page=2';">&#8250;</button> <span class="pagination-jump">前往<input type="number" link="/?page={jump}" onkeydown="var event = event || window.event; if (event.keyCode == 13) { window.location.href = this.getAttribute('link').replace( '{jump}', this.value); }" onfocus="this.select();" min="1" value="1" number="true" class="pagination-editor">页</span> </div>
eot;

        $this->assertSame(
            $data,
            $page->render()
        );

        $page = $pageFactory->makePrevNext();

        $data = <<<'eot'
<div class="pagination">  <button class="btn-prev disabled">&#8249;</button> <ul class="pager">    </ul> <button class="btn-next" onclick="window.location.href='/?page=2';">&#8250;</button> <span class="pagination-jump">前往<input type="number" link="/?page={jump}" onkeydown="var event = event || window.event; if (event.keyCode == 13) { window.location.href = this.getAttribute('link').replace( '{jump}', this.value); }" onfocus="this.select();" min="1" value="1" number="true" class="pagination-editor">页</span> </div>
eot;

        $this->assertSame(
            $data,
            $page->render()
        );
    }

    protected function createContainer(): Container
    {
        $container = new Container();

        $url = new Url(new Request());

        $container->singleton('url', $url);

        return $container;
    }
}
