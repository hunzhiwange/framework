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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\View\Compiler;

use Tests\TestCase;

/**
 * compiler php test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.07
 *
 * @version 1.0
 *
 * @api(
 *     title="PHP 标签",
 *     path="template/php",
 *     description="PHP 代码可以和标签在模板文件中混合使用，可以在模板文件里面书写任意的 PHP 语句代码 ，包括下面两种方式。",
 * )
 */
class CompilerPhpTest extends TestCase
{
    use Compiler;

    /**
     * @api(
     *     title="基本使用",
     *     description="",
     *     note="",
     * )
     */
    public function testBaseUse()
    {
        $parser = $this->createParser();

        $source = <<<'eot'
<php>echo 'Hello,world!';</php>
eot;

        $compiled = <<<'eot'
<?php echo 'Hello,world!'; ?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     title="原始 PHP",
     *     description="",
     *     note="不过这种方式来使用 PHP 脚本，这是我们大力推荐的写法，用最原始的 PHP 开发项目是我们共同的追求。",
     * )
     */
    public function testPhpSelf()
    {
        $parser = $this->createParser();

        $source = <<<'eot'
<?php echo 'Hello,world!'; ?>
eot;

        $compiled = <<<'eot'
<?php echo 'Hello,world!'; ?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }

    /**
     * @api(
     *     title="PHP 内部不能使用标签",
     *     description="PHP 标签或者 PHP 代码里面就不能再使用标签（包括 code 标签和 node 标签），因此下面的几种方式都是无效的：",
     *     note="程序运行结果是抛出致命错误，这种写法是错误的。",
     * )
     */
    public function testErrorExample()
    {
        $parser = $this->createParser();

        // 错误的写法
        $source = <<<'eot'
<php>
    {if $hello == ''}
        Yet !
    {/if}
</php>
eot;

        $compiled = <<<'eot'
<?php 
    <?php if ($hello == ''): ?>
        Yet !
    <?php endif; ?>
 ?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }
}
