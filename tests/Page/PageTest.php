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

namespace Tests\Page;

use Leevel\Page\Bootstrap;
use Leevel\Page\Defaults;
use Leevel\Page\IPage;
use Leevel\Page\Page;
use Leevel\Router\IUrl;
use Tests\TestCase;

/**
 * page test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.13
 *
 * @version 1.0
 */
class PageTest extends TestCase
{
    public function testBaseUse()
    {
        $page = new Page(10, 52);

        $this->assertInstanceof(IPage::class, $page);

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
array (
  'per_page' => 10,
  'current_page' => 1,
  'total_page' => 6,
  'total_record' => 52,
  'total_macro' => false,
  'from' => 1,
  'to' => 52,
)
eot;

        $this->assertSame(
            $data,
                $this->varExport(
                    $page->toArray()
                )
        );

        $this->assertSame(
            $data,
                $this->varExport(
                    $page->jsonSerialize()
                )
        );

        $data = <<<'eot'
{"per_page":10,"current_page":1,"total_page":6,"total_record":52,"total_macro":false,"from":1,"to":52}
eot;

        $this->assertSame(
            $data,
            $page->toJson()
        );
    }

    public function testFragment()
    {
        $page = new Page(10, 52);

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

    public function testPerPage()
    {
        $page = new Page(10, 52);

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

    public function testSetSmallTemplate()
    {
        $page = new Page(10, 52, [
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

    public function testAppend()
    {
        $page = new Page(5, 3);

        $page->append('foo', 'bar');

        $page->addParameter('foo1', 'bar1');

        $page->appends(['hello' => 'world']);

        $data = <<<'eot'
<div class="pagination"> <span class="pagination-total">共 3 条</span> <button class="btn-prev disabled">&#8249;</button> <ul class="pager">    </ul> <button class="btn-next disabled">&#8250;</button> <span class="pagination-jump">前往<input type="number" link="?foo=bar&foo1=bar1&hello=world&page={jump}" onkeydown="var event = event || window.event; if (event.keyCode == 13) { window.location.href = this.getAttribute('link').replace( '{jump}', this.value); }" onfocus="this.select();" min="1" value="1" number="true" class="pagination-editor">页</span> </div>
eot;

        $this->assertSame(
            $data,
            $page->render()
        );

        $page->parameter(['hello' => 'world']);

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

    public function testRenderOption()
    {
        $page = new Page(5, 3);

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

    public function testUrl()
    {
        $page = new Page(3, 5);

        $page->url('/hello');

        $data = <<<'eot'
<div class="pagination"> <span class="pagination-total">共 5 条</span> <button class="btn-prev disabled">&#8249;</button> <ul class="pager">  <li class="number active"><a>1</a></li><li class="number"><a href="/hello?page=2">2</a></li>  </ul> <button class="btn-next" onclick="window.location.href='/hello?page=2';">&#8250;</button> <span class="pagination-jump">前往<input type="number" link="/hello?page={jump}" onkeydown="var event = event || window.event; if (event.keyCode == 13) { window.location.href = this.getAttribute('link').replace( '{jump}', this.value); }" onfocus="this.select();" min="1" value="1" number="true" class="pagination-editor">页</span> </div>
eot;

        $this->assertSame(
            $data,
            $page->render()
        );
    }

    public function testSetRender()
    {
        $page = new Page(3, 5);

        $page->setRender('bootstrap');

        $data = <<<'eot'
<nav aria-label="navigation"> <ul class="pagination"> <li class="disabled"><a aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>  <li class=" active"><a>1</a></li><li class=""><a href="?page=2">2</a></li>  <li><a aria-label="Next" href="?page=2"><span aria-hidden="true">&raquo;</span></a></li> </ul> </nav>
eot;

        $this->assertSame(
            $data,
            $page->render()
        );
    }

    public function testDefaultPerPage()
    {
        $page = new Page(null, 25);

        $data = <<<'eot'
<div class="pagination"> <span class="pagination-total">共 25 条</span> <button class="btn-prev disabled">&#8249;</button> <ul class="pager">  <li class="number active"><a>1</a></li><li class="number"><a href="?page=2">2</a></li>  </ul> <button class="btn-next" onclick="window.location.href='?page=2';">&#8250;</button> <span class="pagination-jump">前往<input type="number" link="?page={jump}" onkeydown="var event = event || window.event; if (event.keyCode == 13) { window.location.href = this.getAttribute('link').replace( '{jump}', this.value); }" onfocus="this.select();" min="1" value="1" number="true" class="pagination-editor">页</span> </div>
eot;

        $this->assertSame(
            $data,
            $page->render()
        );
    }

    public function testPageName()
    {
        $page = new Page(10, 25);

        $page->pageName('page2');

        $data = <<<'eot'
<div class="pagination"> <span class="pagination-total">共 25 条</span> <button class="btn-prev disabled">&#8249;</button> <ul class="pager">  <li class="number active"><a>1</a></li><li class="number"><a href="?page2=2">2</a></li><li class="number"><a href="?page2=3">3</a></li>  </ul> <button class="btn-next" onclick="window.location.href='?page2=2';">&#8250;</button> <span class="pagination-jump">前往<input type="number" link="?page2={jump}" onkeydown="var event = event || window.event; if (event.keyCode == 13) { window.location.href = this.getAttribute('link').replace( '{jump}', this.value); }" onfocus="this.select();" min="1" value="1" number="true" class="pagination-editor">页</span> </div>
eot;

        $this->assertSame(
            $data,
            $page->render()
        );
    }

    public function testParseLastRenderNext()
    {
        $page = new Page(3, 30);

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

    public function testWithResolver()
    {
        $page = new Page(10, 25);

        Page::setUrlResolver(function () {
            $args = func_get_args();

            $url = $this->createMock(IUrl::class);

            $this->assertInstanceof(IUrl::class, $url);

            $resultUrl = $args[2].$args[0];

            $url->method('make')->willReturn($resultUrl);
            $this->assertEquals($resultUrl, $url->make($args[0]));

            return call_user_func_array(
                [$url, 'make'], $args
            );
        });

        $page->url('domain@/hello/world');

        $data = <<<'eot'
<div class="pagination"> <span class="pagination-total">共 25 条</span> <button class="btn-prev disabled">&#8249;</button> <ul class="pager">  <li class="number active"><a>1</a></li><li class="number"><a href="domain/hello/world">2</a></li><li class="number"><a href="domain/hello/world">3</a></li>  </ul> <button class="btn-next" onclick="window.location.href='domain/hello/world';">&#8250;</button> <span class="pagination-jump">前往<input type="number" link="domain/hello/world" onkeydown="var event = event || window.event; if (event.keyCode == 13) { window.location.href = this.getAttribute('link').replace( '{jump}', this.value); }" onfocus="this.select();" min="1" value="1" number="true" class="pagination-editor">页</span> </div>
eot;

        $this->assertSame(
            $data,
            $page->render()
        );

        $page->url('@/hello/world');

        $data = <<<'eot'
<div class="pagination"> <span class="pagination-total">共 25 条</span> <button class="btn-prev disabled">&#8249;</button> <ul class="pager">  <li class="number active"><a>1</a></li><li class="number"><a href="www/hello/world">2</a></li><li class="number"><a href="www/hello/world">3</a></li>  </ul> <button class="btn-next" onclick="window.location.href='www/hello/world';">&#8250;</button> <span class="pagination-jump">前往<input type="number" link="www/hello/world" onkeydown="var event = event || window.event; if (event.keyCode == 13) { window.location.href = this.getAttribute('link').replace( '{jump}', this.value); }" onfocus="this.select();" min="1" value="1" number="true" class="pagination-editor">页</span> </div>
eot;

        $this->assertSame(
            $data,
            $page->render()
        );

        Page::setUrlResolver(null);
    }

    public function testWithResolverException()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Page not set url resolver.'
        );

        $page = new Page(10, 25);

        $page->url('domain@/hello/world');

        $page->render();
    }

    public function testRange()
    {
        $page = new Page(3, 40);

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

    public function testMacro()
    {
        $page = new Page(3, Page::MACRO);

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

    public function testUseParameterAsPage()
    {
        $page = new Page(3, null);

        $page->addParameter('page', 5);

        $data = <<<'eot'
<div class="pagination">  <button class="btn-prev" onclick="window.location.href='?page=4';">&#8249;</button> <ul class="pager">    </ul> <button class="btn-next" onclick="window.location.href='?page=6';">&#8250;</button> <span class="pagination-jump">前往<input type="number" link="?page={jump}" onkeydown="var event = event || window.event; if (event.keyCode == 13) { window.location.href = this.getAttribute('link').replace( '{jump}', this.value); }" onfocus="this.select();" min="1" value="1" number="true" class="pagination-editor">页</span> </div>
eot;

        $this->assertSame(
            $data,
            $page->render()
        );

        $data = <<<'eot'
array (
  'per_page' => 3,
  'current_page' => 5,
  'total_page' => NULL,
  'total_record' => NULL,
  'total_macro' => false,
  'from' => NULL,
  'to' => NULL,
)
eot;

        $this->assertSame(
            $data,
            $this->varExport(
                $page->toArray()
            )
        );
    }

    public function testPageBootstrapSize()
    {
        $page = new Page(3, 40);

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

    public function testRenderObject()
    {
        $page = new Page(10, 25);

        $render = new Defaults($page, [
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

    public function testUnsupportedRenderType()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Unsupported render type.'
        );

        $page = new Page(10, 25);

        $page->render([1, 2]);
    }
}
