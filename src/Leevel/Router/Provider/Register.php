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

namespace Leevel\Router\Provider;

use Leevel\Di\IContainer;
use Leevel\Di\Provider;
use Leevel\Http\CookieUtils;
use Leevel\Router\IRouter;
use Leevel\Router\IUrl;
use Leevel\Router\IView;
use Leevel\Router\Redirect;
use Leevel\Router\Response;
use Leevel\Router\Router;
use Leevel\Router\Url;
use Leevel\Router\View;

/**
 * router 服务提供者.
 */
class Register extends Provider
{
    /**
     * {@inheritDoc}
     */
    public function register(): void
    {
        $this->router();
        $this->url();
        $this->redirect();
        $this->response();
        $this->view();
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
            'router'   => [IRouter::class, Router::class],
            'url'      => [IUrl::class, Url::class],
            'redirect' => Redirect::class,
            'response' => Response::class,
            'view'     => [IView::class, View::class],
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
            );
    }

    /**
     * 注册 url 服务.
     */
    protected function url(): void
    {
        $this->container
            ->singleton(
                'url',
                function (IContainer $container): Url {
                    $option = $container['option'];
                    $options = [];
                    foreach (['with_suffix', 'suffix', 'domain'] as $item) {
                        $options[$item] = $option->get($item);
                    }

                    return new Url($container['request'], $options);
                },
            );
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
                    $redirect = new Redirect($container['url']);
                    if (isset($container['session'])) {
                        $redirect->setSession($container['session']);
                    }

                    return $redirect;
                },
            );
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
                    $option = $container['option'];

                    return (new Response($container['view'], $container['redirect']))
                        ->setViewSuccessTemplate($option->get('view\\success'))
                        ->setViewFailTemplate($option->get('view\\fail'));
                },
            );
    }

    /**
     * 注册 view 服务
     */
    protected function view(): void
    {
        $this->container
            ->singleton(
                'view',
                fn (IContainer $container): View => new View($container['view.view']),
            );
    }

    /**
     * 设置 COOKIE 助手配置.
     */
    protected function cookie(): void
    {
        /** @var \Leevel\Option\IOption $option */
        $option = $this->container->make('option');
        CookieUtils::initOption($option->get('cookie\\'));
    }
}
