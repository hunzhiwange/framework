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

namespace Leevel\Session\Provider;

use Leevel\Di\Provider;
use Leevel\Session\Manager;

/**
 * session 服务提供者.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.06.05
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
        $this->sessions();
        $this->session();
        $this->middleware();
    }

    /**
     * 可用服务提供者.
     *
     * @return array
     */
    public static function providers()
    {
        return [
            'sessions' => [
                'Leevel\\Session\\Manager',
            ],
            'session' => [
                'Leevel\\Session\\Session',
                'Leevel\\Session\\ISession',
            ],
            'Leevel\\Session\\Middleware\\Session',
        ];
    }

    /**
     * 注册 sessions 服务
     */
    protected function sessions()
    {
        $this->container->singleton('sessions', function ($project) {
            return new Manager($project);
        });
    }

    /**
     * 注册 session 服务
     */
    protected function session()
    {
        $this->container->singleton('session', function ($project) {
            return $project['sessions']->connect();
        });
    }

    /**
     * 注册 middleware 服务
     */
    protected function middleware()
    {
        $this->container->singleton('Leevel\\Session\\Middleware\\Session');
    }
}
