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
 *
 * @api(
 *     title="变量赋值",
 *     path="template/assign",
 *     description="可以在模板中进行一些变量的赋值，以便于进行后续计算处理。",
 * )
 */
class CompilerAssignTest extends TestCase
{
    use Compiler;

    /**
     * @api(
     *     title="node 版本",
     *     description="assign 标签也是用于页面快捷赋值，这个还是用起来比较方便。",
     *     note="",
     * )
     */
    public function testBaseUse()
    {
        $parser = $this->createParser();

        $source = <<<'eot'
        <assign name="helloWorld" value="say hello to the world" />
        <assign name="test.hello" value="hello" />
        eot;

        $compiled = <<<'eot'
        <?php $helloWorld = 'say hello to the world'; ?>
        <?php $test->hello = 'hello'; ?>
        eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     title="node 版本.初始化为 Null 值",
     *     description="",
     *     note="",
     * )
     */
    public function testNode()
    {
        $parser = $this->createParser();

        $source = <<<'eot'
        <assign name="test.hello" />
        eot;

        $compiled = <<<'eot'
        <?php $test->hello = null; ?>
        eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     title="node 版本.初始化为指定变量",
     *     description="",
     *     note="",
     * )
     */
    public function testNode2()
    {
        $parser = $this->createParser();

        $source = <<<'eot'
        <assign name="test.hello" value="$hello" />
        eot;

        $compiled = <<<'eot'
        <?php $test->hello = $hello; ?>
        eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     title="node 版本.初始化为函数格式化变量",
     *     description="",
     *     note="",
     * )
     */
    public function testNode3()
    {
        $parser = $this->createParser();

        $source = <<<'eot'
        <assign name="test.hello" value="$hello|md5" />
        eot;

        $compiled = <<<'eot'
        <?php $test->hello = md5($hello); ?>
        eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     title="node 版本.初始化为函数格式化占位变量",
     *     description="",
     *     note="",
     * )
     */
    public function testNode4()
    {
        $parser = $this->createParser();

        $source = <<<'eot'
        <assign name="test.hello" value="$hello|test=0,1|foo=**" />
        eot;

        $compiled = <<<'eot'
        <?php $test->hello = foo(test($hello, 0,1)); ?>
        eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     title="JS 风格版本",
     *     description="",
     *     note="",
     * )
     */
    public function testLet()
    {
        $parser = $this->createParser();

        $source = <<<'eot'
        {% let foo = 'foo' %}
        {% let hello = hello . 'foo' %}
        eot;

        $compiled = <<<'eot'
        <?php $foo = 'foo'; ?>
        <?php $hello = $hello . 'foo'; ?>
        eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     title="JS 风格版本.初始化值",
     *     description="",
     *     note="",
     * )
     */
    public function testLet2()
    {
        $parser = $this->createParser();

        $source = <<<'eot'
        {% let foo 'foo' %}
        eot;

        $compiled = <<<'eot'
        <?php $foo = 'foo'; ?>
        eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     title="JS 风格版本.初始化为 Null 值",
     *     description="",
     *     note="",
     * )
     */
    public function testLet3()
    {
        $parser = $this->createParser();

        $source = <<<'eot'
        {% let foo %}
        eot;

        $compiled = <<<'eot'
        <?php $foo = null; ?>
        eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     title="JS 风格版本.初始化为 Null 值带上等于符",
     *     description="",
     *     note="",
     * )
     */
    public function testLet4()
    {
        $parser = $this->createParser();

        $source = <<<'eot'
        {% let foo = %}
        eot;

        $compiled = <<<'eot'
        <?php $foo = null; ?>
        eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }
}
