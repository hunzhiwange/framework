<?php

declare(strict_types=1);

namespace Leevel\Router\Provider;

use Leevel\Di\IContainer;
use Leevel\Di\Provider;
use Leevel\Http\CookieUtils;
use Leevel\Router\IRouter;
use Leevel\Router\Redirect;
use Leevel\Router\Response;
use Leevel\Router\Router;

/**
 * 路由服务提供者.
 */
class Register extends Provider
{
    /**
     * {@inheritDoc}
     */
    public function register(): void
    {
        $this->router();
        $this->redirect();
        $this->response();
    }

    /**
     * bootstrap.
     */
    public function bootstrap(): void
    {
        $this->cookie();
    }

    /**
     * {@inheritDoc}
     */
    public static function providers(): array
    {
        return [
            'router' => [IRouter::class, Router::class],
            'redirect' => Redirect::class,
            'response' => Response::class,
        ];
    }

    /**
     * 注册 router 服务.
     */
    protected function router(): void
    {
        $this->container
            ->singleton(
                'router',
                fn (IContainer $container): Router => new Router($container),
            )
        ;
    }

    /**
     * 注册 redirect 服务.
     */
    protected function redirect(): void
    {
        $this->container
            ->singleton(
                'redirect',
                function (IContainer $container): Redirect {
                    $redirect = new Redirect();
                    if (isset($container['session'])) {
                        $redirect->setSession($container['session']);
                    }

                    return $redirect;
                },
            )
        ;
    }

    /**
     * 注册 response 服务
     */
    protected function response(): void
    {
        $this->container
            ->singleton(
                'response',
                function (IContainer $container): Response {
                    $config = $container['config'];

                    return (new Response($container['view'], $container['redirect']))
                        ->setViewSuccessTemplate($config->get('view\\success'))
                        ->setViewFailTemplate($config->get('view\\fail'))
                    ;
                },
            )
        ;
    }

    /**
     * 设置 COOKIE 助手配置.
     */
    protected function cookie(): void
    {
        /** @var \Leevel\Config\IConfig $config */
        $config = $this->container->make('config');
        CookieUtils::initConfig((array) $config->get('cookie\\', []));
    }
}
