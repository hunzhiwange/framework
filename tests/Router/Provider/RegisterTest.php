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

namespace Tests\Router\Provider;

use Leevel\Cookie\Cookie;
use Leevel\Cookie\ICookie;
use Leevel\Di\Container;
use Leevel\Http\IRequest;
use Leevel\Mvc\IView;
use Leevel\Mvc\View;
use Leevel\Option\Option;
use Leevel\Router\IResponseFactory;
use Leevel\Router\IRouter;
use Leevel\Router\IUrl;
use Leevel\Router\Provider\Register;
use Leevel\Router\Redirect;
use Leevel\Router\ResponseFactory;
use Leevel\Router\Router;
use Leevel\Router\Url;
use Leevel\Session\ISession;
use Leevel\View\IView as IViews;
use Tests\TestCase;

/**
 * register test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.09.12
 *
 * @version 1.0
 */
class RegisterTest extends TestCase
{
    public function testBaseUse()
    {
        $test = new Register($container = $this->createContainer());

        $test->register();

        $url = $container->make('url');

        $this->assertInstanceof(IRouter::class, $container->make('router'));
        $this->assertInstanceof(Router::class, $container->make('router'));
        $this->assertInstanceof(IUrl::class, $container->make('url'));
        $this->assertInstanceof(Url::class, $container->make('url'));
        $this->assertInstanceof(Redirect::class, $container->make('redirect'));
        $this->assertInstanceof(IResponseFactory::class, $container->make('response'));
        $this->assertInstanceof(ResponseFactory::class, $container->make('response'));
        $this->assertInstanceof(ICookie::class, $container->make('cookie'));
        $this->assertInstanceof(ICookie::class, $container->make('cookie'));
        $this->assertInstanceof(IView::class, $container->make('view'));
        $this->assertInstanceof(View::class, $container->make('view'));

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
            ],
            'view' => [
                'success' => 'success',
                'fail'    => 'fail',
            ],
        ]);

        $container->singleton('option', $option);

        $request = $this->createMock(IRequest::class);

        $request->method('getEnter')->willReturn('');
        $this->assertSame('', $request->getEnter());

        $request->method('isSecure')->willReturn(false);
        $this->assertFalse($request->isSecure());

        $container->singleton('request', $request);

        $view = $this->createMock(IViews::class);

        $container->singleton('view.view', $view);

        $session = $this->createMock(ISession::class);

        $container->singleton('session', $session);

        return $container;
    }
}
