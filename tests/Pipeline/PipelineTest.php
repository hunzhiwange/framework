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

namespace Tests\Pipeline;

use Closure;
use Leevel\Di\Container;
use Leevel\Pipeline\Pipeline;
use Tests\TestCase;

/**
 * pipeline 组件测试.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.05.27
 *
 * @version 1.0
 */
class PipelineTest extends TestCase
{
    public function testPipelineBasic(): void
    {
        $result = (new Pipeline(new Container()))->

        send(['hello world'])->

        through(['Tests\Pipeline\First', 'Tests\Pipeline\Second'])->

        then();

        $this->assertSame('i am in first handle and get the send:hello world', $_SERVER['test.first']);
        $this->assertSame('i am in second handle and get the send:hello world', $_SERVER['test.second']);

        unset($_SERVER['test.first'], $_SERVER['test.second']);
    }

    public function testPipelineWithThen(): void
    {
        $thenCallback = function (Closure $next, $send) {
            $_SERVER['test.then'] = 'i am end and get the send:'.$send;
        };

        $result = (new Pipeline(new Container()))->

        send(['foo bar'])->

        through(['Tests\Pipeline\First', 'Tests\Pipeline\Second'])->

        then($thenCallback);

        $this->assertSame('i am in first handle and get the send:foo bar', $_SERVER['test.first']);
        $this->assertSame('i am in second handle and get the send:foo bar', $_SERVER['test.second']);
        $this->assertSame('i am end and get the send:foo bar', $_SERVER['test.then']);

        unset($_SERVER['test.first'], $_SERVER['test.second'], $_SERVER['test.then']);
    }

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

        $result = (new Pipeline(new Container()))->

        send(['return test'])->

        through([$pipe1, $pipe2])->

        then();

        $this->assertSame('1 and get the send:return test', $_SERVER['test.1']);
        $this->assertSame('2 and get the send:return test', $_SERVER['test.2']);

        unset($_SERVER['test.1'], $_SERVER['test.2']);
    }

    public function testPipelineWithDiConstruct(): void
    {
        $result = (new Pipeline(new Container()))->

        send(['hello world'])->

        through(['Tests\Pipeline\DiConstruct'])->

        then();

        $this->assertSame('get class:Tests\Pipeline\TestClass', $_SERVER['test.DiConstruct']);

        unset($_SERVER['test.DiConstruct']);
    }

    public function testPipelineWithSendNoneParams(): void
    {
        $pipe = function (Closure $next) {
            $this->assertCount(1, func_get_args());
        };

        $result = (new Pipeline(new Container()))->

        through([$pipe])->

        then();
    }

    public function testPipelineWithSendMoreParams(): void
    {
        $pipe = function (Closure $next, $send1, $send2, $send3, $send4) {
            $this->assertSame($send1, 'hello world');
            $this->assertSame($send2, 'foo');
            $this->assertSame($send3, 'bar');
            $this->assertSame($send4, 'wow');
        };

        $result = (new Pipeline(new Container()))->

        send(['hello world'])->

        send(['foo', 'bar', 'wow'])->

        through([$pipe])->

        then();
    }

    public function testPipelineWithThroughMore(): void
    {
        $_SERVER['test.Through.count'] = 0;

        $pipe = function (Closure $next) {
            $_SERVER['test.Through.count']++;

            $next();
        };

        $result = (new Pipeline(new Container()))->

        through([$pipe])->

        through([$pipe, $pipe, $pipe])->

        through([$pipe, $pipe])->

        then();

        $this->assertSame(6, $_SERVER['test.Through.count']);

        unset($_SERVER['test.Through.count']);
    }

    public function testPipelineWithPipeArgs(): void
    {
        $params = ['one', 'two'];

        $result = (new Pipeline(new Container()))->

        through(['Tests\Pipeline\WithArgs:'.implode(',', $params)])->

        then();

        $this->assertSame($params, $_SERVER['test.WithArgs']);

        unset($_SERVER['test.WithArgs']);
    }

    public function testStageWithInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Stage is invalid.'
        );

        (new Pipeline(new Container()))->

        through(['Tests\Pipeline\NotFound'])->

        then();
    }

    public function testStageWithAtMethod(): void
    {
        (new Pipeline(new Container()))->

        send(['hello world'])->

        through(['Tests\Pipeline\WithAtMethod@run'])->

        then();

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
        $_SERVER['test.DiConstruct'] = 'get class:'.get_class($this->testClass);

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
