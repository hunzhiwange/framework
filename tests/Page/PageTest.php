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

namespace Tests\Page;

use I18nMock;
use Leevel\Di\Container;
use Leevel\Page\Bootstrap;
use Leevel\Page\Page;
use Leevel\Page\Render;
use Tests\TestCase;

/**
 * @api(
 *     title="Page",
 *     zh-CN:title="分页",
 *     zh-TW:title="分頁",
 *     path="component/page",
 *     zh-CN:description="
 * QueryPHP 提供的分页组件，可以轻松地对数据进行分页处理。
 * ",
 * )
 */
class PageTest extends TestCase
{
    protected function setUp(): void
    {
        $container = Container::singletons();
        $container->clear();

        $container->singleton('i18n', function (): I18nMock {
            return new I18nMock();
        });
    }

    protected function tearDown(): void
    {
        Container::singletons()->clear();
    }

    /**
     * @api(
     *     zh-CN:title="render 分页基本使用",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $page = new Page(1, 10, 52);

        $data = <<<'eot'
            <div class="pagination"> <span class="pagination-total">共 52 条</span> <button class="btn-prev disabled">&#8249;</button> <ul class="pager">  <li class="number active"><a>1</a></li><li class="number"><a href="?page=2">2</a></li><li class="number"><a href="?page=3">3</a></li><li class="number"><a href="?page=4">4</a></li><li class="number"><a href="?page=5">5</a></li><li class="number"><a href="?page=6">6</a></li>  </ul> <button class="btn-next" onclick="window.location.href='?page=2';">&#8250;</button> <span class="pagination-jump">前往<input type="number" link="?page={jump}" onkeydown="var event = event || window.event; if (event.keyCode == 13) { window.location.href = this.getAttribute('link').replace( '{jump}', this.value); }" onfocus="this.select();" min="1" value="1" number="true" class="pagination-editor">页</span> </div>
            eot;

        $this->assertSame(
            $data,
            $page->render()
        );

        $this->assertSame(
            $data,
            $page->toHtml()
        );

        $this->assertSame(
            $data,
            $page->__toString()
        );

        $this->assertSame(
            $data,
            (string) ($page)
        );

        $data = <<<'eot'
            {
                "per_page": 10,
                "current_page": 1,
                "total_page": 6,
                "total_record": 52,
                "total_macro": false,
                "from": 0,
                "to": 10
            }
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $page->toArray()
            )
        );

        $this->assertSame(
            $data,
            $this->varJson(
                $page->jsonSerialize()
            )
        );

        $data = <<<'eot'
            {"per_page":10,"current_page":1,"total_page":6,"total_record":52,"total_macro":false,"from":0,"to":10}
            eot;

        $this->assertSame(
            $data,
            $page->toJson()
        );
    }

    /**
     * @api(
     *     zh-CN:title="分页页码必须大于 0",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testCurrentPageIsZero(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Current page must great than 0.'
        );

        $page = new Page(0, 10, 12);
        $page->render();
    }

    public function testWithCurrentPage(): void
    {
        $page = new Page(2, 10, 52);

        $data = <<<'eot'
            <div class="pagination"> <span class="pagination-total">共 52 条</span> <button class="btn-prev" onclick="window.location.href='?page=1';">&#8249;</button> <ul class="pager">  <li class="number"><a href="?page=1">1</a></li><li class="number active"><a>2</a></li><li class="number"><a href="?page=3">3</a></li><li class="number"><a href="?page=4">4</a></li><li class="number"><a href="?page=5">5</a></li><li class="number"><a href="?page=6">6</a></li>  </ul> <button class="btn-next" onclick="window.location.href='?page=3';">&#8250;</button> <span class="pagination-jump">前往<input type="number" link="?page={jump}" onkeydown="var event = event || window.event; if (event.keyCode == 13) { window.location.href = this.getAttribute('link').replace( '{jump}', this.value); }" onfocus="this.select();" min="1" value="1" number="true" class="pagination-editor">页</span> </div>
            eot;

        $this->assertSame(
            $data,
            $page->render()
        );

        $this->assertSame(
            $data,
            $page->toHtml()
        );

        $this->assertSame(
            $data,
            $page->__toString()
        );

        $this->assertSame(
            $data,
            (string) ($page)
        );

        $data = <<<'eot'
            {
                "per_page": 10,
                "current_page": 2,
                "total_page": 6,
                "total_record": 52,
                "total_macro": false,
                "from": 10,
                "to": 20
            }
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $page->toArray()
            )
        );

        $this->assertSame(
            $data,
            $this->varJson(
                $page->jsonSerialize()
            )
        );

        $data = <<<'eot'
            {"per_page":10,"current_page":2,"total_page":6,"total_record":52,"total_macro":false,"from":10,"to":20}
            eot;

        $this->assertSame(
            $data,
            $page->toJson()
        );
    }

    /**
     * @api(
     *     zh-CN:title="fragment.getFragment 分页 URL 描点",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testFragment(): void
    {
        $page = new Page(1, 10, 52);
        $page->fragment('hello');

        $this->assertSame('hello', $page->getFragment());

        $data = <<<'eot'
            <div class="pagination"> <span class="pagination-total">共 52 条</span> <button class="btn-prev disabled">&#8249;</button> <ul class="pager">  <li class="number active"><a>1</a></li><li class="number"><a href="?page=2#hello">2</a></li><li class="number"><a href="?page=3#hello">3</a></li><li class="number"><a href="?page=4#hello">4</a></li><li class="number"><a href="?page=5#hello">5</a></li><li class="number"><a href="?page=6#hello">6</a></li>  </ul> <button class="btn-next" onclick="window.location.href='?page=2#hello';">&#8250;</button> <span class="pagination-jump">前往<input type="number" link="?page={jump}#hello" onkeydown="var event = event || window.event; if (event.keyCode == 13) { window.location.href = this.getAttribute('link').replace( '{jump}', this.value); }" onfocus="this.select();" min="1" value="1" number="true" class="pagination-editor">页</span> </div>
            eot;

        $this->assertSame(
            $data,
            $page->render()
        );

        $data = <<<'eot'
            <nav aria-label="navigation"> <ul class="pagination"> <li class="disabled"><a aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>  <li class=" active"><a>1</a></li><li class=""><a href="?page=2#hello">2</a></li><li class=""><a href="?page=3#hello">3</a></li><li class=""><a href="?page=4#hello">4</a></li><li class=""><a href="?page=5#hello">5</a></li><li class=""><a href="?page=6#hello">6</a></li>  <li><a aria-label="Next" href="?page=2#hello"><span aria-hidden="true">&raquo;</span></a></li> </ul> </nav>
            eot;

        $this->assertSame(
            $data,
            $page->render('bootstrap')
        );

        $data = <<<'eot'
            <nav aria-label="..."> <ul class="pager"> <li class="disabled"><a aria-label="Previous"><span aria-hidden="true">上一页</span></a></li> <li class=""><a aria-label="Next" href="?page=2#hello"><span aria-hidden="true">下一页</span></a></li> </ul> </nav>
            eot;

        $this->assertSame(
            $data,
            $page->render('bootstrapSimple')
        );
    }

    /**
     * @api(
     *     zh-CN:title="perPage.getPerPage 每页分页数量",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testPerPage(): void
    {
        $page = new Page(1, 10, 52);
        $this->assertSame(10, $page->getPerPage());
        $page->perPage(20);
        $this->assertSame(20, $page->getPerPage());

        $data = <<<'eot'
            <div class="pagination"> <span class="pagination-total">共 52 条</span> <button class="btn-prev disabled">&#8249;</button> <ul class="pager">  <li class="number active"><a>1</a></li><li class="number"><a href="?page=2">2</a></li><li class="number"><a href="?page=3">3</a></li>  </ul> <button class="btn-next" onclick="window.location.href='?page=2';">&#8250;</button> <span class="pagination-jump">前往<input type="number" link="?page={jump}" onkeydown="var event = event || window.event; if (event.keyCode == 13) { window.location.href = this.getAttribute('link').replace( '{jump}', this.value); }" onfocus="this.select();" min="1" value="1" number="true" class="pagination-editor">页</span> </div>
            eot;

        $this->assertSame(
            $data,
            $page->render()
        );

        $data = <<<'eot'
            <nav aria-label="navigation"> <ul class="pagination"> <li class="disabled"><a aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>  <li class=" active"><a>1</a></li><li class=""><a href="?page=2">2</a></li><li class=""><a href="?page=3">3</a></li>  <li><a aria-label="Next" href="?page=2"><span aria-hidden="true">&raquo;</span></a></li> </ul> </nav>
            eot;

        $this->assertSame(
            $data,
            $page->render('bootstrap')
        );

        $data = <<<'eot'
            <nav aria-label="..."> <ul class="pager"> <li class="disabled"><a aria-label="Previous"><span aria-hidden="true">上一页</span></a></li> <li class=""><a aria-label="Next" href="?page=2"><span aria-hidden="true">下一页</span></a></li> </ul> </nav>
            eot;

        $this->assertSame(
            $data,
            $page->render('bootstrapSimple')
        );
    }

    /**
     * @api(
     *     zh-CN:title="分页渲染配置",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testSetSmallTemplate(): void
    {
        $page = new Page(1, 10, 52, [
            'render_option' => ['small_template' => true],
        ]);

        $data = <<<'eot'
            <div class="pagination"> <button class="btn-prev disabled">&#8249;</button> <ul class="pager">  <li class="number active"><a>1</a></li><li class="number"><a href="?page=2">2</a></li><li class="number"><a href="?page=3">3</a></li><li class="number"><a href="?page=4">4</a></li><li class="number"><a href="?page=5">5</a></li><li class="number"><a href="?page=6">6</a></li>  </ul> <button class="btn-next" onclick="window.location.href='?page=2';">&#8250;</button> </div>
            eot;

        $this->assertSame(
            $data,
            $page->render()
        );

        $data = <<<'eot'
            <nav aria-label="navigation"> <ul class="pagination"> <li class="disabled"><a aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>  <li class=" active"><a>1</a></li><li class=""><a href="?page=2">2</a></li><li class=""><a href="?page=3">3</a></li><li class=""><a href="?page=4">4</a></li><li class=""><a href="?page=5">5</a></li><li class=""><a href="?page=6">6</a></li>  <li><a aria-label="Next" href="?page=2"><span aria-hidden="true">&raquo;</span></a></li> </ul> </nav>
            eot;

        $this->assertSame(
            $data,
            $page->render('bootstrap')
        );

        $data = <<<'eot'
            <nav aria-label="..."> <ul class="pager"> <li class="disabled"><a aria-label="Previous"><span aria-hidden="true">上一页</span></a></li> <li class=""><a aria-label="Next" href="?page=2"><span aria-hidden="true">下一页</span></a></li> </ul> </nav>
            eot;

        $this->assertSame(
            $data,
            $page->render('bootstrapSimple')
        );
    }

    /**
     * @api(
     *     zh-CN:title="append.addParam.appends 追加分页条件",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testAppend(): void
    {
        $page = new Page(1, 5, 3);
        $page->append('foo', 'bar');
        $page->addParam('foo1', 'bar1');
        $page->appends(['hello' => 'world']);

        $data = <<<'eot'
            <div class="pagination"> <span class="pagination-total">共 3 条</span> <button class="btn-prev disabled">&#8249;</button> <ul class="pager">    </ul> <button class="btn-next disabled">&#8250;</button> <span class="pagination-jump">前往<input type="number" link="?foo=bar&foo1=bar1&hello=world&page={jump}" onkeydown="var event = event || window.event; if (event.keyCode == 13) { window.location.href = this.getAttribute('link').replace( '{jump}', this.value); }" onfocus="this.select();" min="1" value="1" number="true" class="pagination-editor">页</span> </div>
            eot;

        $this->assertSame(
            $data,
            $page->render()
        );

        $page->param(['hello' => 'world']);

        $data = <<<'eot'
            <div class="pagination"> <span class="pagination-total">共 3 条</span> <button class="btn-prev disabled">&#8249;</button> <ul class="pager">    </ul> <button class="btn-next disabled">&#8250;</button> <span class="pagination-jump">前往<input type="number" link="?hello=world&page={jump}" onkeydown="var event = event || window.event; if (event.keyCode == 13) { window.location.href = this.getAttribute('link').replace( '{jump}', this.value); }" onfocus="this.select();" min="1" value="1" number="true" class="pagination-editor">页</span> </div>
            eot;

        $this->assertSame(
            $data,
            $page->render()
        );

        $data = <<<'eot'
            <nav aria-label="navigation"> <ul class="pagination"> <li class="disabled"><a aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>    <li class="disabled"><a aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li> </ul> </nav>
            eot;

        $this->assertSame(
            $data,
            $page->render('bootstrap')
        );

        $data = <<<'eot'
            <nav aria-label="..."> <ul class="pager"> <li class="disabled"><a aria-label="Previous"><span aria-hidden="true">上一页</span></a></li> <li class="disabled"><a aria-label="Next"><span aria-hidden="true">下一页</span></a></li> </ul> </nav>
            eot;

        $this->assertSame(
            $data,
            $page->render('bootstrapSimple')
        );
    }

    /**
     * @api(
     *     zh-CN:title="renderOption 设置渲染参数",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testRenderOption(): void
    {
        $page = new Page(1, 5, 3);

        $data = <<<'eot'
            <div class="pagination"> <span class="pagination-total">共 3 条</span> <button class="btn-prev disabled">&#8249;</button> <ul class="pager">    </ul> <button class="btn-next disabled">&#8250;</button> <span class="pagination-jump">前往<input type="number" link="?page={jump}" onkeydown="var event = event || window.event; if (event.keyCode == 13) { window.location.href = this.getAttribute('link').replace( '{jump}', this.value); }" onfocus="this.select();" min="1" value="1" number="true" class="pagination-editor">页</span> </div>
            eot;

        $this->assertSame(
            $data,
            $page->render()
        );

        $page->renderOption('small', true);

        $data = <<<'eot'
            <div class="pagination pagination-small"> <span class="pagination-total">共 3 条</span> <button class="btn-prev disabled">&#8249;</button> <ul class="pager">    </ul> <button class="btn-next disabled">&#8250;</button> <span class="pagination-jump">前往<input type="number" link="?page={jump}" onkeydown="var event = event || window.event; if (event.keyCode == 13) { window.location.href = this.getAttribute('link').replace( '{jump}', this.value); }" onfocus="this.select();" min="1" value="1" number="true" class="pagination-editor">页</span> </div>
            eot;

        $this->assertSame(
            $data,
            $page->render()
        );

        $page->renderOptions(
            ['small' => true, 'template' => '{prev} {ul} {first} {main} {last} {endul} {next}']
        );

        $data = <<<'eot'
            <button class="btn-prev disabled">&#8249;</button> <ul class="pager">    </ul> <button class="btn-next disabled">&#8250;</button>
            eot;

        $this->assertSame(
            $data,
            $page->render()
        );

        $data = <<<'eot'
            <li class="disabled"><a aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li> <ul class="pagination">    </ul> <li class="disabled"><a aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>
            eot;

        $this->assertSame(
            $data,
            $page->render('bootstrap')
        );

        $data = <<<'eot'
            <li class="disabled"><a aria-label="Previous"><span aria-hidden="true">上一页</span></a></li> <ul class="pager">    </ul> <li class="disabled"><a aria-label="Next"><span aria-hidden="true">下一页</span></a></li>
            eot;

        $this->assertSame(
            $data,
            $page->render('bootstrapSimple')
        );
    }

    /**
     * @api(
     *     zh-CN:title="url 设置 URL",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testUrl(): void
    {
        $page = new Page(1, 3, 5);
        $page->url('/hello');

        $data = <<<'eot'
            <div class="pagination"> <span class="pagination-total">共 5 条</span> <button class="btn-prev disabled">&#8249;</button> <ul class="pager">  <li class="number active"><a>1</a></li><li class="number"><a href="/hello?page=2">2</a></li>  </ul> <button class="btn-next" onclick="window.location.href='/hello?page=2';">&#8250;</button> <span class="pagination-jump">前往<input type="number" link="/hello?page={jump}" onkeydown="var event = event || window.event; if (event.keyCode == 13) { window.location.href = this.getAttribute('link').replace( '{jump}', this.value); }" onfocus="this.select();" min="1" value="1" number="true" class="pagination-editor">页</span> </div>
            eot;

        $this->assertSame(
            $data,
            $page->render()
        );
    }

    /**
     * @api(
     *     zh-CN:title="setRender 设置渲染组件",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testSetRender(): void
    {
        $page = new Page(1, 3, 5);
        $page->setRender('bootstrap');

        $data = <<<'eot'
            <nav aria-label="navigation"> <ul class="pagination"> <li class="disabled"><a aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>  <li class=" active"><a>1</a></li><li class=""><a href="?page=2">2</a></li>  <li><a aria-label="Next" href="?page=2"><span aria-hidden="true">&raquo;</span></a></li> </ul> </nav>
            eot;

        $this->assertSame(
            $data,
            $page->render()
        );
    }

    /**
     * @api(
     *     zh-CN:title="默认每页分页数量",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testDefaultPerPage(): void
    {
        $page = new Page(1, null, 25);

        $data = <<<'eot'
            <div class="pagination"> <span class="pagination-total">共 25 条</span> <button class="btn-prev disabled">&#8249;</button> <ul class="pager">  <li class="number active"><a>1</a></li><li class="number"><a href="?page=2">2</a></li>  </ul> <button class="btn-next" onclick="window.location.href='?page=2';">&#8250;</button> <span class="pagination-jump">前往<input type="number" link="?page={jump}" onkeydown="var event = event || window.event; if (event.keyCode == 13) { window.location.href = this.getAttribute('link').replace( '{jump}', this.value); }" onfocus="this.select();" min="1" value="1" number="true" class="pagination-editor">页</span> </div>
            eot;

        $this->assertSame(
            $data,
            $page->render()
        );
    }

    /**
     * @api(
     *     zh-CN:title="pageName.getPageName 分页名字",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testPageName(): void
    {
        $page = new Page(1, 10, 25);

        $this->assertSame('page', $page->getPageName());

        $page->pageName('page2');

        $this->assertSame('page2', $page->getPageName());

        $data = <<<'eot'
            <div class="pagination"> <span class="pagination-total">共 25 条</span> <button class="btn-prev disabled">&#8249;</button> <ul class="pager">  <li class="number active"><a>1</a></li><li class="number"><a href="?page2=2">2</a></li><li class="number"><a href="?page2=3">3</a></li>  </ul> <button class="btn-next" onclick="window.location.href='?page2=2';">&#8250;</button> <span class="pagination-jump">前往<input type="number" link="?page2={jump}" onkeydown="var event = event || window.event; if (event.keyCode == 13) { window.location.href = this.getAttribute('link').replace( '{jump}', this.value); }" onfocus="this.select();" min="1" value="1" number="true" class="pagination-editor">页</span> </div>
            eot;

        $this->assertSame(
            $data,
            $page->render()
        );
    }

    public function testParseLastRenderNext(): void
    {
        $page = new Page(1, 3, 30);
        $page->currentPage(3);

        $data = <<<'eot'
            <div class="pagination"> <span class="pagination-total">共 30 条</span> <button class="btn-prev" onclick="window.location.href='?page=2';">&#8249;</button> <ul class="pager">  <li class="number"><a href="?page=1">1</a></li><li class="number"><a href="?page=2">2</a></li><li class="number active"><a>3</a></li><li class="number"><a href="?page=4">4</a></li><li class="number"><a href="?page=5">5</a></li><li class="number"><a href="?page=6">6</a></li> <li class="btn-quicknext" onclick="window.location.href='?page=8';" onmouseenter="this.innerHTML='&raquo;';" onmouseleave="this.innerHTML='...';">...</li><li><a href="?page=10">10</a></li> </ul> <button class="btn-next" onclick="window.location.href='?page=4';">&#8250;</button> <span class="pagination-jump">前往<input type="number" link="?page={jump}" onkeydown="var event = event || window.event; if (event.keyCode == 13) { window.location.href = this.getAttribute('link').replace( '{jump}', this.value); }" onfocus="this.select();" min="1" value="1" number="true" class="pagination-editor">页</span> </div>
            eot;

        $this->assertSame(
            $data,
            $page->render()
        );

        $data = <<<'eot'
            <nav aria-label="navigation"> <ul class="pagination"> <li><a aria-label="Previous" href="?page=2"><span aria-hidden="true">&laquo;</span></a></li>  <li class=""><a href="?page=1">1</a></li><li class=""><a href="?page=2">2</a></li><li class=" active"><a>3</a></li><li class=""><a href="?page=4">4</a></li><li class=""><a href="?page=5">5</a></li><li class=""><a href="?page=6">6</a></li> <li><a href="?page=8">...</a></li><li><a href="?page=10">10</a></li> <li><a aria-label="Next" href="?page=4"><span aria-hidden="true">&raquo;</span></a></li> </ul> </nav>
            eot;

        $this->assertSame(
            $data,
            $page->render('bootstrap')
        );

        $page->currentPage(6);

        $data = <<<'eot'
            <div class="pagination"> <span class="pagination-total">共 30 条</span> <button class="btn-prev" onclick="window.location.href='?page=5';">&#8249;</button> <ul class="pager"> <li class=""><a href="?page=1" >1</a></li><li onclick="window.location.href='?page=1';" class="btn-quickprev" onmouseenter="this.innerHTML='&laquo;';" onmouseleave="this.innerHTML='...';">...</li> <li class="number"><a href="?page=1">1</a></li><li class="number"><a href="?page=2">2</a></li><li class="number"><a href="?page=3">3</a></li><li class="number"><a href="?page=4">4</a></li><li class="number"><a href="?page=5">5</a></li><li class="number active"><a>6</a></li> <li class="btn-quicknext" onclick="window.location.href='?page=10';" onmouseenter="this.innerHTML='&raquo;';" onmouseleave="this.innerHTML='...';">...</li><li><a href="?page=10">10</a></li> </ul> <button class="btn-next" onclick="window.location.href='?page=7';">&#8250;</button> <span class="pagination-jump">前往<input type="number" link="?page={jump}" onkeydown="var event = event || window.event; if (event.keyCode == 13) { window.location.href = this.getAttribute('link').replace( '{jump}', this.value); }" onfocus="this.select();" min="1" value="1" number="true" class="pagination-editor">页</span> </div>
            eot;

        $this->assertSame(
            $data,
            $page->render()
        );

        $data = <<<'eot'
            <nav aria-label="navigation"> <ul class="pagination"> <li><a aria-label="Previous" href="?page=5"><span aria-hidden="true">&laquo;</span></a></li> <li class=""><a href="?page=1" >1</a></li><li><a href="?page=1">...</a></li> <li class=""><a href="?page=1">1</a></li><li class=""><a href="?page=2">2</a></li><li class=""><a href="?page=3">3</a></li><li class=""><a href="?page=4">4</a></li><li class=""><a href="?page=5">5</a></li><li class=" active"><a>6</a></li> <li><a href="?page=10">...</a></li><li><a href="?page=10">10</a></li> <li><a aria-label="Next" href="?page=7"><span aria-hidden="true">&raquo;</span></a></li> </ul> </nav>
            eot;

        $this->assertSame(
            $data,
            $page->render('bootstrap')
        );

        $data = <<<'eot'
            <nav aria-label="..."> <ul class="pager"> <li class=""><a aria-label="Previous" href="?page=5"><span aria-hidden="true">上一页</span></a></li> <li class=""><a aria-label="Next" href="?page=7"><span aria-hidden="true">下一页</span></a></li> </ul> </nav>
            eot;

        $this->assertSame(
            $data,
            $page->render('bootstrapSimple')
        );
    }

    /**
     * @api(
     *     zh-CN:title="range 分页范围",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testRange(): void
    {
        $page = new Page(1, 3, 40);
        $page->currentPage(7);
        $page->range(4);

        $data = <<<'eot'
            <div class="pagination"> <span class="pagination-total">共 40 条</span> <button class="btn-prev" onclick="window.location.href='?page=6';">&#8249;</button> <ul class="pager">  <li class="number"><a href="?page=1">1</a></li><li class="number"><a href="?page=2">2</a></li><li class="number"><a href="?page=3">3</a></li><li class="number"><a href="?page=4">4</a></li><li class="number"><a href="?page=5">5</a></li><li class="number"><a href="?page=6">6</a></li><li class="number active"><a>7</a></li><li class="number"><a href="?page=8">8</a></li><li class="number"><a href="?page=9">9</a></li><li class="number"><a href="?page=10">10</a></li> <li class="btn-quicknext" onclick="window.location.href='?page=14';" onmouseenter="this.innerHTML='&raquo;';" onmouseleave="this.innerHTML='...';">...</li><li><a href="?page=14">14</a></li> </ul> <button class="btn-next" onclick="window.location.href='?page=8';">&#8250;</button> <span class="pagination-jump">前往<input type="number" link="?page={jump}" onkeydown="var event = event || window.event; if (event.keyCode == 13) { window.location.href = this.getAttribute('link').replace( '{jump}', this.value); }" onfocus="this.select();" min="1" value="1" number="true" class="pagination-editor">页</span> </div>
            eot;

        $this->assertSame(
            $data,
            $page->render()
        );

        $data = <<<'eot'
            <nav aria-label="navigation"> <ul class="pagination"> <li><a aria-label="Previous" href="?page=6"><span aria-hidden="true">&laquo;</span></a></li>  <li class=""><a href="?page=1">1</a></li><li class=""><a href="?page=2">2</a></li><li class=""><a href="?page=3">3</a></li><li class=""><a href="?page=4">4</a></li><li class=""><a href="?page=5">5</a></li><li class=""><a href="?page=6">6</a></li><li class=" active"><a>7</a></li><li class=""><a href="?page=8">8</a></li><li class=""><a href="?page=9">9</a></li><li class=""><a href="?page=10">10</a></li> <li><a href="?page=14">...</a></li><li><a href="?page=14">14</a></li> <li><a aria-label="Next" href="?page=8"><span aria-hidden="true">&raquo;</span></a></li> </ul> </nav>
            eot;

        $this->assertSame(
            $data,
            $page->render('bootstrap')
        );

        $data = <<<'eot'
            <nav aria-label="..."> <ul class="pager"> <li class=""><a aria-label="Previous" href="?page=6"><span aria-hidden="true">上一页</span></a></li> <li class=""><a aria-label="Next" href="?page=8"><span aria-hidden="true">下一页</span></a></li> </ul> </nav>
            eot;

        $this->assertSame(
            $data,
            $page->render('bootstrapSimple')
        );
    }

    /**
     * @api(
     *     zh-CN:title="MACRO 无限数据分页",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testMacro(): void
    {
        $page = new Page(1, 3, Page::MACRO);
        $page->currentPage(44);

        $data = <<<'eot'
            <div class="pagination">  <button class="btn-prev" onclick="window.location.href='?page=43';">&#8249;</button> <ul class="pager"> <li class=""><a href="?page=1" >1</a></li><li onclick="window.location.href='?page=39';" class="btn-quickprev" onmouseenter="this.innerHTML='&laquo;';" onmouseleave="this.innerHTML='...';">...</li> <li class="number"><a href="?page=42">42</a></li><li class="number"><a href="?page=43">43</a></li><li class="number active"><a>44</a></li><li class="number"><a href="?page=45">45</a></li><li class="number"><a href="?page=46">46</a></li> <li class="btn-quicknext" onclick="window.location.href='?page=49';" onmouseenter="this.innerHTML='&raquo;';" onmouseleave="this.innerHTML='...';">...</li> </ul> <button class="btn-next" onclick="window.location.href='?page=45';">&#8250;</button> <span class="pagination-jump">前往<input type="number" link="?page={jump}" onkeydown="var event = event || window.event; if (event.keyCode == 13) { window.location.href = this.getAttribute('link').replace( '{jump}', this.value); }" onfocus="this.select();" min="1" value="1" number="true" class="pagination-editor">页</span> </div>
            eot;

        $this->assertSame(
            $data,
            $page->render()
        );

        $data = <<<'eot'
            <nav aria-label="navigation"> <ul class="pagination"> <li><a aria-label="Previous" href="?page=43"><span aria-hidden="true">&laquo;</span></a></li> <li class=""><a href="?page=1" >1</a></li><li><a href="?page=39">...</a></li> <li class=""><a href="?page=42">42</a></li><li class=""><a href="?page=43">43</a></li><li class=" active"><a>44</a></li><li class=""><a href="?page=45">45</a></li><li class=""><a href="?page=46">46</a></li> <li><a href="?page=49">...</a></li> <li><a aria-label="Next" href="?page=45"><span aria-hidden="true">&raquo;</span></a></li> </ul> </nav>
            eot;

        $this->assertSame(
            $data,
            $page->render('bootstrap')
        );

        $data = <<<'eot'
            <nav aria-label="..."> <ul class="pager"> <li class=""><a aria-label="Previous" href="?page=43"><span aria-hidden="true">上一页</span></a></li> <li class=""><a aria-label="Next" href="?page=45"><span aria-hidden="true">下一页</span></a></li> </ul> </nav>
            eot;

        $this->assertSame(
            $data,
            $page->render('bootstrapSimple')
        );
    }

    public function testUseParamWithPageWillBeRemoved(): void
    {
        $page = new Page(1, 3, null);
        $page->addParam('page', 5);

        $data = <<<'eot'
            <div class="pagination">  <button class="btn-prev disabled">&#8249;</button> <ul class="pager">    </ul> <button class="btn-next" onclick="window.location.href='?page=2';">&#8250;</button> <span class="pagination-jump">前往<input type="number" link="?page={jump}" onkeydown="var event = event || window.event; if (event.keyCode == 13) { window.location.href = this.getAttribute('link').replace( '{jump}', this.value); }" onfocus="this.select();" min="1" value="1" number="true" class="pagination-editor">页</span> </div>
            eot;

        $this->assertSame(
            $data,
            $page->render()
        );

        $data = <<<'eot'
            {
                "per_page": 3,
                "current_page": 1,
                "total_page": null,
                "total_record": null,
                "total_macro": false,
                "from": 0,
                "to": null
            }
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $page->toArray()
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="bootstrap 分页尺寸设置",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testPageBootstrapSize(): void
    {
        $page = new Page(1, 3, 40);
        $page->currentPage(8);

        $data = <<<'eot'
            <nav aria-label="navigation"> <ul class="pagination pagination-lg"> <li><a aria-label="Previous" href="?page=7"><span aria-hidden="true">&laquo;</span></a></li> <li class=""><a href="?page=1" >1</a></li><li><a href="?page=3">...</a></li> <li class=""><a href="?page=6">6</a></li><li class=""><a href="?page=7">7</a></li><li class=" active"><a>8</a></li><li class=""><a href="?page=9">9</a></li><li class=""><a href="?page=10">10</a></li> <li><a href="?page=13">...</a></li><li><a href="?page=14">14</a></li> <li><a aria-label="Next" href="?page=9"><span aria-hidden="true">&raquo;</span></a></li> </ul> </nav>
            eot;

        $this->assertSame(
            $data,
            $page->render('bootstrap', ['large_size' => true])
        );

        $data = <<<'eot'
            <nav aria-label="navigation"> <ul class="pagination pagination-sm"> <li><a aria-label="Previous" href="?page=7"><span aria-hidden="true">&laquo;</span></a></li> <li class=""><a href="?page=1" >1</a></li><li><a href="?page=3">...</a></li> <li class=""><a href="?page=6">6</a></li><li class=""><a href="?page=7">7</a></li><li class=" active"><a>8</a></li><li class=""><a href="?page=9">9</a></li><li class=""><a href="?page=10">10</a></li> <li><a href="?page=13">...</a></li><li><a href="?page=14">14</a></li> <li><a aria-label="Next" href="?page=9"><span aria-hidden="true">&raquo;</span></a></li> </ul> </nav>
            eot;

        $this->assertSame(
            $data,
            $page->render('bootstrap', ['small_size' => true])
        );
    }

    public function testRenderObject(): void
    {
        $page = new Page(1, 10, 25);
        $render = new Render($page, [
            'small_template' => true,
        ]);

        $data = <<<'eot'
            <div class="pagination"> <button class="btn-prev disabled">&#8249;</button> <ul class="pager">  <li class="number active"><a>1</a></li><li class="number"><a href="?page=2">2</a></li><li class="number"><a href="?page=3">3</a></li>  </ul> <button class="btn-next" onclick="window.location.href='?page=2';">&#8250;</button> </div>
            eot;

        $this->assertSame(
            $data,
            $page->render($render)
        );

        $render = new Bootstrap($page, [
            'large_size' => true,
        ]);

        $data = <<<'eot'
            <nav aria-label="navigation"> <ul class="pagination pagination-lg"> <li class="disabled"><a aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>  <li class=" active"><a>1</a></li><li class=""><a href="?page=2">2</a></li><li class=""><a href="?page=3">3</a></li>  <li><a aria-label="Next" href="?page=2"><span aria-hidden="true">&raquo;</span></a></li> </ul> </nav>
            eot;

        $this->assertSame(
            $data,
            $page->render($render)
        );
    }

    public function testUnsupportedRenderType(): void
    {
        $this->expectException(\TypeError::class);

        $page = new Page(1, 10, 25);
        $page->render([1, 2]);
    }
}
