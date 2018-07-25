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

namespace Tests\View;

use Leevel\View\V8;
use Tests\TestCase;
use V8Js;

/**
 * v8 test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.24
 *
 * @version 1.0
 */
class V8Test extends TestCase
{
    protected function setUp()
    {
        if (!extension_loaded('v8js')) {
            $this->markTestSkipped('Please install php v8js extension.');
        }
    }

    public function testV8jsSelf()
    {
        $v8 = new V8js();

        ob_start();
        $v8->executeString('print("hello v8js!")');
        $content = ob_get_clean();

        $this->assertSame('hello v8js!', $content);
    }

    public function testBaseUse()
    {
        $v8 = new V8();

        $content = $v8->display(__DIR__.'/assert/demo1.js', [], null, false);

        $this->assertSame('hello v8 demo1.js!', $content);

        $content = $v8->display(__DIR__.'/assert/demo1.js', [], null, true);

        $this->assertNull($content);

        $this->assertInstanceof(V8js::class, $v8->getV8js());
    }

    public function testDiplayWithVar()
    {
        $v8 = new V8();

        $content = $v8->display(__DIR__.'/assert/demo3.js', ['hello' => 'world', 'foo' => 'bar'], null, false);

        $this->assertSame('hello v8 demo3.js! foo = bar;hello = world', $content);
    }

    public function testDump()
    {
        $v8 = new V8();

        $content = $v8->display(__DIR__.'/assert/demo2.js', [], null, false);

        $this->assertContains('string(18) "hello v8 demo2.js!"', $content);
    }

    public function testSelect()
    {
        $v8 = new V8();

        $content = $v8->select('print("hello v8js for select!")');

        $this->assertContains('hello v8js for select!', $content);
    }

    public function testExecute()
    {
        $v8 = new V8();

        $content = $v8->execute('print("hello v8js for execute!")');

        $this->assertSame(23, $content);
    }

    public function testArt()
    {
        $v8 = new V8(['theme_path' => __DIR__.'/assert/default']);

        $listVar = [
            '摄影',
            '电影',
            '民谣',
            '旅行',
            '吉他',
        ];

        $content = $v8->display(__DIR__.'/assert/art.js', ['list' => $listVar], null, false);

        $data = <<<'EOT'
<ul>
    
        <li>索引 1 ：摄影</li>
    
        <li>索引 2 ：电影</li>
    
        <li>索引 3 ：民谣</li>
    
        <li>索引 4 ：旅行</li>
    
        <li>索引 5 ：吉他</li>
    
</ul>
EOT;
        $this->assertSame($data, $content);
    }

    public function testVue()
    {
        $v8 = new V8(['theme_path' => __DIR__.'/assert/default']);

        $content = $v8->display(__DIR__.'/assert/vue.js', ['msg' => 'hello v8js for vue'], null, false);

        $data = <<<'EOT'
<div data-server-rendered="true">
    hello v8js for vue
</div>
EOT;
        $this->assertSame($data, $content);
    }

    public function testRequires()
    {
        $v8 = new V8(['theme_path' => __DIR__.'/assert/default']);

        $content = $v8->display(__DIR__.'/assert/requires.js', [], null, false);

        $data = <<<'EOT'
i am requireshello.js is found.[object Object]dir index.js was found.[object Object]
EOT;
        $this->assertSame($data, $content);
    }
}
