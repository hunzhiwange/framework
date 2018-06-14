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
 * compiler if test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.07
 *
 * @version 1.0
 * @coversNothing
 */
class CompilerIfTest extends TestCase
{
    use Compiler;

    public function testBaseUse()
    {
        $parser = $this->createParser();

        $source = <<<'eot'
{if $id==1}
    我的值为1，我为if下的内容。
{elseif $id==2}
    我的值为2，我为elseif下的内容。
{else}
    我的值为{$id}，我不是谁的谁！
{/if}
eot;

        $compiled = <<<'eot'
<?php if ($id==1):?>
    我的值为1，我为if下的内容。
<?php elseif ($id==2):?>
    我的值为2，我为elseif下的内容。
<?php else:?>
    我的值为<?php echo $id;?>，我不是谁的谁！
<?php endif;?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        $source = <<<'eot'
{if $a->name == 1}
    a
{/if}

{if hello::run() == 1}
    b
{/if}
eot;

        $compiled = <<<'eot'
<?php if ($a->name == 1):?>
    a
<?php endif;?>

<?php if (hello::run() == 1):?>
    b
<?php endif;?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        $source = <<<'eot'
<if condition="($id eq 1) OR ($id gt 100)">one
    <elseif condition="$id eq 2" />two?
    <else />other?
</if>
eot;

        $compiled = <<<'eot'
<?php if (($id == 1) OR ($id > 100)):?>one
    <?php elseif ($id == 2):?>two?
    <?php else:?>other?
<?php endif;?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        $source = <<<'eot'
<if condition="$a.name == 1">
    one
</if>

<if condition="hello::run() == 1">
    two
</if>
eot;

        $compiled = <<<'eot'
<?php if ($a->name == 1):?>
    one
<?php endif;?>

<?php if (hello::run() == 1):?>
    two
<?php endif;?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        $source = <<<'eot'
{% if length(users) > 0 %}
a
{% elseif foo.bar > 0 %}
b
{% else %}
c
{% /if %}
eot;

        $compiled = <<<'eot'
<?php if (length($users) > 0):?>
a
<?php elseif ($foo->bar > 0):?>
b
<?php else:?>
c
<?php endif;?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }
}
