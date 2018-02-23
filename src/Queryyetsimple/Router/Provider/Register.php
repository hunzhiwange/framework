<?php
/*
 * This file is part of the ************************ package.
 * ##########################################################
 * #   ____                          ______  _   _ ______   #
 * #  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
 * # |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
 * #  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
 * #       \__   | \___ |_|    \__  || |    | | | || |      #
 * #     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
 * #                          |___ /  Since 2010.10.03      #
 * ##########################################################
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
    Router\Router
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

            return (new Url($options))->

            setApp($router->app())->

            setController($router->controller())->

            setAction($router->action())->

            setUrlEnter($project['url_enter']);
        });
    }
}
