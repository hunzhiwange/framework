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

namespace Tests\Docs\Started;

/**
 * @api(
 *     title="Specification",
 *     zh-CN:title="开发规范",
 *     zh-TW:title="開發規範",
 *     path="started/specification",
 *     description="`QueryPHP` 遵循 `PSR-2` 命名规范和 `PSR-4` 自动加载规范。",
 *     zh-CN:description="`QueryPHP` 遵循 `PSR-2` 命名规范和 `PSR-4` 自动加载规范。",
 *     zh-TW:description="`QueryPHP` 遵循 `PSR-2` 命名规范和 `PSR-4` 自动加载规范。",
 * )
 */
class SpecificationDoc
{
    /**
     * @api(
     *     title="文件和目录",
     *     description="
     * PSR-4 基础目录使用小写，其它依次使用大驼峰法命名，例如。
     *
     * ```
     * /data/codes/queryphp/application/app/Domain/Entity/
     * /data/codes/queryphp/application/app/Domain/Entity/Test.php
     * ```
     *
     * 其中 composer 配置
     *
     * ```
     * "autoload": {
     *     "psr-4": {
     *         "App\\" : "application/app",
     *         "Admin\\" : "application/admin",
     *         "Common\\" : "common"
     *     }
     * }
     * ```
     *
     * 不存在类文件，请使用小写目录，其文件也一样:
     *
     * ```
     * /data/codes/queryphp/option/
     * /data/codes/queryphp/option/app.php
     * ```
     * ",
     *     note="",
     * )
     */
    public function doc1()
    {
    }

    /**
     * @api(
     *     title="统一代码风格",
     *     description="
     * 为了屏蔽不同用户的不同代码风格习惯，QueryPHP 设置一个统一的代码格式化配置来规范团队的代码风格，这符合 `PSR-2` 规范并且可以通过 `StyleCI` 规范。
     *
     * ### 手工优化
     *
     * 在使用前您需要安装 [php-cs-fixer](http://cs.sensiolabs.org/)，这样子才能够进行下面的工作。
     *
     * ```
     * /data/codes/queryphp/.php_cs.dist # 应用
     * /data/codes/queryphp/vendor/hunzhiwange/framework/.php_cs.dist # 框架核心包
     * ```
     *
     * 可以通过下面的方式来格式化代码风格:
     *
     * ``` sh
     * $cd /data/codes/queryphp
     * $php build/php-cs-fixer fix --config=.php_cs.dist
     * ```
     *
     * ### 结合 `Git` Hooks 来格式化代码：
     *
     * ```
     * /data/codes/queryphp/build/pre-commit.sh
     * /data/codes/queryphp/vendor/hunzhiwange/framework/build/pre-commit.sh
     * ```
     *
     * 应用 QueryPHP 脚本 `/data/codes/queryphp/build/pre-commit.sh`
     * 核心包 framework 脚本 `/data/codes/queryphp/build/pre-commit.sh`
     *
     * ::: warning
     * 应用脚本也包含一段 JS 的脚本，这个用于格式化 QueryPHP 的通用前端后台的 JS 代码风格，跟 PHP 差不多。
     * :::
     *
     * ### Git Commit
     *
     * ``` sh
     * git commit -m 'pass hook'
     * ```
     *
     * 上述脚本就会自动运行帮助你格式化代码,你也可以忽略脚本。
     *
     * ``` sh
     * git commit -n -m 'pass hook'
     * ```
     *
     * 这样子我们再也不需要浪费时间在无意义的代码风格的讨论上了。
     * ",
     *     note="",
     * )
     */
    public function doc2()
    {
    }
}
