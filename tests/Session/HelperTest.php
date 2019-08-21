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

use Leevel\Di\Container;
use Leevel\Session\Helper;
use Leevel\Session\ISession;
use Tests\TestCase;

/**
 * helper test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.08.10
 *
 * @version 1.0
 */
class HelperTest extends TestCase
{
    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        Container::singletons()->clear();
    }

    public function testSession(): void
    {
        $session = $this->createMock(ISession::class);
        $this->assertNull($session->set('foo', 'bar'));
        $session->method('get')->willReturn('bar');
        $this->assertSame('bar', $session->get('foo'));

        $container = $this->createContainer();
        $container->singleton('sessions', function () use ($session) {
            return $session;
        });

        $this->assertInstanceof(ISession::class, f('Leevel\\Session\\Helper\\session'));
        $this->assertNull(f('Leevel\\Session\\Helper\\session_set', 'foo', 'bar'));
        $this->assertSame('bar', f('Leevel\\Session\\Helper\\session_get', 'foo'));
    }

    public function testFlash(): void
    {
        $session = $this->createMock(ISession::class);
        $this->assertNull($session->flashs(['foo' => 'bar']));
        $session->method('getFlash')->willReturn('bar');
        $this->assertSame('bar', $session->getFlash('foo'));

        $container = $this->createContainer();
        $container->singleton('sessions', function () use ($session) {
            return $session;
        });

        $this->assertNull(f('Leevel\\Session\\Helper\\flash_set', 'foo', 'bar'));
        $this->assertSame('bar', f('Leevel\\Session\\Helper\\flash_get', 'foo'));
    }

    public function testSessionHelper(): void
    {
        $session = $this->createMock(ISession::class);
        $this->assertNull($session->set('foo', 'bar'));
        $session->method('get')->willReturn('bar');
        $this->assertSame('bar', $session->get('foo'));

        $container = $this->createContainer();
        $container->singleton('sessions', function () use ($session) {
            return $session;
        });

        $this->assertInstanceof(ISession::class, Helper::session());
        $this->assertNull(Helper::sessionSet('foo', 'bar'));
        $this->assertSame('bar', Helper::sessionGet('foo'));
    }

    public function testFlashHelper(): void
    {
        $session = $this->createMock(ISession::class);
        $this->assertNull($session->flashs(['foo' => 'bar']));
        $session->method('getFlash')->willReturn('bar');
        $this->assertSame('bar', $session->getFlash('foo'));

        $container = $this->createContainer();
        $container->singleton('sessions', function () use ($session) {
            return $session;
        });

        $this->assertNull(Helper::flashSet('foo', 'bar'));
        $this->assertSame('bar', Helper::flashGet('foo'));
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();

        return $container;
    }
}
