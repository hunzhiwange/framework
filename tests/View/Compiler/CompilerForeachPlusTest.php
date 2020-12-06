<?php

declare(strict_types=1);

namespace Tests\View\Compiler;

use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="Foreach 增强循环",
 *     path="template/foreachplus",
 *     zh-CN:description="foreach+ 标签主要用于在模板中循环输出数据集或者多维数组。",
 * )
 */
class CompilerForeachPlusTest extends TestCase
{
    use Compiler;

    /**
     * @api(
     *     zh-CN:title="普通输出",
     *     zh-CN:description="
     * foreach+ 标签的 `name` 属性表示模板赋值的变量名称，因此不可随意在模板文件中改变。
     * `id` 表示当前的循环变量，可以随意指定，但确保不要和 name 属性冲突。
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $parser = $this->createParser();

        $source = <<<'eot'
            {% foreach+ name="list" id="vo" %}
                {{ $vo->title }}  {{ $vo->people }}
            {% :foreach+ %}
            eot;

        $compiled = <<<'eot'
            <?php if (is_array($list)):
                $index = 0;
                $tmp = $list;
                if (0 === count($tmp)):
                    echo "";
                else:
                    foreach ($tmp as $key => $vo):
                        ++$index;
                        $mod = $index % 2; ?>
                <?php echo $vo->title; ?>  <?php echo $vo->people; ?>
                    <?php endforeach;
                endif;
            else:
                echo "";
            endif; ?>
            eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     zh-CN:title="部分输出指定开始位置和长度的记录",
     *     zh-CN:description="支持输出部分数据，例如输出其中的第 2～4 条记录。",
     *     zh-CN:note="",
     * )
     */
    public function testOffsetLength(): void
    {
        $parser = $this->createParser();

        $source = <<<'eot'
            {% foreach+ name="list" id="vo" offset="2" length='4' %}
                {{ $vo->title }} {{ $vo->people }}
            {% :foreach+ %}
            eot;

        $compiled = <<<'eot'
            <?php if (is_array($list)):
                $index = 0;
                $tmp = array_slice($list, 2, 4);
                if (0 === count($tmp)):
                    echo "";
                else:
                    foreach ($tmp as $key => $vo):
                        ++$index;
                        $mod = $index % 2; ?>
                <?php echo $vo->title; ?> <?php echo $vo->people; ?>
                    <?php endforeach;
                endif;
            else:
                echo "";
            endif; ?>
            eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     zh-CN:title="部分输出指定开始位置到结尾的所有记录",
     *     zh-CN:description="支持输出部分数据，例如输出指定开始位置到结尾的所有记录。",
     *     zh-CN:note="",
     * )
     */
    public function testOffset(): void
    {
        $parser = $this->createParser();

        $source = <<<'eot'
            {% foreach+ name="list" id="vo" offset="3" %}
                {{ $vo->title }}  {{ $vo->people }}
            {% :foreach+ %}
            eot;

        $compiled = <<<'eot'
            <?php if (is_array($list)):
                $index = 0;
                $tmp = array_slice($list, 3);
                if (0 === count($tmp)):
                    echo "";
                else:
                    foreach ($tmp as $key => $vo):
                        ++$index;
                        $mod = $index % 2; ?>
                <?php echo $vo->title; ?>  <?php echo $vo->people; ?>
                    <?php endforeach;
                endif;
            else:
                echo "";
            endif; ?>
            eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     zh-CN:title="输出偶数记录",
     *     zh-CN:description="foreach+ 还支持偶数记录的输出，基于 `mod` 属性来控制。",
     *     zh-CN:note="奇数记录和偶数记录规定如下，我们以数组的 0 为开始，0、2、4为偶记录，其它的都为基数记录。",
     * )
     */
    public function testMod(): void
    {
        $parser = $this->createParser();

        $source = <<<'eot'
            {% foreach+ name="list" id="vo" mod="2" %}
                <?php if ($mod == 1): ?>
                    {{ $vo->title }} {{ $vo->people }}
                <?php endif; ?>
            {% :foreach+ %}
            eot;

        $compiled = <<<'eot'
            <?php if (is_array($list)):
                $index = 0;
                $tmp = $list;
                if (0 === count($tmp)):
                    echo "";
                else:
                    foreach ($tmp as $key => $vo):
                        ++$index;
                        $mod = $index % 2; ?>
                <?php if ($mod == 1): ?>
                    <?php echo $vo->title; ?> <?php echo $vo->people; ?>
                <?php endif; ?>
                    <?php endforeach;
                endif;
            else:
                echo "";
            endif; ?>
            eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     zh-CN:title="输出奇数记录",
     *     zh-CN:description="foreach+ 还支持奇数记录的输出，基于 `mod` 属性来控制。",
     *     zh-CN:note="奇数记录和偶数记录规定如下，我们以数组索引的 0 为开始，0、2、4 为偶数记录，1、3、5 为基数记录。",
     * )
     */
    public function testMod2(): void
    {
        $parser = $this->createParser();

        $source = <<<'eot'
            {% foreach+ name="list" id="vo" mod="2" %}
                <?php if (0 === $mod): ?>
                    {{ $vo->title }} {{ $vo->people }}
                <?php endif; ?>
            {% :foreach+ %}
            eot;

        $compiled = <<<'eot'
            <?php if (is_array($list)):
                $index = 0;
                $tmp = $list;
                if (0 === count($tmp)):
                    echo "";
                else:
                    foreach ($tmp as $key => $vo):
                        ++$index;
                        $mod = $index % 2; ?>
                <?php if (0 === $mod): ?>
                    <?php echo $vo->title; ?> <?php echo $vo->people; ?>
                <?php endif; ?>
                    <?php endforeach;
                endif;
            else:
                echo "";
            endif; ?>
            eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     zh-CN:title="控制换行",
     *     zh-CN:description="mod 属性还用于控制一定记录的换行。",
     *     zh-CN:note="",
     * )
     */
    public function testMod3(): void
    {
        $parser = $this->createParser();

        $source = <<<'eot'
            {% foreach+ name="list" id="vo" mod="2" %}
                {{ $vo->title }} {{ $vo->people }}
                <?php if (0 === $mod): ?>
                    <br>
                <?php endif; ?>
            {% :foreach+ %}
            eot;

        $compiled = <<<'eot'
            <?php if (is_array($list)):
                $index = 0;
                $tmp = $list;
                if (0 === count($tmp)):
                    echo "";
                else:
                    foreach ($tmp as $key => $vo):
                        ++$index;
                        $mod = $index % 2; ?>
                <?php echo $vo->title; ?> <?php echo $vo->people; ?>
                <?php if (0 === $mod): ?>
                    <br>
                <?php endif; ?>
                    <?php endforeach;
                endif;
            else:
                echo "";
            endif; ?>
            eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     zh-CN:title="mod 支持变量",
     *     zh-CN:description="mod 属性支持变量。",
     *     zh-CN:note="",
     * )
     */
    public function testModCanBeVar(): void
    {
        $parser = $this->createParser();

        $source = <<<'eot'
            {{~ $mod = 4 }}
            
            {% foreach+ name="list" id="vo" mod="mod" %}
                {{ $vo->title }}  {{ $vo->people }}
            {% :foreach+ %}
            eot;

        $compiled = <<<'eot'
            <?php $mod = 4; ?>
            
            <?php if (is_array($list)):
                $index = 0;
                $tmp = $list;
                if (0 === count($tmp)):
                    echo "";
                else:
                    foreach ($tmp as $key => $vo):
                        ++$index;
                        $mod = $index % $mod; ?>
                <?php echo $vo->title; ?>  <?php echo $vo->people; ?>
                    <?php endforeach;
                endif;
            else:
                echo "";
            endif; ?>
            eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     zh-CN:title="输出循环索引",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testIndex(): void
    {
        $parser = $this->createParser();

        $source = <<<'eot'
            {% foreach+ name="list" id="vo" index="k" %}
                {{ $k }} {{ $vo->people }}
            {% :foreach+ %}
            eot;

        $compiled = <<<'eot'
            <?php if (is_array($list)):
                $k = 0;
                $tmp = $list;
                if (0 === count($tmp)):
                    echo "";
                else:
                    foreach ($tmp as $key => $vo):
                        ++$k;
                        $mod = $k % 2; ?>
                <?php echo $k; ?> <?php echo $vo->people; ?>
                    <?php endforeach;
                endif;
            else:
                echo "";
            endif; ?>
            eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     zh-CN:title="输出数组的键值",
     *     zh-CN:description="如果要输出数组的键值，可以直接使用 `key` 变量，和循环变量不同的是，这个 `key` 是由数据本身决定，而不是循环控制的，这个 `key` 可以通过 `key` 属性指定。",
     *     zh-CN:note="",
     * )
     */
    public function testKey(): void
    {
        $parser = $this->createParser();

        $source = <<<'eot'
            {% foreach+ name="list" id="vo" %}
                key: {{ $key }}
            {% :foreach+ %}
            eot;

        $compiled = <<<'eot'
            <?php if (is_array($list)):
                $index = 0;
                $tmp = $list;
                if (0 === count($tmp)):
                    echo "";
                else:
                    foreach ($tmp as $key => $vo):
                        ++$index;
                        $mod = $index % 2; ?>
                key: <?php echo $key; ?>
                    <?php endforeach;
                endif;
            else:
                echo "";
            endif; ?>
            eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }
}
