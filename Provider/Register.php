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
namespace Queryyetsimple\Session\Provider;

use Queryyetsimple\{
    Di\Provider,
    Session\Manager
};

/**
 * session 服务提供者
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.06.05
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
        $this->sessions();
        $this->session();
        $this->middleware();
    }

    /**
     * 可用服务提供者
     *
     * @return array
     */
    public static function providers()
    {
        return [
            'sessions' => [
                'Queryyetsimple\Session\Manager',
                'Qys\Session\Manager'
            ],
            'session' => [
                'Queryyetsimple\Session\Session',
                'Queryyetsimple\Session\ISession',
                'Qys\Session\Session',
                'Qys\Session\ISession'
            ],
            'Queryyetsimple\Session\Middleware\Session'
        ];
    }

    /**
     * 注册 sessions 服务
     *
     * @return void
     */
    protected function sessions()
    {
        $this->singleton('sessions', function ($project) {
            return new Manager($project);
        });
    }

    /**
     * 注册 session 服务
     *
     * @return void
     */
    protected function session()
    {
        $this->singleton('session', function ($project) {
            return $project['sessions']->connect();
        });
    }

    /**
     * 注册 middleware 服务
     *
     * @return void
     */
    protected function middleware()
    {
        $this->singleton('Queryyetsimple\Session\Middleware\Session');
    }
}
