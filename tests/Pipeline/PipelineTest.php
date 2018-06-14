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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
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
 * @coversNothing
 */
class PipelineTest extends TestCase
{
    public function testPipelineBasic()
    {
        $result = (new Pipeline(new Container()))->

        send('hello world')->

        through(['Tests\Pipeline\First', 'Tests\Pipeline\Second'])->

        then();

        $this->assertSame('i am in first handle and get the send:hello world', $_SERVER['test.first']);
        $this->assertSame('i am in second handle and get the send:hello world', $_SERVER['test.second']);

        unset($_SERVER['test.first'], $_SERVER['test.second']);
    }

    public function testPipelineWithThen()
    {
        $thenCallback = function (Closure $next, $send) {
            $_SERVER['test.then'] = 'i am end and get the send:'.$send;
        };

        $result = (new Pipeline(new Container()))->

        send('foo bar')->

        through(['Tests\Pipeline\First', 'Tests\Pipeline\Second'])->

        then($thenCallback);

        $this->assertSame('i am in first handle and get the send:foo bar', $_SERVER['test.first']);
        $this->assertSame('i am in second handle and get the send:foo bar', $_SERVER['test.second']);
        $this->assertSame('i am end and get the send:foo bar', $_SERVER['test.then']);

        unset($_SERVER['test.first'], $_SERVER['test.second'], $_SERVER['test.then']);
    }

    public function testPipelineWithReturn()
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

        send('return test')->

        through($pipe1, $pipe2)->

        then();

        $this->assertSame('1 and get the send:return test', $_SERVER['test.1']);
        $this->assertSame('2 and get the send:return test', $_SERVER['test.2']);

        unset($_SERVER['test.1'], $_SERVER['test.2']);
    }

    public function testPipelineWithDiConstruct()
    {
        $result = (new Pipeline(new Container()))->

        send('hello world')->

        through(['Tests\Pipeline\DiConstruct'])->

        then();

        $this->assertSame('get class:Tests\Pipeline\TestClass', $_SERVER['test.DiConstruct']);

        unset($_SERVER['test.DiConstruct']);
    }

    public function testPipelineWithSendNoneParams()
    {
        $pipe = function (Closure $next) {
            $this->assertSame(1, count(func_get_args()));
        };

        $result = (new Pipeline(new Container()))->

        through($pipe)->

        then();
    }

    public function testPipelineWithSendMoreParams()
    {
        $pipe = function (Closure $next, $send1, $send2, $send3, $send4) {
            $this->assertSame($send1, 'hello world');
            $this->assertSame($send2, 'foo');
            $this->assertSame($send3, 'bar');
            $this->assertSame($send4, 'wow');
        };

        $result = (new Pipeline(new Container()))->

        send('hello world')->

        send(['foo', 'bar', 'wow'])->

        through($pipe)->

        then();
    }

    public function testPipelineWithThroughMore()
    {
        $_SERVER['test.Through.count'] = 0;

        $pipe = function (Closure $next) {
            $_SERVER['test.Through.count']++;

            $next();
        };

        $result = (new Pipeline(new Container()))->

        through($pipe)->

        through($pipe, $pipe, $pipe)->

        through([$pipe, $pipe])->

        then();

        $this->assertSame(6, $_SERVER['test.Through.count']);

        unset($_SERVER['test.Through.count']);
    }

    public function testPipelineWithPipeArgs()
    {
        $parameters = ['one', 'two'];

        $result = (new Pipeline(new Container()))->

        through('Tests\Pipeline\WithArgs:'.implode(',', $parameters))->

        then();

        $this->assertSame($parameters, $_SERVER['test.WithArgs']);

        unset($_SERVER['test.WithArgs']);
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
    protected $args = [];

    public function __construct($one = null, $two = null)
    {
        $this->args = [$one, $two];
    }

    public function handle(Closure $next)
    {
        $_SERVER['test.WithArgs'] = $this->args;

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
