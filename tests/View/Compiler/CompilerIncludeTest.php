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
 * compiler include test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.08
 *
 * @version 1.0
 * @coversNothing
 */
class CompilerIncludeTest extends TestCase
{
    use Compiler;

    public function testBaseUse()
    {
        $parser = $this->createParser();

        $source = <<<'eot'
<include file="application/app/ui/theme/default/header.html">
eot;

        $compiled = <<<'eot'
<?php $this->display('application/app/ui/theme/default/header', [], '.html', true);?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        $source = <<<'eot'
{~$headTpl = app()->pathApplicationTheme() . '/' . '/header.html'}
<include file="$headTpl">
eot;

        $compiled = <<<'eot'
<?php $headTpl = app()->pathApplicationTheme() . '/' . '/header.html';?>
<?php $this->display($headTpl, [], '', true);?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        $source = <<<'eot'
<include file="test" />
eot;

        $compiled = <<<'eot'
<?php $this->display('test', [], '', true);?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        $source = <<<'eot'
<include file="public+header" />
eot;

        $compiled = <<<'eot'
<?php $this->display('public+header', [], '', true);?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        $source = <<<'eot'
<include file="blue@blog+view" />
eot;

        $compiled = <<<'eot'
<?php $this->display('blue@blog+view', [], '', true);?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        // 放置 . 被替换加上 () 包裹起来
        $source = <<<'eot'
<include file="($path . '/' . $name)" />
<include file="Template::tpl('header')" />
<include file="tpl('header')" />
<include file="$hello.world('header')" />
eot;

        $compiled = <<<'eot'
<?php $this->display(($path . '/' . $name), [], '', true);?>
<?php $this->display(Template::tpl('header'), [], '', true);?>
<?php $this->display(tpl('header'), [], '', true);?>
<?php $this->display($hello->world('header'), [], '', true);?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }
}
