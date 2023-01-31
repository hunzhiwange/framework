<?php

declare(strict_types=1);

namespace Tests\Log;

use Leevel\Filesystem\Helper;
use Leevel\Log\File;
use Leevel\Log\ILog;
use Monolog\Logger;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="Log",
 *     path="component/log",
 *     zh-CN:description="
 * 日志记录统一由日志组件完成，通常我们使用代理 `\Leevel\Log\Proxy\Log` 类进行静态调用。
 *
 * 内置支持的 log 驱动类型包括 file、syslog，未来可能增加其他驱动。
 *
 * ::: tip
 * 日志遵循 PSR-3 规范，用法与主流框架完全一致。
 * :::
 *
 * ## 使用方式
 *
 * 使用容器 logs 服务
 *
 * ``` php
 * \App::make('logs')->emergency(string $message, array $context = []): void;
 * \App::make('logs')->alert(string $message, array $context = []): void;
 * \App::make('logs')->critical(string $message, array $context = []): void;
 * \App::make('logs')->error(string $message, array $context = []): void;
 * \App::make('logs')->warning(string $message, array $context = []): void;
 * \App::make('logs')->notice(string $message, array $context = []): void;
 * \App::make('logs')->info(string $message, array $context = []): void;
 * \App::make('logs')->debug(string $message, array $context = []): void;
 * ```
 *
 * 依赖注入
 *
 * ``` php
 * class Demo
 * {
 *     private \Leevel\Log\Manager $log;
 *
 *     public function __construct(\Leevel\Log\Manager $log)
 *     {
 *         $this->log = $log;
 *     }
 * }
 * ```
 *
 * 使用静态代理
 *
 * ``` php
 * \Leevel\Log\Proxy\Log::emergency(string $message, array $context = []): void;
 * \Leevel\Log\Proxy\Log::alert(string $message, array $context = []): void;
 * \Leevel\Log\Proxy\Log::critical(string $message, array $context = []): void;
 * \Leevel\Log\Proxy\Log::error(string $message, array $context = []): void;
 * \Leevel\Log\Proxy\Log::warning(string $message, array $context = []): void;
 * \Leevel\Log\Proxy\Log::notice(string $message, array $context = []): void;
 * \Leevel\Log\Proxy\Log::info(string $message, array $context = []): void;
 * \Leevel\Log\Proxy\Log::debug(string $message, array $context = []): void;
 * ```
 *
 * ## log 配置
 *
 * 系统的 log 配置位于应用下面的 `option/log.php` 文件。
 *
 * 可以定义多个日志连接，并且支持切换，每一个连接支持驱动设置。
 *
 * ``` php
 * {[file_get_contents('option/log.php')]}
 * ```
 *
 * log 参数根据不同的连接会有所区别，通用的 log 参数如下：
 *
 * |配置项|配置描述|
 * |:-|:-|
 * |level|允许记录的日志级别|
 * |channel|频道|
 * |buffer|是否启用缓冲|
 * |buffer_size|日志数量达到缓冲数量会执行一次 IO 操作|
 *
 * ::: warning 注意
 * QueryPHP 的日志如果启用了缓冲，会在日志数量达到缓冲数量会执行一次 IO 操作。
 * :::
 * ",
 * )
 */
class LogTest extends TestCase
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
     *
     * @api(
     *     zh-CN:title="log 基本使用",
     *     zh-CN:description="
     * 除了 PSR-3 支持的方法外，系统还提供了一些额外方法。
     *
     * **支持的日志类型**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Log\LogTest::class, 'baseUseProvider')]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(string $level): void
    {
        $log = $this->createFileConnect();

        $this->assertInstanceof(ILog::class, $log);

        $this->assertNull($log->{$level}('foo', ['hello', 'world']));

        $logData = $this->getTestProperty($log, 'logs');
        $this->assertSame([$level => [
            ILOG::DEFAULT_MESSAGE_CATEGORY => [[$level, 'foo', ['hello', 'world']]],
        ],
        ], $logData);
        $this->assertInstanceOf(Logger::class, $log->getMonolog());
        Helper::deleteDirectory(__DIR__.'/cacheLog');
    }

    public function baseUseProvider(): array
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

    /**
     * @api(
     *     zh-CN:title="日志支持等级过滤",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testLogFilterLevel(): void
    {
        $log = $this->createFileConnect([
            'level' => [
                ILOG::DEFAULT_MESSAGE_CATEGORY => ILog::LEVEL_INFO,
            ],
        ]);
        $log->info('foo', ['hello', 'world']);
        $log->debug('foo', ['hello', 'world']);
        $logData = $this->getTestProperty($log, 'logs');
        $this->assertSame(
            [
                ILog::LEVEL_INFO => [
                    ILOG::DEFAULT_MESSAGE_CATEGORY => [
                        [ILog::LEVEL_INFO, 'foo', ['hello', 'world']],
                    ],
                ],
            ],
            $logData
        );
    }

    public function testWithOutBuffer(): void
    {
        $log = $this->createFileConnect(['buffer' => false]);
        $this->assertInstanceof(ILog::class, $log);

        $dir = __DIR__.'/cacheLog';
        $this->assertDirectoryDoesNotExist($dir);
        $this->assertNull($log->info('foo', ['hello', 'world']));
        $this->assertDirectoryExists($dir);
    }

    /**
     * @api(
     *     zh-CN:title="日志支持消息分类",
     *     zh-CN:description="
     * 系统提供的等级 `level` 无法满足更精细化的日志需求，于是对消息 `message` 定义了一套规则来满足更精细的分类。
     *
     * ::: tip
     * 消息开头满足 `[大小写字母|数字|下划线|中横线|点号|斜杆|冒号]` 会被识别为消息分类。
     * :::
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testLogMessageCategory(): void
    {
        $log = $this->createFileConnect();
        $log->info('[SQL] foo', ['hello', 'world']);
        $log->info('[SQL:FAILED] foo', ['hello', 'world']);
        $logData = $this->getTestProperty($log, 'logs');
        $this->assertSame([
            ILog::LEVEL_INFO => [
                'SQL'        => [[ILog::LEVEL_INFO, '[SQL] foo', ['hello', 'world']]],
                'SQL:FAILED' => [[ILog::LEVEL_INFO, '[SQL:FAILED] foo', ['hello', 'world']]],
            ],
        ], $logData);
        $log->flush();
    }

    protected function createFileConnect(array $option = []): File
    {
        return new File(array_merge([
            'path' => __DIR__.'/cacheLog',
        ], $option));
    }
}
