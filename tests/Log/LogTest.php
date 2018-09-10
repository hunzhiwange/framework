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

namespace Tests\Log;

use Leevel\Filesystem\Fso;
use Leevel\Log\File;
use Leevel\Log\IConnect;
use Leevel\Log\ILog;
use Leevel\Log\Log;
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
    public function testBaseUse(string $level)
    {
        $log = new Log($this->createFileConnect());

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

        $this->assertInstanceof(IConnect::class, $log->getConnect());

        Fso::deleteDirectory(__DIR__.'/cacheLog', true);
    }

    public function baseUseProvider()
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

    public function testSetOption()
    {
        $log = new Log($this->createFileConnect());

        $log->setOption('levels', [ILog::INFO]);

        $log->info('foo', ['hello', 'world']);
        $log->debug('foo', ['hello', 'world']);

        $this->assertSame([ILog::INFO => [[ILog::INFO, 'foo', ['hello', 'world']]]], $log->all());
    }

    public function testLogFilterLevel()
    {
        $log = new Log($this->createFileConnect());

        $log->setOption('levels', [ILog::INFO]);

        $log->log(ILog::INFO, 'foo', ['hello', 'world']);
        $log->log(ILog::DEBUG, 'foo', ['hello', 'world']);

        $this->assertSame([ILog::INFO => [[ILog::INFO, 'foo', ['hello', 'world']]]], $log->all());
    }

    public function testFilter()
    {
        $log = new Log($this->createFileConnect());

        $log->filter(function (string $level, string $message, array $context) {
            $this->assertSame('foo', $message);
            $this->assertSame(['hello', 'world'], $context);

            if (ILog::DEBUG === $level) {
                return false;
            }
        });

        $log->log(ILog::INFO, 'foo', ['hello', 'world']);
        $log->log(ILog::DEBUG, 'foo', ['hello', 'world']);

        $this->assertSame([ILog::INFO => [[ILog::INFO, 'foo', ['hello', 'world']]]], $log->all());
    }

    public function testProcessor()
    {
        $log = new Log($this->createFileConnect());

        $log->processor(function (string $level, string $message, array $context) {
            $this->assertSame(ILog::INFO, $level);
            $this->assertSame('foo', $message);
            $this->assertSame(['hello', 'world'], $context);
        });

        $log->log(ILog::INFO, 'foo', ['hello', 'world']);

        $this->assertSame([ILog::INFO => [[ILog::INFO, 'foo', ['hello', 'world']]]], $log->all());

        $log->flush();

        $this->assertSame([], $log->all());

        Fso::deleteDirectory(__DIR__.'/cacheLog', true);
    }

    public function testWithOutBuffer()
    {
        $log = new Log($this->createFileConnect(), ['buffer' => false]);

        $this->assertInstanceof(ILog::class, $log);

        $dir = __DIR__.'/cacheLog';

        $this->assertDirectoryNotExists($dir);

        $this->assertNull($log->info('foo', ['hello', 'world']));

        $this->assertDirectoryExists($dir);

        Fso::deleteDirectory(__DIR__.'/cacheLog', true);
    }

    protected function createFileConnect(): File
    {
        return new File([
            'path' => __DIR__.'/cacheLog',
        ]);
    }
}
