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

namespace Tests\Log;

use Leevel\Filesystem\Fso;
use Leevel\Log\File;
use Leevel\Log\ILog;
use Tests\TestCase;

/**
 * log test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.25
 *
 * @version 1.0
 */
class LogTest extends TestCase
{
    /**
     * @dataProvider baseUseProvider
     *
     * @param string $level
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

    public function testLogFilterLevel(): void
    {
        $log = $this->createFileConnect();
        $log->setOption('levels', [ILog::INFO]);
        $log->log(ILog::INFO, 'foo', ['hello', 'world']);
        $log->log(ILog::DEBUG, 'foo', ['hello', 'world']);

        $this->assertSame([ILog::INFO => [[ILog::INFO, 'foo', ['hello', 'world']]]], $log->all());
    }

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
