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
 * compiler for test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.07
 *
 * @version 1.0
 */
class CompilerForTest extends TestCase
{
    use Compiler;

    public function testBaseUse()
    {
        $parser = $this->createParser();

        $source = <<<'eot'
{for $i=1;$i<10;$i++}
    QueryPHP - 代码版本for <br>
{/for}
eot;

        $compiled = <<<'eot'
<?php for ($i=1;$i<10;$i++):?>
    QueryPHP - 代码版本for <br>
<?php endfor;?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        $source = <<<'eot'
<for start='1'>
    QueryPHP - node - for <br>
</for>
eot;

        $compiled = <<<'eot'
<?php for ($var = 1; $var <= 0; $var += 1):?>
    QueryPHP - node - for <br>
<?php endfor;?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        $source = <<<'eot'
<for start='1' end='10' var='myValue' step='3'>
    QueryPHP for <br>
</for>
eot;

        $compiled = <<<'eot'
<?php for ($myValue = 1; $myValue <= 10; $myValue += 3):?>
    QueryPHP for <br>
<?php endfor;?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        $source = <<<'eot'
{% for item in navigation %}
    <li><a href="{{ item.href }}">{{ item.caption }}</a></li>
{% /for %}
eot;

        $compiled = <<<'eot'
<?php foreach ($navigation as $key => $item):?>
    <li><a href="<?php echo $item->href;?>"><?php echo $item->caption;?></a></li>
<?php endforeach;?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        $source = <<<'eot'
{% for mykey,item in navigation %}
    <li><a href="{{ item.href }}">{{ item.caption }}</a></li>
{% /for %}
eot;

        $compiled = <<<'eot'
<?php foreach ($navigation as $mykey => $item):?>
    <li><a href="<?php echo $item->href;?>"><?php echo $item->caption;?></a></li>
<?php endforeach;?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        $source = <<<'eot'
{% for mykey item in navigation %}
    <li><a href="{{ item.href }}">{{ item.caption }}</a></li>
{% /for %}
eot;

        $compiled = <<<'eot'
<?php foreach ($navigation as $mykey => $item):?>
    <li><a href="<?php echo $item->href;?>"><?php echo $item->caption;?></a></li>
<?php endforeach;?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }
}
