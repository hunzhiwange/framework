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

namespace Tests\Pipeline;

use Closure;
use Leevel\Di\Container;
use Leevel\Pipeline\Pipeline;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="管道模式",
 *     path="component/pipeline",
 *     zh-CN:description="
 * QueryPHP 提供了一个管道模式组件 `\Leevel\Pipeline\Pipeline` 对象。
 *
 * QueryPHP 管道模式提供的几个 API 命名参考了 Laravel，底层核心采用迭代器实现。
 *
 * 管道就像流水线，将复杂的问题分解为一个个小的单元，依次传递并处理，前一个单元的处理结果作为第二个单元的输入。
 * ",
 * )
 */
class PipelineTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="管道模式基本使用方法",
     *     zh-CN:description="
     * fixture 定义
     *
     * **Tests\Pipeline\First**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Pipeline\First::class)]}
     * ```
     *
     * **Tests\Pipeline\Second**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Pipeline\Second::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testPipelineBasic(): void
    {
        $result = (new Pipeline(new Container()))
            ->send(['hello world'])
            ->through([First::class, Second::class])
            ->then();

        $this->assertSame('i am in first handle and get the send:hello world', $_SERVER['test.first']);
        $this->assertSame('i am in second handle and get the send:hello world', $_SERVER['test.second']);

        unset($_SERVER['test.first'], $_SERVER['test.second']);
    }

    /**
     * @api(
     *     zh-CN:title="then 执行管道工序并返回响应结果",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testPipelineWithThen(): void
    {
        $thenCallback = function (Closure $next, $send) {
            $_SERVER['test.then'] = 'i am end and get the send:'.$send;
        };

        $result = (new Pipeline(new Container()))
            ->send(['foo bar'])
            ->through([First::class, Second::class])
            ->then($thenCallback);

        $this->assertSame('i am in first handle and get the send:foo bar', $_SERVER['test.first']);
        $this->assertSame('i am in second handle and get the send:foo bar', $_SERVER['test.second']);
        $this->assertSame('i am end and get the send:foo bar', $_SERVER['test.then']);

        unset($_SERVER['test.first'], $_SERVER['test.second'], $_SERVER['test.then']);
    }

    /**
     * @api(
     *     zh-CN:title="管道工序支持返回值",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testPipelineWithReturn(): void
    {
        $pipe1 = function (Closure $next, $send) {
            $result = $next($send);
            $this->assertSame($result, 'return 2');
            $_SERVER['test.1'] = '1 and get the send:'.$send;

            return 'return 1';
        };

        $pipe2 = function (Closure $next, $send) {
            $result = $next($send);
            $this->assertNull($result);
            $_SERVER['test.2'] = '2 and get the send:'.$send;

            return 'return 2';
        };

        $result = (new Pipeline(new Container()))
            ->send(['return test'])
            ->through([$pipe1, $pipe2])
            ->then();

        $this->assertSame('1 and get the send:return test', $_SERVER['test.1']);
        $this->assertSame('2 and get the send:return test', $_SERVER['test.2']);
        $this->assertSame('return 1', $result);

        unset($_SERVER['test.1'], $_SERVER['test.2']);
    }

    /**
     * @api(
     *     zh-CN:title="then 管道工序支持依赖注入",
     *     zh-CN:description="
     * fixture 定义
     *
     * **Tests\Pipeline\DiConstruct**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Pipeline\DiConstruct::class)]}
     * ```
     *
     * **Tests\Pipeline\TestClass**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Pipeline\TestClass::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testPipelineWithDiConstruct(): void
    {
        $result = (new Pipeline(new Container()))
            ->send(['hello world'])
            ->through([DiConstruct::class])
            ->then();

        $this->assertSame('get class:'.TestClass::class, $_SERVER['test.DiConstruct']);

        unset($_SERVER['test.DiConstruct']);
    }

    /**
     * @api(
     *     zh-CN:title="管道工序无参数",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testPipelineWithSendNoneParams(): void
    {
        $pipe = function (Closure $next) {
            $this->assertCount(1, func_get_args());
        };

        $result = (new Pipeline(new Container()))
            ->through([$pipe])
            ->then();
    }

    /**
     * @api(
     *     zh-CN:title="send 管道工序通过 send 传递参数",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testPipelineWithSendMoreParams(): void
    {
        $pipe = function (Closure $next, $send1, $send2, $send3, $send4) {
            $this->assertSame($send1, 'hello world');
            $this->assertSame($send2, 'foo');
            $this->assertSame($send3, 'bar');
            $this->assertSame($send4, 'wow');
        };

        $result = (new Pipeline(new Container()))
            ->send(['hello world'])
            ->send(['foo', 'bar', 'wow'])
            ->through([$pipe])
            ->then();
    }

    /**
     * @api(
     *     zh-CN:title="through 设置管道中的执行工序支持多次添加",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testPipelineWithThroughMore(): void
    {
        $_SERVER['test.Through.count'] = 0;

        $pipe = function (Closure $next) {
            $_SERVER['test.Through.count']++;

            $next();
        };

        $result = (new Pipeline(new Container()))
            ->through([$pipe])
            ->through([$pipe, $pipe, $pipe])
            ->through([$pipe, $pipe])
            ->then();

        $this->assertSame(6, $_SERVER['test.Through.count']);

        unset($_SERVER['test.Through.count']);
    }

    /**
     * @api(
     *     zh-CN:title="管道工序支持参数传入",
     *     zh-CN:description="
     * fixture 定义
     *
     * **Tests\Pipeline\WithArgs**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Pipeline\WithArgs::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testPipelineWithPipeArgs(): void
    {
        $params = ['one', 'two'];

        $result = (new Pipeline(new Container()))
            ->through([WithArgs::class.':'.implode(',', $params)])
            ->then();

        $this->assertSame($params, $_SERVER['test.WithArgs']);

        unset($_SERVER['test.WithArgs']);
    }

    public function testStageWithInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Stage is invalid.'
        );

        (new Pipeline(new Container()))
            ->through(['Tests\\Pipeline\\NotFound'])
            ->then();
    }

    /**
     * @api(
     *     zh-CN:title="管道工序支持自定义入口方法",
     *     zh-CN:description="
     * fixture 定义
     *
     * **Tests\Pipeline\WithAtMethod**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Pipeline\WithAtMethod::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testStageWithAtMethod(): void
    {
        (new Pipeline(new Container()))
            ->send(['hello world'])
            ->through([WithAtMethod::class.'@run'])
            ->then();

        $this->assertSame('i am in at.method handle and get the send:hello world', $_SERVER['test.at.method']);

        unset($_SERVER['test.at.method']);
    }
}

class First
{
    public function handle(Closure $next, $send)
    {
        $_SERVER['test.first'] = 'i am in first handle and get the send:'.$send;
        $next($send);
    }
}

class Second
{
    public function handle(Closure $next, $send)
    {
        $_SERVER['test.second'] = 'i am in second handle and get the send:'.$send;
        $next($send);
    }
}

class WithArgs
{
    public function handle(Closure $next, $one, $two)
    {
        $_SERVER['test.WithArgs'] = [$one, $two];
        $next();
    }
}

class TestClass
{
}

class DiConstruct
{
    protected $testClass;

    public function __construct(TestClass $testClass)
    {
        $this->testClass = $testClass;
    }

    public function handle(Closure $next, $send)
    {
        $_SERVER['test.DiConstruct'] = 'get class:'.$this->testClass::class;
        $next($send);
    }
}

class WithAtMethod
{
    public function run(Closure $next, $send)
    {
        $_SERVER['test.at.method'] = 'i am in at.method handle and get the send:'.$send;
        $next($send);
    }
}
