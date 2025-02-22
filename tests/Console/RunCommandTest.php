<?php

declare(strict_types=1);

namespace Tests\Console;

use Leevel\Console\Application;
use Leevel\Console\RunCommand;
use Leevel\Di\Container;
use Leevel\Kernel\Utils\Api;
use Tests\Console\Command\CallOtherCommand;
use Tests\Console\Load1\Test1;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '运行命令代码',
    'path' => 'component/console/run-command',
    'zh-CN:description' => <<<'EOT'
有时候我们需要在非命令行调用命令，比如在控制器等地方直接运行命令行代码，系统对这种场景进行了简单封装。
EOT,
])]
final class RunCommandTest extends TestCase
{
    #[Api([
        'zh-CN:title' => '运行命令代码基本使用方法',
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
        'zh-CN:note' => <<<'EOT'
normalizeCommand 格式化命令，主要用于一个命令可能会调用其它命令，需要预先加载。
EOT,
    ])]
    public function testBaseUse(): void
    {
        $application = new Application(new Container(), '1.0');
        $runCommand = new RunCommand($application);

        $runCommand->normalizeCommand(Test1::class);
        $result = $runCommand->handle(new CallOtherCommand(), [
            'command' => 'call:other',
        ]);

        $result = $this->normalizeContent($result);

        self::assertStringContainsString($this->normalizeContent('call other command test.'), $result);
        self::assertStringContainsString($this->normalizeContent('load1 test1'), $result);

        // argument and config
        self::assertStringContainsString($this->normalizeContent('argument is {"command":"call:other"}'), $result);
        self::assertStringContainsString($this->normalizeContent('config is {"env":null,"help":false'), $result);

        // table
        self::assertStringContainsString($this->normalizeContent('| Item  | Value |'), $result);
        self::assertStringContainsString($this->normalizeContent('| hello | world |'), $result);
        self::assertStringContainsString($this->normalizeContent('| foo   | bar   |'), $result);

        // time
        self::assertStringContainsString($this->normalizeContent(']test time'), $result);

        // question
        self::assertStringContainsString($this->normalizeContent('a question'), $result);

        // error
        self::assertStringContainsString($this->normalizeContent('a error message'), $result);
    }
}
