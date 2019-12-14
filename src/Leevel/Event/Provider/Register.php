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

namespace Leevel\Event\Provider;

use Leevel\Di\IContainer;
use Leevel\Di\Provider;
use Leevel\Event\Dispatch;
use Leevel\Event\IDispatch;

/**
 * event 服务提供者.
 */
class Register extends Provider
{
    /**
     * 注册服务.
     */
    public function register(): void
    {
        $this->container
            ->singleton(
                'event',
                fn (IContainer $container): Dispatch => new Dispatch($container),
            );
    }

    /**
     * 可用服务提供者.
     */
    public static function providers(): array
    {
        return [
            'event' => [IDispatch::class, Dispatch::class],
        ];
    }
}
