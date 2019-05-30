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

namespace Tests\Docs;

/**
 * 如何成为 QueryPHP 开发者.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.05.30
 *
 * @version 1.0
 * @api(
 *     title="如何成为 QueryPHP 开发者",
 *     path="developer/README",
 *     description="QueryPHP 非常欢迎各位给我们共同的伟大的作品添砖加瓦，实现为 PHP 社区提供一个好框架的美好愿景。
 *
 *  _* 文档开发.基于单元测试实现的自动化文档 [当前文档开发](https://github.com/hunzhiwange/framework/projects/2)
 *  _* 计划功能.开发 [当前计划功能](https://github.com/hunzhiwange/framework/projects/6)
 *  _* 技术债务.清偿 [当前技术债务](https://github.com/hunzhiwange/framework/projects/7)
 *  _* 单元测试.尽可能减少 Bug [当前单元测试](https://github.com/hunzhiwange/framework/projects/4)
 *
 * 本篇指南将带你搭建的 QueryPHP 开发框架的开发环境，使得你可以参与 QueryPHP 底层代码、单元测试和文档等开发工作。
 * ",
 * )
 */
class BecomeAQueryphpDeveloperDoc
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
        /*
        {$name}

        {if $name == 'You'}
            欢迎进入 QueryPHP 开发者世界！
        {/if}
        */
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
        /*
        <if condition="$name eq 'You'">
            欢迎进入 QueryPHP 开发者世界！
        </if>
        */
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
        /*
        #_
        {{ i + 1 }}
        */
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
        /*
        <$name>

        {if condition="$name eq 'You'"}
            欢迎进入 QueryPHP 开发者世界！
        {/if}
        */
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
        /*
        <?php if ($name == 'You'): ?>
            欢迎进入 QueryPHP 开发者世界！
        <?php endif; ?>
        */
    }

    /**
     * @api(
     *     title="扩展支持",
     *     description="为了减少学习成本，系统还支持 PHP 自身作为 UI 模板。",
     *
     *     note="",
     * )
     */
    public function doc6()
    {
    }
}
