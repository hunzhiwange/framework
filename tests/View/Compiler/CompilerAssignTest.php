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

namespace Tests\View\Compiler;

use Tests\TestCase;

/**
 * compiler assign test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.07
 *
 * @version 1.0
 */
class CompilerAssignTest extends TestCase
{
    use Compiler;

    public function testBaseUse()
    {
        $parser = $this->createParser();

        $source = <<<'eot'
<assign name="helloWorld" value="say hello to the world" />
<assign name="test.hello" value="hello" />
eot;

        $compiled = <<<'eot'
<?php $helloWorld='say hello to the world';?>
<?php $test->hello='hello';?>
eot;
        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    public function testNode()
    {
        $parser = $this->createParser();

        $source = <<<'eot'
<assign name="test.hello" />
eot;

        $compiled = <<<'eot'
<?php $test->hello=null;?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    public function testNode2()
    {
        $parser = $this->createParser();

        $source = <<<'eot'
<assign name="test.hello" value="$hello" />
eot;

        $compiled = <<<'eot'
<?php $test->hello=$hello;?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    public function testNode3()
    {
        $parser = $this->createParser();

        $source = <<<'eot'
<assign name="test.hello" value="$hello|md5" />
eot;

        $compiled = <<<'eot'
<?php $test->hello=md5($hello);?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    public function testNode4()
    {
        $parser = $this->createParser();

        $source = <<<'eot'
<assign name="test.hello" value="$hello|test=0,1|foo=**" />
eot;

        $compiled = <<<'eot'
<?php $test->hello=foo(test($hello, 0,1));?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    public function testLet()
    {
        $parser = $this->createParser();

        $source = <<<'eot'
{% let foo = 'foo' %}
{% let hello = hello . 'foo' %}
eot;

        $compiled = <<<'eot'
<?php $foo = 'foo';?>
<?php $hello = $hello . 'foo';?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    public function testLet2()
    {
        $parser = $this->createParser();

        $source = <<<'eot'
{% let foo 'foo' %}
eot;

        $compiled = <<<'eot'
<?php $foo = 'foo';?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    public function testLet3()
    {
        $parser = $this->createParser();

        $source = <<<'eot'
{% let foo %}
eot;

        $compiled = <<<'eot'
<?php $foo = null;?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    public function testLet4()
    {
        $parser = $this->createParser();

        $source = <<<'eot'
{% let foo = %}
eot;

        $compiled = <<<'eot'
<?php $foo = null;?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }
}
