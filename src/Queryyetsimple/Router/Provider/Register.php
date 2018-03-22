<?php
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
namespace Queryyetsimple\Router\Provider;

use Queryyetsimple\{
    Router\Url,
    Di\Provider,
    Http\Request,
    Http\Response,
    Router\Router,
    Router\Redirect,
    Router\ResponseFactory
};

/**
 * router 服务提供者
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.05.12
 * @version 1.0
 */
class Register extends Provider
{

    /**
     * 注册服务
     *
     * @return void
     */
    public function register()
    {
        $this->router();
        $this->url();
        $this->redirect();
        $this->request();
        $this->response();
        $this->cookieResolver();
    }

    /**
     * 可用服务提供者
     *
     * @return array
     */
    public static function providers()
    {
        return [
            'router' => [
                'Queryyetsimple\Router\Router',
                'Qys\Router\Router'
            ],
            'url' => [
                'Queryyetsimple\Router\Url',
                'Qys\Router\Url'
            ],
            'redirect' => [
                'Queryyetsimple\Router\Redirect',
                'Qys\Router\Redirect'
            ],
            'request' => [
                'Queryyetsimple\Http\Request',
                'Qys\Http\Request'
            ],
            'response' => [
                'Queryyetsimple\Router\IResponseFactory',
                'Queryyetsimple\Router\ResponseFactory',
                'Qys\Router\IResponseFactory',
                'Qys\Router\ResponseFactory'
            ]
        ];
    }

    /**
     * 注册 router 服务
     *
     * @return void
     */
    protected function router()
    {
        $this->singleton('router', function ($project) {
            $option = $project['option'];

            $options = [];
            foreach ([
                'default_app',
                'default_controller',
                'default_action',
                'middleware_group',
                'middleware_alias',
                'model',
                'router_cache',
                'router_strict',
                'router_domain_on',
                'router_domain_top',
                'make_subdomain_on',
                'pathinfo_restful',
                'args_protected',
                'args_regex',
                'args_strict',
                'middleware_strict',
                'method_strict',
                'controller_dir'
            ] as $item) {
                $options[$item] = $option->get($item);
            }

            $options['apps'] = $project->apps();

            return new Router($project, $project['request'], $options);
        });
    }

    /**
     * 注册 url 服务
     *
     * @return void
     */
    protected function url()
    {
        $this->singleton('url', function ($project) {
            $option = $project['option'];
            $router = $project['router'];

            $options = [];
            foreach ([
                'default_app',
                'default_controller',
                'default_action',
                'model',
                'html_suffix',
                'router_domain_top',
                'make_subdomain_on'
            ] as $item) {
                $options[$item] = $option->get($item);
            }

            return new Url($project['request'], $options);
        });
    }

    /**
     * 注册 redirect 服务
     *
     * @return void
     */
    protected function redirect()
    {
        $this->container['redirect'] = $this->share(function ($project) {
            $redirect = new Redirect($project['url']);

            if (isset($project['session'])) {
                $redirect->setSession($project['session']);
            }

            return $redirect;
        });
    }

    /**
     * 注册 request 服务
     *
     * @return void
     */
    protected function request()
    {
        $this->singleton('request', function ($project) {
            $option = $project['option'];

            return Request::createFromGlobals([
                'var_method' => $option['var_method'],
                'var_ajax' => $option['var_ajax'],
                'var_pjax' => $option['var_pjax'],
                'html_suffix' => $option['html_suffix'],
                'rewrite' => $option['rewrite'],
                'public' => $option['public']
            ]);
        });
    }

    /**
     * 注册 response 服务
     *
     * @return void
     */
    protected function response()
    {
        $this->singleton('response', function ($project) {
            $option = $project['option'];

            return (new ResponseFactory($project['view'], $project['redirect']))->

            setViewSuccessTemplate($option->get('view\action_success'))->

            setViewFailTemplate($option->get('view\action_fail'));
        });
    }

    /**
     * 设置 COOKIE Resolver
     *
     * @return void
     */
    protected function cookieResolver()
    {
        Response::setCookieResolver(function() {
            return $this->container['cookie'];
        });
    }
}
