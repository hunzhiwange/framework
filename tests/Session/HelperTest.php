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

namespace Tests\Session;

use Leevel\Di\Container;
use Leevel\Session\Helper;
use Leevel\Session\ISession;
use Leevel\Session\Manager;
use Tests\Session\Fixtures\Manager1;
use Tests\TestCase;

/**
 * helper test.
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
        $connect = $this->createMock(ISession::class);
        $this->assertNull($connect->set('foo', 'bar'));
        $connect->method('get')->willReturn('bar');
        $this->assertSame('bar', $connect->get('foo'));

        $container = $this->createContainer();
        $session = new Manager1($container);
        Manager1::setConnect($connect);
        $container->singleton('sessions', function () use ($session): Manager {
            return $session;
        });

        $this->assertInstanceof(Manager::class, f('Leevel\\Session\\Helper\\session'));
        $this->assertNull(f('Leevel\\Session\\Helper\\set', 'foo', 'bar'));
        $this->assertSame('bar', f('Leevel\\Session\\Helper\\get', 'foo'));
    }

    public function testFlash(): void
    {
        $connect = $this->createMock(ISession::class);
        $this->assertNull($connect->flashs(['foo' => 'bar']));
        $connect->method('getFlash')->willReturn('bar');
        $this->assertSame('bar', $connect->getFlash('foo'));

        $container = $this->createContainer();
        $session = new Manager1($container);
        Manager1::setConnect($connect);
        $container->singleton('sessions', function () use ($session): Manager {
            return $session;
        });

        $this->assertNull(f('Leevel\\Session\\Helper\\flash', 'foo', 'bar'));
        $this->assertSame('bar', f('Leevel\\Session\\Helper\\get_flash', 'foo'));
    }

    public function testSessionHelper(): void
    {
        $connect = $this->createMock(ISession::class);
        $this->assertNull($connect->set('foo', 'bar'));
        $connect->method('get')->willReturn('bar');
        $this->assertSame('bar', $connect->get('foo'));

        $container = $this->createContainer();
        $session = new Manager1($container);
        Manager1::setConnect($connect);
        $container->singleton('sessions', function () use ($session): Manager {
            return $session;
        });

        $this->assertInstanceof(Manager::class, Helper::session());
        $this->assertNull(Helper::set('foo', 'bar'));
        $this->assertSame('bar', Helper::get('foo'));
    }

    public function testFlashHelper(): void
    {
        $connect = $this->createMock(ISession::class);
        $this->assertNull($connect->flashs(['foo' => 'bar']));
        $connect->method('getFlash')->willReturn('bar');
        $this->assertSame('bar', $connect->getFlash('foo'));

        $container = $this->createContainer();
        $session = new Manager1($container);
        Manager1::setConnect($connect);
        $container->singleton('sessions', function () use ($session) {
            return $session;
        });

        $this->assertNull(Helper::flash('foo', 'bar'));
        $this->assertSame('bar', Helper::getFlash('foo'));
    }

    public function testHelperNotFound(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage(
            'Call to undefined function Leevel\\Session\\Helper\\not_found()'
        );

        $this->assertFalse(Helper::notFound());
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();

        return $container;
    }
}
