<?php

declare(strict_types=1);

namespace Tests\View;

/**
 * 文档概述.
 *
 * @api(
 *     title="Summary",
 *     zh-CN:title="概述",
 *     zh-TW:title="概述",
 *     path="template/README",
 *     zh-CN:description="
 * QueryPHP 内置二种方式的模板引擎，一种是类似于 Smart 模板的 Code 语法，一种是 HTML 标签式的 Node 语法。
 *
 *  * code 语法，侧重简单实用，输出变量、注释等
 *  * Node 语法，严谨务实，输出循环、流程控制等
 *
 * 例外，二种语法随心嵌套，随意使用，QueryPHP 模板引擎底层分析器和编译器非常健壮，完美运行 10 年。
 *
 * QueryPHP 模板引擎技术来自于 Jecat,一款无与伦比的技术大餐，有幸在 2010 接触到这个框架，通过这个框架学到了很多。
 * ",
 * )
 */
class SummaryDoc
{
    /**
     * @api(
     *     zh-CN:title="Code 语法",
     *     zh-CN:description="侧重简单实用。",
     *     zh-CN:note="",
     *     lang="html",
     * )
     */
    public function doc1(): void
    {
        <<<'EOT'
            {{ $name }}
            EOT;
    }

    /**
     * @api(
     *     zh-CN:title="Node 语法",
     *     zh-CN:description="严谨务实。",
     *     zh-CN:note="",
     *     lang="html",
     * )
     */
    public function doc2(): void
    {
        <<<'EOT'
            {% if cond="'You' == $name" %}
                欢迎进入 QueryPHP 开发者世界！
            {% :if %}
            EOT;
    }

    /**
     * @api(
     *     zh-CN:title="拒绝交叉",
     *     zh-CN:description="下面这种写法就是错误的，模板引擎将无法正确解析。",
     *     zh-CN:note="",
     *     lang="html",
     * )
     */
    public function doc4(): void
    {
        <<<'EOT'
            {% $name %}

            {{ if cond="'You' == $name" }}
                欢迎进入 QueryPHP 开发者世界！
            {{ :if }}
            EOT;
    }

    /**
     * @api(
     *     zh-CN:title="PHP 方式",
     *     zh-CN:description="如果你不习惯使用使用内置的模板引擎，你也可以完全使用 PHP 自生来写。",
     *     zh-CN:note="",
     * )
     */
    public function doc5(): void
    {
        <<<'EOT'
            <?php if ('You' == $name): ?>
                欢迎进入 QueryPHP 开发者世界！
            <?php endif; ?>
            EOT;
    }

    /**
     * @api(
     *     zh-CN:title="扩展支持",
     *     zh-CN:description="为了减少学习成本，系统还支持 PHP 自身作为 UI 模板。",
     *     zh-CN:note="",
     * )
     */
    public function doc6(): void
    {
    }
}
