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

namespace Tests\Kernel;

use Error;
use Exception;
use function Tests\Kernel\Fixtures\Func\foo_bar;
use function Tests\Kernel\Fixtures\Func\helper_fn_throw;
use function Tests\Kernel\Fixtures\Func\single_fn;
use function Tests\Kernel\Fixtures\Func\testgroup_fn1;
use function Tests\Kernel\Fixtures\Func\testgroup_fn2;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="函数惰性加载",
 *     path="architecture/fn",
 *     zh-CN:description="
 * 使用函数惰性加载可以更好地管理辅助方法，避免载入过多无用的辅助函数，并且可以提高性能。
 *
 * `func` 是一个全局函数随着 `kernel` 包自动加载，可以在业务中随时使用，组件开发中请使用原生 `class_exists` 导入函数。
 * ",
 * )
 */
class FnTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="分组函数",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testGroup(): void
    {
        $this->assertFalse(function_exists('Tests\\Kernel\\Fixtures\\Func\\testgroup_fn1'));
        $this->assertFalse(function_exists('Tests\\Kernel\\Fixtures\\Func\\testgroup_fn2'));

        $result = func(fn () => testgroup_fn1());
        $this->assertSame('hello world', $result);
        $result = func(fn () => testgroup_fn2());
        $this->assertSame('hello world2', $result);

        $this->assertTrue(function_exists('Tests\\Kernel\\Fixtures\\Func\\testgroup_fn1'));
        $this->assertTrue(function_exists('Tests\\Kernel\\Fixtures\\Func\\testgroup_fn2'));
    }

    /**
     * @api(
     *     zh-CN:title="单个函数",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testSingleFn(): void
    {
        $this->assertFalse(function_exists('Tests\\Kernel\\Fixtures\\Func\\single_fn'));

        $result = func(fn () => single_fn());
        $this->assertSame('hello single fn', $result);

        $this->assertTrue(function_exists('Tests\\Kernel\\Fixtures\\Func\\single_fn'));
    }

    /**
     * @api(
     *     zh-CN:title="目录索引函数",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testIndex(): void
    {
        $this->assertFalse(function_exists('Tests\\Kernel\\Fixtures\\Func\\foo_bar'));

        $result = func(fn () => foo_bar());
        $this->assertSame('foo bar', $result);
        $result = func(fn () => foo_bar(' haha'));
        $this->assertSame('foo bar haha', $result);
        $result = func(fn (string $extend = '') => foo_bar($extend), ' haha');
        $result = func(function (string $extend = '') {
            return foo_bar($extend);
        }, ' haha');
        $this->assertSame('foo bar haha', $result);

        $this->assertTrue(function_exists('Tests\\Kernel\\Fixtures\\Func\\foo_bar'));
    }

    public function testFuncNotFound(): void
    {
        $this->assertFalse(function_exists('Tests\\Kernel\\Fixtures\\Func\\func_was_not_found'));

        $this->expectException(\Error::class);
        $this->expectExceptionMessage(
            'Call to undefined function Tests\\Kernel\\Fixtures\\Func\\func_was_not_found()'
        );

        func(fn () => \Tests\Kernel\Fixtures\Func\func_was_not_found());
    }

    public function testGroupFuncNotFound(): void
    {
        $this->assertFalse(function_exists('Tests\\Kernel\\Fixtures\\Func\\testgroup_not_found'));

        $this->expectException(\Error::class);
        $this->expectExceptionMessage(
            'Call to undefined function Tests\\Kernel\\Fixtures\\Func\\testgroup_not_found()'
        );

        func(fn () => \Tests\Kernel\Fixtures\Func\testgroup_not_found());
    }

    public function testNotFuncNotDefinedError(): void
    {
        $this->assertFalse(function_exists('Tests\\Kernel\\Fixtures\\Func\\helper_fn_throw'));

        $this->expectException(\Error::class);
        $this->expectExceptionMessage(
            'not'
        );

        func(fn () => helper_fn_throw());
        $this->assertTrue(function_exists('Tests\\Kernel\\Fixtures\\Func\\helper_fn_throw'));
    }

    public function testNotFuncWithoutUnderline(): void
    {
        $this->assertFalse(function_exists('Tests\\Kernel\\Fixtures\\Func\\fnwithoutunderline'));

        $this->expectException(\Error::class);
        $this->expectExceptionMessage(
            'Call to undefined function Tests\\Kernel\\Fixtures\\Func\\fnwithoutunderline()'
        );

        func(fn () => \Tests\Kernel\Fixtures\Func\fnwithoutunderline());
    }

    public function testNotFuncWithoutBackslash(): void
    {
        $this->assertFalse(function_exists('fnwithoutbackslash'));

        $this->expectException(\Error::class);
        $this->expectExceptionMessage(
            'Call to undefined function fnwithoutbackslash()'
        );

        func(fn () => \fnwithoutbackslash());
    }

    public function testFnAlreadyExists(): void
    {
        $this->assertTrue(function_exists('Tests\\Kernel\\fn_already_exists'));
        $result = func_exists('Tests\\Kernel\\fn_already_exists');
        $this->assertTrue($result);
    }

    public function testNotFuncWithoutThrowException(): void
    {
        $this->assertFalse(function_exists('fnwithoutbackslash'));
        $result = func_exists('fnwithoutbackslash', false);
        $this->assertFalse($result);
    }

    public function testNotCallToUndefinedFunctionErrorException(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage(
            'Hello world'
        );

        func(function () {
            throw new Error('Hello world');
        });
    }

    public function testNotCallToUndefinedFunctionErrorException2(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            'Hello world'
        );

        func(function () {
            throw new Exception('Hello world');
        });
    }
}

function fn_already_exists(): bool
{
    return true;
}
