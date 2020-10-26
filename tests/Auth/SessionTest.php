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

namespace Tests\Auth;

use Leevel\Auth\AuthException;
use Leevel\Auth\Session;
use Leevel\Cache\File as CacheFile;
use Leevel\Session\File;
use Tests\TestCase;

class SessionTest extends TestCase
{
    public function testBaseUse(): void
    {
        $session = new Session($this->createSession(), ['token' => 'token']);

        $this->assertFalse($session->isLogin());
        $this->assertSame([], $session->getLogin());

        $this->assertNull($session->login(['foo' => 'bar', 'hello' => 'world'], 10));

        $this->assertTrue($session->isLogin());
        $this->assertSame(['foo' => 'bar', 'hello' => 'world'], $session->getLogin());

        $this->assertNull($session->logout());

        $this->assertFalse($session->isLogin());
        $this->assertSame([], $session->getLogin());
    }

    public function testTokenNameWasNotSet(): void
    {
        $this->expectException(\Leevel\Auth\AuthException::class);
        $this->expectExceptionMessage(
            'Token name was not set.'
        );

        $session = new Session($this->createSession(), ['token' => null]);

        $session->isLogin();
    }

    public function testAuthExceptionReportable(): void
    {
        $e = new AuthException();
        $this->assertFalse($e->reportable());
    }

    protected function createSession(): File
    {
        $session = new File(new CacheFile([
            'path' => __DIR__.'/cache',
        ]));

        $session->start();

        return $session;
    }
}
