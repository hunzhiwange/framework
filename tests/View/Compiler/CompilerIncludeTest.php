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
 *     zh-CN:title="Include 标签",
 *     path="template/include",
 *     zh-CN:description="可以使用 include 标签来包含外部的模板文件。",
 * )
 */
class CompilerIncludeTest extends TestCase
{
    use Compiler;

    /**
     * @api(
     *     zh-CN:title="使用完整文件名包含",
     *     zh-CN:description="
     * 使用方法如下：
     *
     * ``` html
     * <include file="完整模板文件名" />
     * ```
     *
     * 这种情况下，模板文件名必须包含后缀。
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $parser = $this->createParser();

        $source = <<<'eot'
            <include file="application/app/ui/theme/default/header.html">
            eot;

        $compiled = <<<'eot'
            <?php $this->display('application/app/ui/theme/default/header', [], '.html', true); ?>
            eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     zh-CN:title="使用变量定义完整的文件",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testVar(): void
    {
        $parser = $this->createParser();

        $source = <<<'eot'
            {~$headTpl = \Leevel::themesPath() . '/' . 'header.html'}
            <include file="$headTpl">
            eot;

        $compiled = <<<'eot'
            <?php $headTpl = \Leevel::themesPath() . '/' . 'header.html'; ?>
            <?php $this->display($headTpl, [], '', true); ?>
            eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     zh-CN:title="包含当前视图目录下的模板文件",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testInViewDir(): void
    {
        $parser = $this->createParser();

        $source = <<<'eot'
            <include file="test" />
            eot;

        $compiled = <<<'eot'
            <?php $this->display('test', [], '', true); ?>
            eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     zh-CN:title="包含其他模块的操作模板",
     *     zh-CN:description="其中模块以目录分隔",
     *     zh-CN:note="",
     * )
     */
    public function testOtherModule(): void
    {
        $parser = $this->createParser();

        $source = <<<'eot'
            <include file="public+header" />
            eot;

        $compiled = <<<'eot'
            <?php $this->display('public+header', [], '', true); ?>
            eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        $source = <<<'eot'
            <include file="public/header" />
            eot;

        $compiled = <<<'eot'
            <?php $this->display('public/header', [], '', true); ?>
            eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     zh-CN:title="函数表达式支持",
     *     zh-CN:description="为了防止 `.` 被解析为 `->`，需要由 `()` 包裹起来，`file` 内容区的解析规则遵循 `if` 标签的 `condition` 特性。",
     *     zh-CN:note="",
     * )
     */
    public function testExpr(): void
    {
        $parser = $this->createParser();

        // 防止 . 被替换加上 () 包裹起来
        $source = <<<'eot'
            <include file="($path . '/' . $name)" />
            <include file="Template::tpl('header')" />
            <include file="tpl('header')" />
            <include file="$hello.world('header')" />
            eot;

        $compiled = <<<'eot'
            <?php $this->display(($path . '/' . $name), [], '', true); ?>
            <?php $this->display(Template::tpl('header'), [], '', true); ?>
            <?php $this->display(tpl('header'), [], '', true); ?>
            <?php $this->display($hello->world('header'), [], '', true); ?>
            eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }
}
