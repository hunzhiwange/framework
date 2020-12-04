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

namespace Tests\Router\Provider;

use Leevel\Di\Container;
use Leevel\Http\Request;
use Leevel\Option\Option;
use Leevel\Router\IRouter;
use Leevel\Router\IUrl;
use Leevel\Router\Provider\Register;
use Leevel\Router\Redirect;
use Leevel\Router\Response;
use Leevel\Router\Router;
use Leevel\Router\Url;
use Leevel\Session\ISession;
use Leevel\View\Manager;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    public function testBaseUse(): void
    {
        $test = new Register($container = $this->createContainer());

        $test->register();
        $test->bootstrap();
        $url = $container->make('url');

        $this->assertInstanceof(IRouter::class, $container->make('router'));
        $this->assertInstanceof(Router::class, $container->make('router'));
        $this->assertInstanceof(IUrl::class, $container->make('url'));
        $this->assertInstanceof(Url::class, $container->make('url'));
        $this->assertInstanceof(Redirect::class, $container->make('redirect'));
        $this->assertInstanceof(Response::class, $container->make('response'));
        $this->assertInstanceof(Response::class, $container->make('response'));
        $this->assertInstanceof(Manager::class, $container->make('views'));

        $this->assertSame('http://www.queryphp.cn/foo/bar?hello=world', $url->make('foo/bar', ['hello' => 'world']));
    }

    protected function createContainer(): Container
    {
        $container = new Container();

        $option = new Option([
            'app' => [
                'with_suffix'  => false,
                'suffix'       => '.html',
                'domain'       => 'queryphp.cn',
            ],
            'cookie' => [
                'domain'   => '',
                'path'     => '/',
                'expire'   => 86400,
                'secure'   => false,
                'httponly' => false,
                'samesite' => null,
            ],
            'view' => [
                'success' => 'success',
                'fail'    => 'fail',
            ],
        ]);
        $container->singleton('option', $option);

        $request = $this->createMock(Request::class);
        $request->method('getEnter')->willReturn('');
        $this->assertSame('', $request->getEnter());
        $request->method('isSecure')->willReturn(false);
        $this->assertFalse($request->isSecure());
        $container->singleton('request', $request);

        $view = $this->createMock(Manager::class);
        $container->singleton('views', $view);

        $session = $this->createMock(ISession::class);
        $container->singleton('session', $session);

        return $container;
    }
}
