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

namespace Tests\Docs;

/**
 * @api(
 *     zh-CN:title="自动化测试",
 *     path="test/README",
 *     zh-CN:description="
 * QueryPHP 自身经过大量的单元测试用例验证过，取得了非常好的效果，对于业务层测试来说，我们也提供了基础的测试功能。
 * ",
 * )
 */
class TestDoc
{
    /**
     * @api(
     *     zh-CN:title="基本使用方法",
     *     zh-CN:description="
     * **fixture 定义**
     *
     * **tests/Example/ExampleTest.php**
     *
     * ``` php
     * {[file_get_contents('tests/Example/ExampleTest.php')]}
     * ```
     * ",
     *     zh-CN:note="",
     *     lang="shell",
     * )
     */
    public function doc1(): void
    {
    }
}
