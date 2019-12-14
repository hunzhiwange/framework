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

namespace Tests\View;

/**
 * 文档概述.
 *
 * @api(
 *     title="Summary",
 *     zh-CN:title="概述",
 *     zh-TW:title="概述",
 *     path="template/README",
 *     description="
 * QueryPHP 内置三种方式的模板引擎，一种是类似于 Smart 模板的 Code 语法，一种是 HTML 标签式的 Node 语法，例外还有一种类 Javascript 的语法与 Twig 比较相似。
 *
 *  * code 语法，侧重简单实用
 *  * Node 语法，严谨务实
 *  * JS 语法，现代潮流
 *
 * 例外，三种语法随心嵌套，随意使用，QueryPHP 模板引擎底层分析器和编译器非常健壮，完美运行 8 年。
 *
 * QueryPHP 模板引擎技术来自于 Jecat,一款无与伦比的技术大餐，有幸在 2010 接触到这个框架，通过这个框架学到了很多。
 * ",
 * )
 */
class SummaryDoc
{
    /**
     * @api(
     *     title="Code 语法",
     *     description="侧重简单实用。",
     *     note="",
     *     lang="html",
     * )
     */
    public function doc1()
    {
        <<<'EOT'
            {$name}

            {if $name == 'You'}
                欢迎进入 QueryPHP 开发者世界！
            {/if}
            EOT;
    }

    /**
     * @api(
     *     title="Node 语法",
     *     description="严谨务实。",
     *     note="",
     *     lang="html",
     * )
     */
    public function doc2()
    {
        <<<'EOT'
            <if condition="$name eq 'You'">
                欢迎进入 QueryPHP 开发者世界！
            </if>
            EOT;
    }

    /**
     * @api(
     *     title="现代化类 JS 语法",
     *     description="现代潮流。",
     *     note="",
     *     lang="html",
     * )
     */
    public function doc3()
    {
        <<<'EOT'
            {{ i + 1 }}
            EOT;
    }

    /**
     * @api(
     *     title="拒绝交叉",
     *     description="下面这种写法就是错误的，模板引擎将无法正确解析。",
     *     note="",
     *     lang="html",
     * )
     */
    public function doc4()
    {
        <<<'EOT'
            <$name>

            {if condition="$name eq 'You'"}
                欢迎进入 QueryPHP 开发者世界！
            {/if}
            EOT;
    }

    /**
     * @api(
     *     title="PHP 方式",
     *     description="如果你不习惯使用使用内置的模板引擎，你也可以完全使用 PHP 自生来写。",
     *     note="",
     * )
     */
    public function doc5()
    {
        <<<'EOT'
            <?php if ($name == 'You'): ?>
                欢迎进入 QueryPHP 开发者世界！
            <?php endif; ?>
            EOT;
    }

    /**
     * @api(
     *     title="扩展支持",
     *     description="为了减少学习成本，系统还支持 PHP 自身作为 UI 模板。",
     *     note="",
     * )
     */
    public function doc6()
    {
    }
}
