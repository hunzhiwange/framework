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

use Leevel\Di\Provider;
use Leevel\Http\Request;
use Leevel\Http\Response;
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
     * 注册服务
     */
    public function register()
    {
        $this->router();
        $this->url();
        $this->redirect();
        $this->response();
        $this->cookieResolver();
    }

    /**
     * 可用服务提供者.
     *
     * @return array
     */
    public static function providers()
    {
        return [
            'router' => [
                'Leevel\Router\Router',
            ],
            'url' => [
                'Leevel\Router\Url',
            ],
            'redirect' => [
                'Leevel\Router\Redirect',
            ],
            'response' => [
                'Leevel\Router\IResponseFactory',
                'Leevel\Router\ResponseFactory',
            ],
        ];
    }

    /**
     * 注册 router 服务
     */
    protected function router()
    {
        $this->container->singleton('router', function ($project) {
            return new Router($project);
        });
    }

    /**
     * 注册 url 服务
     */
    protected function url()
    {
        $this->container->singleton('url', function ($project) {
            $option = $project['option'];
            $router = $project['router'];

            $options = [];
            foreach ([
                'with_suffix',
                'html_suffix',
                'domain_top',
                'subdomain_on',
            ] as $item) {
                $options[$item] = $option->get($item);
            }

            return new Url($project['request'], $options);
        });
    }

    /**
     * 注册 redirect 服务
     */
    protected function redirect()
    {
        $this->container['redirect'] = $this->container->share(function ($project) {
            $redirect = new Redirect($project['url']);

            if (isset($project['session'])) {
                $redirect->setSession($project['session']);
            }

            return $redirect;
        });
    }

    /**
     * 注册 response 服务
     */
    protected function response()
    {
        $this->container->singleton('response', function ($project) {
            $option = $project['option'];

            return (new ResponseFactory($project['view'], $project['redirect']))->
            setViewSuccessTemplate($option->get('view\action_success'))->

            setViewFailTemplate($option->get('view\action_fail'));
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
