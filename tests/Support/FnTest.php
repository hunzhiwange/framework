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

use Leevel\Support\Fn;
use function Tests\Support\Fixtures\Fn\foo_bar;
use function Tests\Support\Fixtures\Fn\not_found_not_found;
use function Tests\Support\Fixtures\Fn\single_fn;
use function Tests\Support\Fixtures\Fn\testgroup2_fn1;
use function Tests\Support\Fixtures\Fn\testgroup2_fn2;
use function Tests\Support\Fixtures\Fn\testgroup_fn1;
use function Tests\Support\Fixtures\Fn\testgroup_fn2;
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
 * 可以引入一个辅助函数来简化
 *
 * ``` php
 * use Leevel\Support\Fn;
 *
 * function fn($fn, ...$args)
 * {
 *     return (new Fn())($fn, ...$args);
 * }
 * ```
 * ",
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
        $this->assertFalse(function_exists('Tests\\Support\\Fixtures\\Fn\\testgroup_fn1'));
        $this->assertFalse(function_exists('Tests\\Support\\Fixtures\\Fn\\testgroup_fn2'));

        $result = (new Fn())('Tests\\Support\\Fixtures\\Fn\\testgroup_fn1');

        $this->assertSame('hello world', $result);

        $result = (new Fn())('Tests\\Support\\Fixtures\\Fn\\testgroup_fn2');

        $this->assertSame('hello world2', $result);

        $this->assertTrue(function_exists('Tests\\Support\\Fixtures\\Fn\\testgroup_fn1'));
        $this->assertTrue(function_exists('Tests\\Support\\Fixtures\\Fn\\testgroup_fn2'));
    }

    /**
     * @api(
     *     title="闭包调用已载入的分组函数",
     *     description="函数载入一次后面就都存在了，甚至可以直接使用函数。",
     *     note="",
     * )
     */
    public function testGroupWithClosureWithFuncWasLoaded(): void
    {
        $this->assertTrue(function_exists('Tests\\Support\\Fixtures\\Fn\\testgroup_fn1'));
        $this->assertTrue(function_exists('Tests\\Support\\Fixtures\\Fn\\testgroup_fn2'));

        $result = (new Fn())(function () {
            return testgroup_fn1();
        });

        $this->assertSame('hello world', $result);

        $result = (new Fn())(function () {
            return testgroup_fn2();
        });

        $this->assertSame('hello world2', $result);
    }

    /**
     * @api(
     *     title="闭包调用分组函数",
     *     description="",
     *     note="",
     * )
     */
    public function testGroupWithClosure(): void
    {
        $this->assertFalse(function_exists('Tests\\Support\\Fixtures\\Fn\\testgroup2_fn1'));
        $this->assertFalse(function_exists('Tests\\Support\\Fixtures\\Fn\\testgroup2_fn2'));

        $result = (new Fn())(function () {
            return testgroup2_fn1();
        });

        $this->assertSame('g2:hello world', $result);

        $result = (new Fn())(function () {
            return testgroup2_fn2();
        });

        $this->assertSame('g2:hello world2', $result);

        $this->assertTrue(function_exists('Tests\\Support\\Fixtures\\Fn\\testgroup2_fn1'));
        $this->assertTrue(function_exists('Tests\\Support\\Fixtures\\Fn\\testgroup2_fn2'));
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
        $this->assertFalse(function_exists('Tests\\Support\\Fixtures\\Fn\\single_fn'));

        $result = (new Fn())('Tests\\Support\\Fixtures\\Fn\\single_fn');

        $this->assertSame('hello single fn', $result);

        $this->assertTrue(function_exists('Tests\\Support\\Fixtures\\Fn\\single_fn'));
    }

    /**
     * @api(
     *     title="闭包调用单个文件函数",
     *     description="",
     *     note="",
     * )
     */
    public function testSingleFnWithClosure(): void
    {
        $this->assertTrue(function_exists('Tests\\Support\\Fixtures\\Fn\\single_fn'));

        $result = (new Fn())(function () {
            return single_fn();
        });

        $this->assertSame('hello single fn', $result);
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
        $this->assertFalse(function_exists('Tests\\Support\\Fixtures\\Fn\\foo_bar'));

        $result = (new Fn())('Tests\\Support\\Fixtures\\Fn\\foo_bar');

        $this->assertSame('foo bar', $result);

        $result = (new Fn())('Tests\\Support\\Fixtures\\Fn\\foo_bar', ' haha');

        $this->assertSame('foo bar haha', $result);

        $this->assertTrue(function_exists('Tests\\Support\\Fixtures\\Fn\\foo_bar'));
    }

    /**
     * @api(
     *     title="闭包调用 index 索引函数",
     *     description="",
     *     note="",
     * )
     */
    public function testIndexWithClosure(): void
    {
        $this->assertTrue(function_exists('Tests\\Support\\Fixtures\\Fn\\foo_bar'));

        $result = (new Fn())(function () {
            return foo_bar();
        });

        $this->assertSame('foo bar', $result);

        $result = (new Fn())(function () {
            return foo_bar(' haha');
        });

        $this->assertSame('foo bar haha', $result);
    }

    /**
     * @api(
     *     title="闭包调用多个函数",
     *     description="",
     *     note="",
     * )
     */
    public function testIndexWithClosureWithMulti(): void
    {
        $this->assertTrue(function_exists('Tests\\Support\\Fixtures\\Fn\\foo_bar'));

        $result = (new Fn())(function () {
            $result1 = foo_bar();

            return $result1.' '.foo_bar();
        });

        $this->assertSame('foo bar foo bar', $result);
    }

    public function testIndexWithClosureWithMultiWithError(): void
    {
        $this->assertFalse(function_exists('Tests\\Support\\Fixtures\\Fn\\not_found_not_found'));
        $this->assertTrue(function_exists('Tests\\Support\\Fixtures\\Fn\\foo_bar'));

        $this->expectException(\Leevel\Support\FunctionNotFoundException::class);
        $this->expectExceptionMessage(
            'Call to undefined function Tests\\Support\\Fixtures\\Fn\\not_found_not_found()'
        );

        (new Fn())(function () {
            $result1 = not_found_not_found();

            return $result1.' '.foo_bar();
        });
    }

    public function testIndexWithClosureNewStyle(): void
    {
        $this->assertTrue(function_exists('Tests\\Support\\Fixtures\\Fn\\foo_bar'));

        $result = (new Fn())(function (string $a) {
            return foo_bar($a);
        }, ' haha');

        $this->assertSame('foo bar haha', $result);
    }

    public function testArgsInvalid(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage(
            'Fn first args must be Closure or string.'
        );

        (new Fn())(5);
    }

    public function testFuncNotFound(): void
    {
        $this->assertFalse(function_exists('Tests\\Support\\Fixtures\\Fn\\func_was_not_found'));

        $this->expectException(\Leevel\Support\FunctionNotFoundException::class);
        $this->expectExceptionMessage(
            'Call to undefined function Tests\\Support\\Fixtures\\Fn\\func_was_not_found()'
        );

        (new Fn())('Tests\\Support\\Fixtures\\Fn\\func_was_not_found');
    }

    public function testGroupFuncNotFound(): void
    {
        $this->assertFalse(function_exists('Tests\\Support\\Fixtures\\Fn\\testgroup_not_found'));

        $this->expectException(\Leevel\Support\FunctionNotFoundException::class);
        $this->expectExceptionMessage(
            'Call to undefined function Tests\\Support\\Fixtures\\Fn\\testgroup_not_found()'
        );

        (new Fn())('Tests\\Support\\Fixtures\\Fn\\testgroup_not_found');
    }

    public function testNotFuncNotDefinedError(): void
    {
        $this->assertFalse(function_exists('Tests\\Support\\Fixtures\\Fn\\helper_fn_throw'));

        $this->expectException(\Error::class);
        $this->expectExceptionMessage(
            'not'
        );

        (new Fn())('Tests\\Support\\Fixtures\\Fn\\helper_fn_throw');

        $this->assertTrue(function_exists('Tests\\Support\\Fixtures\\Fn\\helper_fn_throw'));
    }

    public function testNotFuncWithoutUnderline(): void
    {
        $this->assertFalse(function_exists('Tests\\Support\\Fixtures\\Fn\\fnwithoutunderline'));

        $this->expectException(\Leevel\Support\FunctionNotFoundException::class);
        $this->expectExceptionMessage(
            'Call to undefined function Tests\\Support\\Fixtures\\Fn\\fnwithoutunderline()'
        );

        (new Fn())('Tests\\Support\\Fixtures\\Fn\\fnwithoutunderline');
    }

    public function testNotFuncWithoutBackslash(): void
    {
        $this->assertFalse(function_exists('fnwithoutbackslash'));

        $this->expectException(\Leevel\Support\FunctionNotFoundException::class);
        $this->expectExceptionMessage(
            'Call to undefined function fnwithoutbackslash()'
        );

        (new Fn())('fnwithoutbackslash');
    }

    public function testNotFuncThrowError(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage(
            'error data'
        );

        (new Fn())(function () {
            throw new \Error('error data');
        });
    }
}
