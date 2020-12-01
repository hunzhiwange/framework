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
 *     zh-CN:title="Foreach 循环",
 *     path="template/foreach",
 *     zh-CN:description="foreach 标签也是用于循环输出，解析后的本质为 foreach。",
 * )
 */
class CompilerForeachTest extends TestCase
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
            {% foreach for=list value=my_value key=my_key index=my_index %}
                {{ $my_index }} {{ $my_key }} {{ $my_value }}
            {% :foreach %}
            eot;

        $compiled = <<<'eot'
            <?php $my_index = 1; ?>
            <?php if (is_array($list)): foreach ($list as $my_key => $my_value): ?>
                <?php echo $my_index; ?> <?php echo $my_key; ?> <?php echo $my_value; ?>
            <?php $my_index++; ?>
            <?php endforeach; endif; ?>
            eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     zh-CN:title="node 省略键值",
     *     zh-CN:description="有时候我们不需要键值，这个时候我们在模板中写下如下的代码：",
     *     zh-CN:note="",
     * )
     */
    public function testNodeSimple(): void
    {
        $parser = $this->createParser();
        $source = <<<'eot'
            {% foreach for=list %}
                {{ $index }} {{ $key }} {{ $value }}
            {% :foreach %}
            eot;

        $compiled = <<<'eot'
            <?php $index = 1; ?>
            <?php if (is_array($list)): foreach ($list as $key => $value): ?>
                <?php echo $index; ?> <?php echo $key; ?> <?php echo $value; ?>
            <?php $index++; ?>
            <?php endforeach; endif; ?>
            eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }
}
