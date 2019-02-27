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
 * compiler for test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.07
 *
 * @version 1.0
 *
 * @api(
 *     title="For 循环",
 *     path="template/for",
 *     description="如果我们需要在模板中使用 for 循环，那么通过 for 标签可以很方便地输出。",
 * )
 */
class CompilerForTest extends TestCase
{
    use Compiler;

    /**
     * @api(
     *     title="code",
     *     description="我们在模板中写下如下的代码和模板编译后的结果。",
     *     note="",
     * )
     */
    public function testBaseUse()
    {
        $parser = $this->createParser();

        $source = <<<'eot'
{for $i=1;$i<10;$i++}
    QueryPHP - 代码版本for <br>
{/for}
eot;

        $compiled = <<<'eot'
<?php for ($i=1;$i<10;$i++): ?>
    QueryPHP - 代码版本for <br>
<?php endfor; ?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     title="node 简单版",
     *     description="我们在模板中写下如下的代码和模板编译后的结果。",
     *     note="",
     * )
     */
    public function testForNode()
    {
        $parser = $this->createParser();

        $source = <<<'eot'
<for start='1'>
    QueryPHP - node - for <br>
</for>
eot;

        $compiled = <<<'eot'
<?php for ($var = 1; $var <= 0; $var += 1): ?>
    QueryPHP - node - for <br>
<?php endfor; ?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     title="node 完整版",
     *     description="我们在模板中写下如下的代码和模板编译后的结果。",
     *     note="",
     * )
     */
    public function testForNode2()
    {
        $parser = $this->createParser();
        $source = <<<'eot'
<for start='1' end='10' var='myValue' step='3'>
    QueryPHP for <br>
</for>
eot;

        $compiled = <<<'eot'
<?php for ($myValue = 1; $myValue <= 10; $myValue += 3): ?>
    QueryPHP for <br>
<?php endfor; ?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     title="JS 风格版: 例 1",
     *     description="最终生成一个 foreach 结果，简单的循环。",
     *     note="",
     * )
     */
    public function testForJsStyle()
    {
        $parser = $this->createParser();

        $source = <<<'eot'
{% for item in navigation %}
    <li><a href="{{ item.href }}">{{ item.caption }}</a></li>
{% /for %}
eot;

        $compiled = <<<'eot'
<?php foreach ($navigation as $key => $item): ?>
    <li><a href="<?php echo $item->href; ?>"><?php echo $item->caption; ?></a></li>
<?php endforeach; ?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     title="JS 风格版: 例 2",
     *     description="可以使用逗号分割建和值，逗号连接不能有空格。",
     *     note="",
     * )
     */
    public function testForJsStyle2()
    {
        $parser = $this->createParser();

        $source = <<<'eot'
{% for mykey,item in navigation %}
    <li><a href="{{ item.href }}">{{ item.caption }}</a></li>
{% /for %}
eot;

        $compiled = <<<'eot'
<?php foreach ($navigation as $mykey => $item): ?>
    <li><a href="<?php echo $item->href; ?>"><?php echo $item->caption; ?></a></li>
<?php endforeach; ?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     title="JS 风格版: 例 3",
     *     description="可以使用空格分割建和值。",
     *     note="",
     * )
     */
    public function testForJsStyle3()
    {
        $parser = $this->createParser();

        $source = <<<'eot'
{% for mykey item in navigation %}
    <li><a href="{{ item.href }}">{{ item.caption }}</a></li>
{% /for %}
eot;

        $compiled = <<<'eot'
<?php foreach ($navigation as $mykey => $item): ?>
    <li><a href="<?php echo $item->href; ?>"><?php echo $item->caption; ?></a></li>
<?php endforeach; ?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    public function testForJsStyleException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The node for lacks the required property: condition3.'
        );

        $parser = $this->createParser();

        $source = <<<'eot'
{% for item navigation %}
{% /for %}
eot;

        $parser->doCompile($source, null, true);
    }

    public function testForJsStyleException2()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'For tag need “in“ separate.'
        );

        $parser = $this->createParser();

        $source = <<<'eot'
{% for key item navigation %}
{% /for %}
eot;

        $parser->doCompile($source, null, true);
    }

    public function testForType()
    {
        $parser = $this->createParser();

        $source = <<<'eot'
<for start='10' end='1' var='myValue' step='3' type='-'>
    QueryPHP for <br>
</for>
eot;

        $compiled = <<<'eot'
<?php for ($myValue = 10; $myValue >= 1; $myValue -= 3): ?>
    QueryPHP for <br>
<?php endfor; ?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }
}
