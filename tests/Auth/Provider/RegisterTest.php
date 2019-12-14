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

namespace Tests\Auth\Provider;

use Leevel\Auth\Provider\Register;
use Leevel\Auth\Session;
use Leevel\Di\Container;
use Leevel\Option\Option;
use Leevel\Session\File as SessionFile;
use Tests\TestCase;

/**
 * register test.
 */
class RegisterTest extends TestCase
{
    public function testBaseUse(): void
    {
        $test = new Register($container = $this->createContainer());
        $test->register();
        $container->alias($test->providers());

        // auths
        $manager = $container->make('auths');
        $this->assertFalse($manager->isLogin());
        $this->assertSame([], $manager->getLogin());
        $this->assertNull($manager->login(['foo' => 'bar', 'hello' => 'world'], 10));
        $this->assertTrue($manager->isLogin());
        $this->assertSame(['foo' => 'bar', 'hello' => 'world'], $manager->getLogin());
        $this->assertNull($manager->logout());
        $this->assertFalse($manager->isLogin());
        $this->assertSame([], $manager->getLogin());

        // auth
        $session = $container->make('auth');
        $this->assertInstanceOf(Session::class, $session);
        $this->assertFalse($session->isLogin());
        $this->assertSame([], $session->getLogin());
    }

    protected function createSession(): SessionFile
    {
        $session = new SessionFile([
            'path' => __DIR__.'/cache',
        ]);

        $session->start();

        return $session;
    }

    protected function createContainer(): Container
    {
        $container = new Container();

        $option = new Option([
            'auth' => [
                'default'     => 'web',
                'web_default' => 'session',
                'api_default' => 'token',
                'connect'     => [
                    'session' => [
                        'driver' => 'session',
                        'token'  => 'token',
                    ],
                    'token' => [
                        'driver'      => 'token',
                        'token'       => null,
                        'input_token' => 'token',
                    ],
                ],
            ],
        ]);

        $container->singleton('option', $option);

        $container->singleton('session', $this->createSession());

        return $container;
    }
}
