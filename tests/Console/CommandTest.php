<?php

declare(strict_types=1);

namespace Tests\Console;

use Leevel\Kernel\Utils\Api;
use Tests\Console\Command\CallOtherCommand;
use Tests\Console\Command\DemoCommand;
use Tests\Console\Load1\Test1;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '命令行脚本',
    'path' => 'component/console',
    'zh-CN:description' => <<<'EOT'
QueryPHP 内置控制台命名，底层采用 `Symfony/console` 开发，用法与 Symfony 一致，对基础命令进行了简单的封装。
几个简单的封装来自 `Laravel`，是对 Symfony 的基础命令做了一些常用功能的包装，可以完全满足常用开发需求。

Console 组件是 Symfony 里面的一个控制台命令组件，可以轻松地编写出运行在 CLI 上面的命名。
EOT,
])]
final class CommandTest extends TestCase
{
    use BaseCommand;

    #[Api([
        'zh-CN:title' => '基本使用方法',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Console\Load1\Test1**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Console\Load1\Test1::class)]}
```

**Tests\Console\Command\CallOtherCommand**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Console\Command\CallOtherCommand::class)]}
```
EOT,
    ])]
    public function testBaseUse(): void
    {
        $result = $this->runCommand(new CallOtherCommand(), [
            'command' => 'call:other',
        ], function ($container, $application): void {
            $application->normalizeCommands([Test1::class]);
        });

        $result = $this->normalizeContent($result);

        static::assertStringContainsString($this->normalizeContent('call other command test.'), $result);
        static::assertStringContainsString($this->normalizeContent('load1 test1'), $result);

        // argument and config
        static::assertStringContainsString($this->normalizeContent('argument is {"command":"call:other"}'), $result);
        static::assertStringContainsString($this->normalizeContent('config is {"env":null,"help":false'), $result);

        // table
        static::assertStringContainsString($this->normalizeContent('| Item  | Value |'), $result);
        static::assertStringContainsString($this->normalizeContent('| hello | world |'), $result);
        static::assertStringContainsString($this->normalizeContent('| foo   | bar   |'), $result);

        // time
        static::assertStringContainsString($this->normalizeContent(']test time'), $result);

        // question
        static::assertStringContainsString($this->normalizeContent('a question'), $result);

        // error
        static::assertStringContainsString($this->normalizeContent('a error message'), $result);
    }

    public function test1(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            'Application was not set.'
        );

        $command = new DemoCommand();
        $command->call('hello:notfound');
    }

    public function test2(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            'Container was not set.'
        );

        $command = new DemoCommand();
        $command->getContainer();
    }

    public function test3(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            'Output was not set.'
        );

        $command = new DemoCommand();
        $this->invokeTestMethod($command, 'getOutput');
    }

    public function test4(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            'Input was not set.'
        );

        $command = new DemoCommand();
        $this->invokeTestMethod($command, 'getInput');
    }
}
