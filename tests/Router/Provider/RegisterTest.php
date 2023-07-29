<?php

declare(strict_types=1);

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
use Leevel\View\IView;
use Tests\TestCase;

/**
 * @internal
 */
final class RegisterTest extends TestCase
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
        $this->assertInstanceof(IView::class, $container->make('view'));

        static::assertSame('http://www.queryphp.cn/foo/bar?hello=world', $url->make('foo/bar', ['hello' => 'world']));
    }

    protected function createContainer(): Container
    {
        $container = new Container();

        $option = new Option([
            'app' => [
                'with_suffix' => false,
                'suffix' => '.html',
                'domain' => 'queryphp.cn',
            ],
            'cookie' => [
                'domain' => '',
                'path' => '/',
                'expire' => 86400,
                'secure' => false,
                'httponly' => false,
                'samesite' => null,
            ],
            'view' => [
                'success' => 'success',
                'fail' => 'fail',
            ],
        ]);
        $container->singleton('option', $option);

        $request = $this->createMock(Request::class);
        $request->method('getEnter')->willReturn('');
        static::assertSame('', $request->getEnter());
        $request->method('isSecure')->willReturn(false);
        static::assertFalse($request->isSecure());
        $container->singleton('request', $request);

        $view = $this->createMock(IView::class);
        $container->singleton('view', $view);

        $session = $this->createMock(ISession::class);
        $container->singleton('session', $session);

        return $container;
    }
}
