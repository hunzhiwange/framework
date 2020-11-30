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

namespace Tests\View\Compiler;

use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="For 循环",
 *     path="template/for",
 *     zh-CN:description="如果我们需要在模板中使用 for 循环，那么通过 for 标签可以很方便地输出。",
 * )
 */
class CompilerForTest extends TestCase
{
    use Compiler;

    /**
     * @api(
     *     zh-CN:title="code",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
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
     *     zh-CN:title="node 简单版",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testForNode(): void
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
     *     zh-CN:title="node 完整版",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testForNode2(): void
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

    public function testForType(): void
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
