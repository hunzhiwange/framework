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

use Leevel\Page\IPage;
use Leevel\Page\Page;
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
    }

    public function testUrl()
    {
        $page = new Page(3, 5);

        $page->url('/hello');
        dd($page);
        $data = <<<'eot'
<div class="pagination"> <span class="pagination-total">共 3 条</span> <button class="btn-prev disabled">&#8249;</button> <ul class="pager">    </ul> <button class="btn-next disabled">&#8250;</button> <span class="pagination-jump">前往<input type="number" link="?page={jump}" onkeydown="var event = event || window.event; if (event.keyCode == 13) { window.location.href = this.getAttribute('link').replace( '{jump}', this.value); }" onfocus="this.select();" min="1" value="1" number="true" class="pagination-editor">页</span> </div>
eot;

        $this->assertSame(
            $data,
            $page->render()
        );
    }
}
