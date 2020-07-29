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

namespace Tests\Flow;

use Leevel\Flow\FlowControl;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="Flow Control",
 *     zh-CN:title="流程控制",
 *     path="component/flow",
 *     zh-CN:description="
 * QueryPHP 为流程控制类统一抽象了一个基础流程控制类 `\Leevel\Flow\FlowControl`，流程控制类可以轻松接入。
 *
 * 系统一些关键服务，比如说数据库查询条件、HTTP 响应等流程控制类均接入了统一的抽象层。
 * ",
 * note="你可以根据不同场景灵活运用，以满足产品需求。",
 * )
 */
class FlowControlTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="基础使用方法",
     *     zh-CN:description="
     * **fixture 定义**
     *
     * **Tests\Flow\Test1**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Flow\Test1::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $test = new Test1();

        $this->assertSame('', $test->value());

        $condition1 = 1;

        $value = $test
            ->if($condition1)
            ->condition1()
            ->else()
            ->condition2()
            ->fi()
            ->value();

        $this->assertSame('condition1', $value);
    }

    /**
     * @api(
     *     zh-CN:title="else 条件语句",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testElse(): void
    {
        $test = new Test1();

        $condition1 = 0;

        $value = $test
            ->if($condition1)
            ->condition1()
            ->else()
            ->condition2()
            ->fi()
            ->value();

        $this->assertSame('condition2', $value);
    }

    /**
     * @dataProvider getElseData
     *
     * @api(
     *     zh-CN:title="else 条件语句例子",
     *     zh-CN:description="
     * **测试例子**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Flow\FlowControlTest::class, 'getElseData')]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testElseMulti(int $condition, string $result): void
    {
        $test = new Test1();

        $value = $test
            ->if(0 === $condition)
            ->condition1()
            ->elif(1 === $condition)
            ->condition2()
            ->elif(2 === $condition)
            ->condition3()
            ->elif(3 === $condition)
            ->condition4()
            ->else() // else 仅能根据上一次的 elif 或 if 来做判断，这里为 elif(3 === $condition)
            ->condition5()
            ->fi()
            ->value();

        $this->assertSame($result, $value);
    }

    public function getElseData()
    {
        return [
            [0, 'condition1 condition5'],
            [1, 'condition2 condition5'],
            [2, 'condition3 condition5'],
            [3, 'condition4'], // else 仅能根据上一次的 elif 或 if 来做判断，这里为 elif(3 === $condition)
            [4, 'condition5'],
            [5, 'condition5'],
            [6, 'condition5'],
        ];
    }

    /**
     * @api(
     *     zh-CN:title="elif 条件语句",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testElseIfs(): void
    {
        $test = new Test1();

        $condition1 = 1;

        $value = $test
            ->if(0 === $condition1)
            ->condition1()
            ->elif(1 === $condition1)
            ->condition2()
            ->fi()
            ->value();

        $this->assertSame('condition2', $value);
    }
}

class Test1
{
    use FlowControl;

    protected $value = [];

    public function __call(string $method, array $args)
    {
    }

    public function value()
    {
        return implode(' ', $this->value);
    }

    public function condition1()
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->value[] = 'condition1';

        return $this;
    }

    public function condition2()
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->value[] = 'condition2';

        return $this;
    }

    public function condition3()
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->value[] = 'condition3';

        return $this;
    }

    public function condition4()
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->value[] = 'condition4';

        return $this;
    }

    public function condition5()
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->value[] = 'condition5';

        return $this;
    }
}
