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

namespace Tests\Support;

use Tests\TestCase;

/**
 * fn test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.04.10
 *
 * @version 1.0
 *
 * @api(
 *     title="函数惰性加载",
 *     path="architecture/fn",
 *     description="使用函数惰性加载可以更好地管理辅助方法，避免载入过多无用的辅助函数，并且可以提高性能。
 *
 * `f` 是一个超级的全局函数随着 `Support` 包自动加载，可以在业务中随时使用，组件开发中只是标注依赖 `leevel/support` 包也可以使用。",
 * )
 */
class FnTest extends TestCase
{
    /**
     * @api(
     *     title="字符串调用分组函数",
     *     description="",
     *     note="",
     * )
     */
    public function testGroup(): void
    {
        $this->assertFalse(function_exists('Tests\\Support\\Fixtures\Func\testgroup_fn1'));
        $this->assertFalse(function_exists('Tests\\Support\\Fixtures\Func\testgroup_fn2'));

        $result = f('Tests\\Support\\Fixtures\Func\testgroup_fn1');

        $this->assertSame('hello world', $result);

        $result = f('Tests\\Support\\Fixtures\Func\testgroup_fn2');

        $this->assertSame('hello world2', $result);

        $this->assertTrue(function_exists('Tests\\Support\\Fixtures\Func\testgroup_fn1'));
        $this->assertTrue(function_exists('Tests\\Support\\Fixtures\Func\testgroup_fn2'));
    }

    /**
     * @api(
     *     title="字符串调用单个文件函数",
     *     description="",
     *     note="",
     * )
     */
    public function testSingleFn(): void
    {
        $this->assertFalse(function_exists('Tests\\Support\\Fixtures\Func\single_fn'));

        $result = f('Tests\\Support\\Fixtures\Func\single_fn');

        $this->assertSame('hello single fn', $result);

        $this->assertTrue(function_exists('Tests\\Support\\Fixtures\Func\single_fn'));
    }

    /**
     * @api(
     *     title="字符串调用 index 索引函数",
     *     description="",
     *     note="",
     * )
     */
    public function testIndex(): void
    {
        $this->assertFalse(function_exists('Tests\\Support\\Fixtures\Func\foo_bar'));

        $result = f('Tests\\Support\\Fixtures\Func\foo_bar');

        $this->assertSame('foo bar', $result);

        $result = f('Tests\\Support\\Fixtures\Func\foo_bar', ' haha');

        $this->assertSame('foo bar haha', $result);

        $this->assertTrue(function_exists('Tests\\Support\\Fixtures\Func\foo_bar'));
    }

    public function testFuncNotFound(): void
    {
        $this->assertFalse(function_exists('Tests\\Support\\Fixtures\Func\func_was_not_found'));

        $this->expectException(\Leevel\Support\FunctionNotFoundException::class);
        $this->expectExceptionMessage(
            'Call to undefined function Tests\\Support\\Fixtures\Func\func_was_not_found()'
        );

        f('Tests\\Support\\Fixtures\Func\func_was_not_found');
    }

    public function testGroupFuncNotFound(): void
    {
        $this->assertFalse(function_exists('Tests\\Support\\Fixtures\Func\testgroup_not_found'));

        $this->expectException(\Leevel\Support\FunctionNotFoundException::class);
        $this->expectExceptionMessage(
            'Call to undefined function Tests\\Support\\Fixtures\Func\testgroup_not_found()'
        );

        f('Tests\\Support\\Fixtures\Func\testgroup_not_found');
    }

    public function testNotFuncNotDefinedError(): void
    {
        $this->assertFalse(function_exists('Tests\\Support\\Fixtures\Func\helper_fn_throw'));

        $this->expectException(\Error::class);
        $this->expectExceptionMessage(
            'not'
        );

        f('Tests\\Support\\Fixtures\Func\helper_fn_throw');

        $this->assertTrue(function_exists('Tests\\Support\\Fixtures\Func\helper_fn_throw'));
    }

    public function testNotFuncWithoutUnderline(): void
    {
        $this->assertFalse(function_exists('Tests\\Support\\Fixtures\Func\fnwithoutunderline'));

        $this->expectException(\Leevel\Support\FunctionNotFoundException::class);
        $this->expectExceptionMessage(
            'Call to undefined function Tests\\Support\\Fixtures\Func\fnwithoutunderline()'
        );

        f('Tests\\Support\\Fixtures\Func\fnwithoutunderline');
    }

    public function testNotFuncWithoutBackslash(): void
    {
        $this->assertFalse(function_exists('fnwithoutbackslash'));

        $this->expectException(\Leevel\Support\FunctionNotFoundException::class);
        $this->expectExceptionMessage(
            'Call to undefined function fnwithoutbackslash()'
        );

        f('fnwithoutbackslash');
    }

    public function testFnAlreadyExists(): void
    {
        $this->assertTrue(function_exists('Tests\\Support\\fn_already_exists'));
        $result = f_exists('Tests\\Support\\fn_already_exists');
        $this->assertTrue($result);
    }

    public function testNotFuncWithoutThrowException(): void
    {
        $this->assertFalse(function_exists('fnwithoutbackslash'));
        $result = f_exists('fnwithoutbackslash', false);
        $this->assertFalse($result);
    }
}

function fn_already_exists(): bool
{
    return true;
}
