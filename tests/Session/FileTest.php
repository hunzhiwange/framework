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

namespace Tests\Session;

use Leevel\Cache\Cache;
use Leevel\Session\File;
use Leevel\Session\ISession;
use Leevel\Session\Session;
use SessionHandlerInterface;
use Tests\TestCase;

/**
 * file test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.16
 *
 * @version 1.0
 */
class FileTest extends TestCase
{
    protected function tearDown(): void
    {
        $dirPath = __DIR__.'/cacheFile';

        if (is_dir($dirPath)) {
            rmdir($dirPath);
        }
    }

    public function testBaseUse()
    {
        $session = new Session($this->createFileSessionHandler());

        $this->assertInstanceof(ISession::class, $session);

        $this->assertFalse($session->isStart());
        $this->assertNull($session->getId());
        $this->assertSame('UID', $session->getName());

        $session->start();
        $this->assertTrue($session->isStart());

        $session->set('hello', 'world');
        $this->assertSame(['hello' => 'world'], $session->all());
        $this->assertTrue($session->has('hello'));
        $this->assertSame('world', $session->get('hello'));

        $session->delete('hello');
        $this->assertSame([], $session->all());
        $this->assertFalse($session->has('hello'));
        $this->assertNull($session->get('hello'));

        $session->start();
        $this->assertTrue($session->isStart());

        $this->assertInstanceof(SessionHandlerInterface::class, $session->getConnect());
    }

    public function testGetConnect()
    {
        $session = new Session($this->createFileSessionHandler());

        $this->assertInstanceof(SessionHandlerInterface::class, $connect = $session->getConnect());

        $this->assertInstanceof(Cache::class, $connect->getCache());

        $this->assertTrue($connect->open('', 'foo'));
        $this->assertTrue($connect->close());
        $this->assertSame(0, $connect->gc(0));
    }

    protected function createFileSessionHandler()
    {
        return new File([
            'path' => __DIR__.'/cacheFile',
        ]);
    }
}
