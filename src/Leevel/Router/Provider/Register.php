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

namespace Leevel\Router\Provider;

use Leevel\Cookie\Cookie;
use Leevel\Di\IContainer;
use Leevel\Di\Provider;
use Leevel\Http\Request;
use Leevel\Http\Response;
use Leevel\Mvc\View;
use Leevel\Router\Redirect;
use Leevel\Router\ResponseFactory;
use Leevel\Router\Router;
use Leevel\Router\Url;

/**
 * router 服务提供者.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.05.12
 *
 * @version 1.0
 */
class Register extends Provider
{
    /**
     * 注册服务.
     */
    public function register(): void
    {
        $this->router();
        $this->url();
        $this->redirect();
        $this->response();
        $this->cookie();
        $this->view();
        $this->cookieResolver();
    }

    /**
     * 可用服务提供者.
     *
     * @return array
     */
    public static function providers(): array
    {
        return [
            'router' => [
                'Leevel\\Router\\Router',
                'Leevel\\Router\\IRouter',
            ],
            'url' => [
                'Leevel\\Router\\Url',
            ],
            'redirect' => [
                'Leevel\\Router\\Redirect',
            ],
            'response' => [
                'Leevel\\Router\\IResponseFactory',
                'Leevel\\Router\\ResponseFactory',
            ],
            'cookie' => [
                'Leevel\\Cookie\\Cookie',
                'Leevel\\Cookie\\ICookie',
            ],
            'view' => [
                'Leevel\\Mvc\\View',
                'Leevel\\Mvc\\IView',
            ],
        ];
    }

    /**
     * 注册 router 服务
     */
    protected function router()
    {
        $this->container->singleton('router', function (IContainer $container) {
            return new Router($container);
        });
    }

    /**
     * 注册 url 服务
     */
    protected function url()
    {
        $this->container->singleton('url', function (IContainer $container) {
            $option = $container['option'];
            $router = $container['router'];

            $options = [];

            foreach ([
                'with_suffix',
                'suffix',
                'domain',
            ] as $item) {
                $options[$item] = $option->get($item);
            }

            return new Url($container['request'], $options);
        });
    }

    /**
     * 注册 redirect 服务
     */
    protected function redirect()
    {
        $this->container->singleton('redirect', function (IContainer $container) {
            $redirect = new Redirect($container['url']);

            if (isset($container['session'])) {
                $redirect->setSession($container['session']);
            }

            return $redirect;
        });
    }

    /**
     * 注册 response 服务
     */
    protected function response()
    {
        $this->container->singleton('response', function (IContainer $container) {
            $option = $container['option'];

            return (new ResponseFactory($container['view'], $container['redirect']))->
            setViewSuccessTemplate($option->get('view\\success'))->

            setViewFailTemplate($option->get('view\\fail'));
        });
    }

    /**
     * 注册 cookie 服务
     */
    protected function cookie()
    {
        $this->container->singleton('cookie', function (IContainer $container) {
            return new Cookie($container['option']->get('cookie\\'));
        });
    }

    /**
     * 注册 view 服务
     */
    protected function view()
    {
        $this->container->singleton('view', function (IContainer $container) {
            return new view($container['view.view']);
        });
    }

    /**
     * 设置 COOKIE Resolver.
     */
    protected function cookieResolver()
    {
        Response::setCookieResolver(function () {
            return $this->container['cookie'];
        });
    }
}
