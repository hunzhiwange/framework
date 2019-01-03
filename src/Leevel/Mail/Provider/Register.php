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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Mail\Provider;

use Leevel\Di\IContainer;
use Leevel\Di\Provider;
use Leevel\Mail\Manager;

/**
 * mail 服务提供者.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.08.26
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
        $this->mails();
        $this->mail();
    }

    /**
     * 可用服务提供者.
     *
     * @return array
     */
    public static function providers(): array
    {
        return [
            'mails' => [
                'Leevel\\Mail\\Manager',
            ],
            'mail' => [
                'Leevel\\Mail\\Mail',
                'Leevel\\Mail\\IMail',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function isDeferred(): bool
    {
        return true;
    }

    /**
     * 注册 mails 服务
     */
    protected function mails(): void
    {
        $this->container->singleton('mails', function (IContainer $container) {
            return new Manager($container);
        });
    }

    /**
     * 注册 mail 服务
     */
    protected function mail(): void
    {
        $this->container->singleton('mail', function (IContainer $container) {
            return $container['mails']->connect();
        });
    }
}
