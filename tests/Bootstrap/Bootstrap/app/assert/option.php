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

return [
    'app' => [
        'environment' => 'development',
        'debug'       => false,
        '_env'        => [
            'environment'  => 'development',
            'debug'        => true,
            'app_auth_key' => '7becb888f518b20224a988906df51e05',
            'foo'          => null,
        ],
        '_deferred_providers' => [
            0 => [
            ],
            1 => [
            ],
        ],
        '_composer' => [
            'providers' => [
            ],
            'ignores' => [
            ],
            'commands' => [
            ],
            'options' => [
            ],
            'i18ns' => [
            ],
            'metas' => [
            ],
        ],
    ],
    'demo' => [
        'foo' => 'bar',
    ],
];
