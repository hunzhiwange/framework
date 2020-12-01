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
 *     zh-CN:title="While 循环",
 *     path="template/while",
 *     zh-CN:description="QueryPHP 支持 while 语法标签，通过这种方式可以很好地将 PHP 的 while 语法布局出来。",
 * )
 */
class CompilerWhileTest extends TestCase
{
    use Compiler;

    /**
     * @api(
     *     zh-CN:title="node",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testNode(): void
    {
        $parser = $this->createParser();

        $source = <<<'eot'
            {{ ~$i = 10 }}
            {% while cond="$i > 0" %}
                {{ $i }}Hello QueryPHP !<br>
                {{~ $i-- }}
            {% :while %}
            eot;

        $compiled = <<<'eot'
            <?php $i = 10; ?>
            <?php while($i > 0): ?>
                <?php echo $i; ?>Hello QueryPHP !<br>
                <?php $i--; ?>
            <?php endwhile; ?>
            eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     zh-CN:title="cond 可省略",
     *     zh-CN:description="默认第一个条件会自动解析为 cond。",
     *     zh-CN:note="",
     * )
     */
    public function testNodeSimple(): void
    {
        $parser = $this->createParser();

        $source = <<<'eot'
            {{ ~$i = 10 }}
            {% while "$i > 0" %}
                {{ $i }}Hello QueryPHP !<br>
                {{~ $i-- }}
            {% :while %}
            eot;

        $compiled = <<<'eot'
            <?php $i = 10; ?>
            <?php while($i > 0): ?>
                <?php echo $i; ?>Hello QueryPHP !<br>
                <?php $i--; ?>
            <?php endwhile; ?>
            eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }
}
