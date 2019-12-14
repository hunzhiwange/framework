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

namespace Tests\Option\Providers;

use Leevel\Di\IContainer;
use Leevel\Di\Provider;

/**
 * bar 服务提供者.
 */
class Bar extends Provider
{
    public function register(): void
    {
        $this->container->singleton('bar', function (IContainer $container) {
            return 'foo';
        });
    }

    public static function providers(): array
    {
        return [
            'bar' => [
                'Tests\\Option\\Providers\\World',
            ],
            'helloworld',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function isDeferred(): bool
    {
        return true;
    }
}
