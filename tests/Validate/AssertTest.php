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

namespace Tests\Validate;

use Leevel\Validate\Assert;
use Tests\TestCase;

/**
 * assert test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.05.27
 *
 * @version 1.0
 *
 * @api(
 *     title="断言",
 *     path="component/validate/assert",
 *     description="这里为系统提供的基础的断言功能。",
 * )
 */
class AssertTest extends TestCase
{
    protected function setUp(): void
    {
        Assert::setPhpUnit($this);
    }

    protected function tearDown(): void
    {
        Assert::setPhpUnit(null);
    }

    /**
     * @api(
     *     title="基本断言测试",
     *     description="
     * 断言和验证器共享规则，所以可以直接参考验证器有哪些规则，排查掉依赖验证器自身的校验规则。
     *
     * **支持格式**
     *
     * ``` php
     * Assert::foo($value, string $message);
     * Assert::foo($value, array $parameter, string $message);
     * ```
     * ",
     *     note="",
     * )
     */
    public function testBaseUse(): void
    {
        Assert::notEmpty(1);
        Assert::notEmpty(55);
        Assert::notEmpty(66);

        Assert::lessThan(4, [5]);
    }

    /**
     * @api(
     *     title="断言失败默认错误消息",
     *     description="",
     *     note="",
     * )
     */
    public function testAssertFailedWithDefaultMessage(): void
    {
        $this->expectException(\Leevel\Validate\AssertException::class);
        $this->expectExceptionMessage(
            'No exception messsage specified.'
        );

        Assert::notEmpty(0);
    }

    /**
     * @api(
     *     title="断言失败自定义消息",
     *     description="",
     *     note="",
     * )
     */
    public function testAssertFailedWithCustomMessage(): void
    {
        $this->expectException(\Leevel\Validate\AssertException::class);
        $this->expectExceptionMessage(
            'No exception messsage specified.'
        );

        Assert::notEmpty(0);
    }

    /**
     * @api(
     *     title="可选断言支持",
     *     description="如果值为 `null` 直接返回正确结果。",
     *     note="",
     * )
     */
    public function testAssertOptional(): void
    {
        Assert::optionalNotEmpty(null);
    }

    /**
     * @api(
     *     title="可选断言失败",
     *     description="",
     *     note="",
     * )
     */
    public function testAssertOptionalFailed(): void
    {
        $this->expectException(\Leevel\Validate\AssertException::class);
        $this->expectExceptionMessage(
            'No exception messsage specified.'
        );

        Assert::optionalNotEmpty(0);
    }

    /**
     * @api(
     *     title="断言支持多个校验",
     *     description="必须每一个都满足规则才算校验成功。",
     *     note="",
     * )
     */
    public function testAssertMulti(): void
    {
        Assert::multiNotEmpty([3, ['hello'], 'bar', 'yes']);
    }

    /**
     * @api(
     *     title="断言支持多个校验",
     *     description="必须每一个都满足规则才算校验成功。",
     *     note="",
     * )
     */
    public function testAssertMultiFailed(): void
    {
        $this->expectException(\Leevel\Validate\AssertException::class);
        $this->expectExceptionMessage(
            'No exception messsage specified.'
        );

        Assert::multiNotEmpty([3, ['hello'], '', 'yes']);
    }

    /**
     * @api(
     *     title="断言支持多个校验也支持可选",
     *     description="必须每一个都满足规则才算校验成功, 可选会跳过验证，可选必须在最前面，即不支持 `multiOptional` 这种写法。",
     *     note="",
     * )
     */
    public function testAssertMultiWithOptional(): void
    {
        Assert::optionalMultiNotEmpty([null, ['hello'], 'bar', 'yes', null]);
    }

    /**
     * @api(
     *     title="断言支持链式表达式",
     *     description="我们可以使用链式表达式来校验规则。
     *
     * **make 原型**
     *
     * ``` php
     * Assert::make($value, ?string $message)
     * ```
     *
     * 第一个参数为待校验的值，第二个为默认校验失败消息，每一条验证规则也支持自己的失败消息。
     * ",
     *     note="",
     * )
     */
    public function testAssertChain(): void
    {
        Assert::make(5, 'Assert success.')
            ->notEmpty()
            ->lessThan([7]);
    }

    /**
     * @api(
     *     title="断言支持延迟释放",
     *     description="可以将所有错误几种抛出。
     *
     * **lazy 原型**
     *
     * ``` php
     * Assert::lazy($value, ?string $message, bool $all = true)
     * ```
     *
     * 第一个参数为待校验的值，第二个为默认校验失败消息，第三个为是否全部验证，每一条验证规则也支持自己的失败消息。
     * ",
     *     note="",
     * )
     */
    public function testAssertLazyChain(): void
    {
        $result = Assert::lazy(5, 'Assert success.')
            ->notEmpty()
            ->lessThan([7], '5 not less than 3')
            ->lessThan([8], '5 not less than 4')
            ->lessThan([9], '5 not less than 2')
            ->flush();

        $this->assert($result);
    }

    /**
     * @api(
     *     title="断言失败延迟释放",
     *     description="",
     *     note="",
     * )
     */
    public function testAssertLazyChainFailed(): void
    {
        $this->expectException(\Leevel\Validate\AssertException::class);
        $this->expectExceptionMessage(
            '5 not less than 3'.PHP_EOL.
            '5 not less than 4'.PHP_EOL.
            '5 not less than 2'
        );

        Assert::lazy(5, 'Assert success.')
            ->notEmpty()
            ->lessThan([3], '5 not less than 3')
            ->lessThan([4], '5 not less than 4')
            ->lessThan([2], '5 not less than 2')
            ->flush(function (array $error): string {
                return implode(PHP_EOL, $error);
            });
    }
}
