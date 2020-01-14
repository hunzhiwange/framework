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

namespace Tests\Log;

use Leevel\Filesystem\Fso;
use Leevel\Log\File;
use Leevel\Log\ILog;
use Tests\TestCase;

/**
 * @api(
 *     title="Log",
 *     path="component/log",
 *     description="
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
 * 使用助手函数
 *
 * ``` php
 * \Leevel\Log\Helper::record(string $message, array $context = [], string $level = \Leevel\Log\ILog::INFO): void;
 * \Leevel\Log\Helper::log(): \Leevel\Log\Manager;
 * ```
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
 * \App::make('logs')->log(string $level, string $message, array $context = []): void;
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
 * \Leevel\Log\Proxy\Log::log(string $level, string $message, array $context = []): void;
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
 * |levels|允许记录的日志级别|
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
    /**
     * @dataProvider baseUseProvider
     *
     * @api(
     *     title="log 基本使用",
     *     description="
     * 除了 PSR-3 支持的方法外，系统还提供了一些额外方法。
     *
     * **支持的日志类型**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Log\LogTest::class, 'baseUseProvider')]}
     * ```
     *
     * **获取日志记录数量**
     *
     * ``` php
     * count(?string $level = null): int;
     * ```
     *
     * **获取当前日志记录**
     *
     * ``` php
     * all(?string $level = null): array;
     * ```
     *
     * **清理日志记录**
     *
     * ``` php
     * clear(?string $level = null): void;
     * ```
     *
     * 除了这些外，还有一些辅助方法如 `isMonolog`，因为 `Monolog` 非常流行，底层进行了一些封装。
     * ",
     *     note="",
     * )
     */
    public function testBaseUse(string $level): void
    {
        $log = $this->createFileConnect();

        $this->assertInstanceof(ILog::class, $log);

        $this->assertNull($log->{$level}('foo', ['hello', 'world']));
        $this->assertSame([$level => [[$level, 'foo', ['hello', 'world']]]], $log->all());
        $this->assertSame([[$level, 'foo', ['hello', 'world']]], $log->all($level));

        $this->assertSame(1, $log->count());
        $this->assertSame(1, $log->count($level));

        $this->assertNull($log->clear($level));
        $this->assertSame([], $log->all($level));

        $this->assertNull($log->clear());
        $this->assertSame([], $log->all());
        $this->assertSame([], $log->all($level));

        $this->assertFalse($log->isMonolog());
        $this->assertNull($log->getMonolog());

        Fso::deleteDirectory(__DIR__.'/cacheLog', true);
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

    public function testSetOption(): void
    {
        $log = $this->createFileConnect();
        $log->setOption('levels', [ILog::INFO]);
        $log->info('foo', ['hello', 'world']);
        $log->debug('foo', ['hello', 'world']);
        $this->assertSame([ILog::INFO => [[ILog::INFO, 'foo', ['hello', 'world']]]], $log->all());
    }

    /**
     * @api(
     *     title="日志支持等级过滤",
     *     description="",
     *     note="",
     * )
     */
    public function testLogFilterLevel(): void
    {
        $log = $this->createFileConnect();
        $log->setOption('levels', [ILog::INFO]);
        $log->log(ILog::INFO, 'foo', ['hello', 'world']);
        $log->log(ILog::DEBUG, 'foo', ['hello', 'world']);

        $this->assertSame([ILog::INFO => [[ILog::INFO, 'foo', ['hello', 'world']]]], $log->all());
    }

    /**
     * @api(
     *     title="日志支持默认等级 debug",
     *     description="",
     *     note="",
     * )
     */
    public function testLogLevelNotFoundWithDefaultLevel(): void
    {
        $log = $this->createFileConnect();
        $log->setOption('levels', [ILog::DEBUG]);
        $log->log('notfound', 'foo', ['hello', 'world']);
        $this->assertSame([ILog::DEBUG => [[ILog::DEBUG, 'foo', ['hello', 'world']]]], $log->all());

        $log->flush();

        Fso::deleteDirectory(__DIR__.'/cacheLog', true);
    }

    public function testWithOutBuffer(): void
    {
        $log = $this->createFileConnect(['buffer' => false]);

        $this->assertInstanceof(ILog::class, $log);

        $dir = __DIR__.'/cacheLog';

        $this->assertDirectoryNotExists($dir);

        $this->assertNull($log->info('foo', ['hello', 'world']));

        $this->assertDirectoryExists($dir);

        Fso::deleteDirectory(__DIR__.'/cacheLog', true);
    }

    protected function createFileConnect(array $option = []): File
    {
        return new File(array_merge([
            'path' => __DIR__.'/cacheLog',
        ], $option));
    }
}
