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
 * compiler lists test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.07
 *
 * @version 1.0
 * @coversNothing
 */
class CompilerListsTest extends TestCase
{
    use Compiler;

    public function testBaseUse()
    {
        $parser = $this->createParser();

        $source = <<<'eot'
<lists name="list" id="vo">
    {$vo.title}  {$vo.people}
</lists>
eot;

        $compiled = <<<'eot'
<?php if (is_array($list)):
    $index = 0;
    $tmp = $list;
    if (count($tmp) == 0):
        echo "";
    else:
        foreach ($tmp as $key => $vo):
            ++$index;
            $mod = $index % 2;?>
    <?php echo $vo->title;?>  <?php echo $vo->people;?>
        <?php endforeach;
    endif;
else:
    echo "";
endif;?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        $source = <<<'eot'
<lists name="list" id="vo" offset="2" length='4'>
    {$vo.title} {$vo.people}
</lists>
eot;

        $compiled = <<<'eot'
<?php if (is_array($list)):
    $index = 0;
    $tmp = array_slice($list, 2, 4);
    if (count($tmp) == 0):
        echo "";
    else:
        foreach ($tmp as $key => $vo):
            ++$index;
            $mod = $index % 2;?>
    <?php echo $vo->title;?> <?php echo $vo->people;?>
        <?php endforeach;
    endif;
else:
    echo "";
endif;?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        $source = <<<'eot'
<lists name="list" id="vo" mod="2">
    <?php if ($mod == 1):?>
        {$vo.title} {$vo.people}
    <?php endif;?>
</lists>
eot;

        $compiled = <<<'eot'
<?php if (is_array($list)):
    $index = 0;
    $tmp = $list;
    if (count($tmp) == 0):
        echo "";
    else:
        foreach ($tmp as $key => $vo):
            ++$index;
            $mod = $index % 2;?>
    <?php if ($mod == 1):?>
        <?php echo $vo->title;?> <?php echo $vo->people;?>
    <?php endif;?>
        <?php endforeach;
    endif;
else:
    echo "";
endif;?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        $source = <<<'eot'
<lists name="list" id="vo" mod="2">
    <?php if ($mod == 0):?>
        {$vo.title} {$vo.people}
    <?php endif;?>
</lists>
eot;

        $compiled = <<<'eot'
<?php if (is_array($list)):
    $index = 0;
    $tmp = $list;
    if (count($tmp) == 0):
        echo "";
    else:
        foreach ($tmp as $key => $vo):
            ++$index;
            $mod = $index % 2;?>
    <?php if ($mod == 0):?>
        <?php echo $vo->title;?> <?php echo $vo->people;?>
    <?php endif;?>
        <?php endforeach;
    endif;
else:
    echo "";
endif;?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        $source = <<<'eot'
<lists name="list" id="vo" mod="2">
    {$vo.title} {$vo.people}
    <?php if ($mod == 0):?>
        <br>
    <?php endif;?>
</lists>
eot;

        $compiled = <<<'eot'
<?php if (is_array($list)):
    $index = 0;
    $tmp = $list;
    if (count($tmp) == 0):
        echo "";
    else:
        foreach ($tmp as $key => $vo):
            ++$index;
            $mod = $index % 2;?>
    <?php echo $vo->title;?> <?php echo $vo->people;?>
    <?php if ($mod == 0):?>
        <br>
    <?php endif;?>
        <?php endforeach;
    endif;
else:
    echo "";
endif;?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        $source = <<<'eot'
<lists name="list" id="vo" mod="2">
    {$vo.title} {$vo.people}
    <?php if ($mod == 0):?>
        <br>
    <?php endif;?>
</lists>
eot;

        $compiled = <<<'eot'
<?php if (is_array($list)):
    $index = 0;
    $tmp = $list;
    if (count($tmp) == 0):
        echo "";
    else:
        foreach ($tmp as $key => $vo):
            ++$index;
            $mod = $index % 2;?>
    <?php echo $vo->title;?> <?php echo $vo->people;?>
    <?php if ($mod == 0):?>
        <br>
    <?php endif;?>
        <?php endforeach;
    endif;
else:
    echo "";
endif;?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        $source = <<<'eot'
<lists name="list" id="vo" index="k">
    {$k} {$vo.people}
</lists>
eot;

        $compiled = <<<'eot'
<?php if (is_array($list)):
    $k = 0;
    $tmp = $list;
    if (count($tmp) == 0):
        echo "";
    else:
        foreach ($tmp as $key => $vo):
            ++$k;
            $mod = $k % 2;?>
    <?php echo $k;?> <?php echo $vo->people;?>
        <?php endforeach;
    endif;
else:
    echo "";
endif;?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        $source = <<<'eot'
<lists name="list" id="vo">
    key: {$key}
</lists>
eot;

        $compiled = <<<'eot'
<?php if (is_array($list)):
    $index = 0;
    $tmp = $list;
    if (count($tmp) == 0):
        echo "";
    else:
        foreach ($tmp as $key => $vo):
            ++$index;
            $mod = $index % 2;?>
    key: <?php echo $key;?>
        <?php endforeach;
    endif;
else:
    echo "";
endif;?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }
}
