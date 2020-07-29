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
 *     zh-CN:title="快捷标签",
 *     path="template/quick",
 *     zh-CN:description="为了使得模板定义更加简洁，系统还支持一些常用的变量输出快捷标签。",
 * )
 */
class CompilerQuickTest extends TestCase
{
    use Compiler;

    /**
     * @api(
     *     zh-CN:title="# 注释标签",
     *     zh-CN:description="模板中的注释仅供模板制作人员查看，最终不会显示出来。",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $parser = $this->createParser();

        $source = <<<'eot'
            {# 我是一个注释 #}
            
            {#
                我是两行注释
              Thank U!
            #}
            eot;

        $compiled = <<<'eot'
             
            
             
            eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     zh-CN:title="~ 原样 PHP 标签",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testOriginalPhp(): void
    {
        $parser = $this->createParser();

        $source = <<<'eot'
            {~$value = 'Make QueryPHP greater !'}
            {$value}
            eot;

        $compiled = <<<'eot'
            <?php $value = 'Make QueryPHP greater !'; ?>
            <?php echo $value; ?>
            eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     zh-CN:title=": echo 快捷方式",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testEcho(): void
    {
        $parser = $this->createParser();

        $source = <<<'eot'
            {:'Hello QueryPHP!'}
            eot;

        $compiled = <<<'eot'
            <?php echo 'Hello QueryPHP!'; ?>
            eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }
}
