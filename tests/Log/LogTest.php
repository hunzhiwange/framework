<?php

declare(strict_types=1);

namespace Tests\Log;

use Leevel\Filesystem\Helper;
use Leevel\Log\File;
use Leevel\Log\ILog;
use Monolog\Logger;
use Psr\Log\LogLevel;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => 'Log',
    'path' => 'component/log',
    'zh-CN:description' => <<<'EOT'
日志记录统一由日志组件完成，通常我们使用代理 `\Leevel\Log\Proxy\Log` 类进行静态调用。

内置支持的 log 驱动类型包括 file、syslog，未来可能增加其他驱动。

::: tip
日志遵循 PSR-3 规范，用法与主流框架完全一致。
:::

## 使用方式

使用容器 logs 服务

``` php
\App::make('logs')->emergency(string|\Stringable $message, array $context = []): void;
\App::make('logs')->alert(string|\Stringable $message, array $context = []): void;
\App::make('logs')->critical(string|\Stringable $message, array $context = []): void;
\App::make('logs')->error(string|\Stringable $message, array $context = []): void;
\App::make('logs')->warning(string|\Stringable $message, array $context = []): void;
\App::make('logs')->notice(string|\Stringable $message, array $context = []): void;
\App::make('logs')->info(string|\Stringable $message, array $context = []): void;
\App::make('logs')->debug(string|\Stringable $message, array $context = []): void;
```

依赖注入

``` php
class Demo
{
    private \Leevel\Log\Manager $log;

    public function __construct(\Leevel\Log\Manager $log)
    {
        $this->log = $log;
    }
}
```

使用静态代理

``` php
\Leevel\Log\Proxy\Log::emergency(string|\Stringable $message, array $context = []): void;
\Leevel\Log\Proxy\Log::alert(string|\Stringable $message, array $context = []): void;
\Leevel\Log\Proxy\Log::critical(string|\Stringable $message, array $context = []): void;
\Leevel\Log\Proxy\Log::error(string|\Stringable $message, array $context = []): void;
\Leevel\Log\Proxy\Log::warning(string|\Stringable $message, array $context = []): void;
\Leevel\Log\Proxy\Log::notice(string|\Stringable $message, array $context = []): void;
\Leevel\Log\Proxy\Log::info(string|\Stringable $message, array $context = []): void;
\Leevel\Log\Proxy\Log::debug(string|\Stringable $message, array $context = []): void;
```

## log 配置

系统的 log 配置位于应用下面的 `option/log.php` 文件。

可以定义多个日志连接，并且支持切换，每一个连接支持驱动设置。

``` php
{[file_get_contents('option/log.php')]}
```

log 参数根据不同的连接会有所区别，通用的 log 参数如下：

|配置项|配置描述|
|:-|:-|
|level|允许记录的日志级别|
|channel|频道|
EOT,
])]
final class LogTest extends TestCase
{
    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        $dirPath = [
            __DIR__.'/cacheLog',
        ];
        foreach ($dirPath as $v) {
            if (is_dir($v)) {
                Helper::deleteDirectory($v);
            }
        }
    }

    /**
     * @dataProvider baseUseProvider
     */
    #[Api([
        'zh-CN:title' => 'log 基本使用',
        'zh-CN:description' => <<<'EOT'
除了 PSR-3 支持的方法外，系统还提供了一些额外方法。

**支持的日志类型**

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Log\LogTest::class, 'baseUseProvider')]}
```
EOT,
    ])]
    public function testBaseUse(string $level): void
    {
        $log = $this->createFileConnect();

        $this->assertInstanceof(ILog::class, $log);

        static::assertNull($log->{$level}('foo', ['hello', 'world']));

        static::assertInstanceOf(Logger::class, $log->getMonolog());
        Helper::deleteDirectory(__DIR__.'/cacheLog');
    }

    public static function baseUseProvider(): array
    {
        return [
            ['emergency'],
            ['alert'],
            ['critical'],
            ['error'],
            ['warning'],
            ['notice'],
            ['info'],
            ['debug'],
        ];
    }

    #[Api([
        'zh-CN:title' => '日志支持等级过滤',
    ])]
    public function testLogFilterLevel(): void
    {
        $log = $this->createFileConnect([
            'level' => [
                ILOG::DEFAULT_MESSAGE_CATEGORY => LogLevel::INFO,
            ],
        ]);
        $log->info('foo', ['hello', 'world']);
        $log->debug('foo', ['hello', 'world']);
        $fileInfo = __DIR__.'/cacheLog/development.info/*-'.date('Y-m-d').'.log';
        $fileDebug = __DIR__.'/cacheLog/development.debug/*-'.date('Y-m-d').'.log';
        static::assertTrue(is_file($fileInfo));
        static::assertTrue(!is_file($fileDebug));
    }

    public function testWithOutBuffer(): void
    {
        $log = $this->createFileConnect(['buffer' => false]);
        $this->assertInstanceof(ILog::class, $log);

        $dir = __DIR__.'/cacheLog';
        static::assertDirectoryDoesNotExist($dir);
        static::assertNull($log->info('foo', ['hello', 'world']));
        static::assertDirectoryExists($dir);
    }

    #[Api([
        'zh-CN:title' => '日志支持消息分类',
        'zh-CN:description' => <<<'EOT'
系统提供的等级 `level` 无法满足更精细化的日志需求，于是对消息 `message` 定义了一套规则来满足更精细的分类。

::: tip
消息开头满足 `[大小写字母|数字|下划线|中横线|点号|斜杆|冒号]` 会被识别为消息分类。
:::
EOT,
    ])]
    public function testLogMessageCategory(): void
    {
        $log = $this->createFileConnect();
        $log->info('[SQL] foo', ['hello', 'world']);
        $log->info('[SQL:FAILED] foo', ['hello', 'world']);
        $fileInfo1 = __DIR__.'/cacheLog/development.info/SQL:FAILED-'.date('Y-m-d').'.log';
        $fileInfo2 = __DIR__.'/cacheLog/development.info/SQL-'.date('Y-m-d').'.log';
        static::assertTrue(is_file($fileInfo1));
        static::assertTrue(is_file($fileInfo2));
    }

    public function test1(): void
    {
        $this->expectException(\Psr\Log\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Level notFound is invalid.'
        );

        $log = $this->createFileConnect();
        $log->log('notFound', 'hello world');
    }

    protected function createFileConnect(array $option = []): File
    {
        return new File(array_merge([
            'path' => __DIR__.'/cacheLog',
        ], $option));
    }
}
