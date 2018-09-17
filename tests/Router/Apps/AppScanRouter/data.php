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

return [
    'base_paths' => [
    ],
    'group_paths' => [
        '/api/v1' => [
            'middlewares' => [
                'handle' => [
                    0 => 'Tests\\Router\\Middlewares\\Demo2@handle',
                ],
                'terminate' => [
                    0 => 'Tests\\Router\\Middlewares\\Demo1@terminate',
                    1 => 'Tests\\Router\\Middlewares\\Demo2@terminate',
                ],
            ],
        ],
        '/api/v2' => [
            'middlewares' => [
                'handle' => [
                    0 => 'Tests\\Router\\Middlewares\\Demo3@handle:10,world',
                ],
                'terminate' => [
                    0 => 'Tests\\Router\\Middlewares\\Demo1@terminate',
                ],
            ],
        ],
        '/api/v3' => [
            'middlewares' => [
                'handle' => [
                    0 => 'Tests\\Router\\Middlewares\\Demo2@handle',
                    1 => 'Tests\\Router\\Middlewares\\Demo3@handle:10,world',
                ],
                'terminate' => [
                    0 => 'Tests\\Router\\Middlewares\\Demo1@terminate',
                    1 => 'Tests\\Router\\Middlewares\\Demo2@terminate',
                ],
            ],
        ],
        '/api/v4' => [
            'middlewares' => [
            ],
        ],
    ],
    'groups' => [
        0 => '/pet',
        1 => '/store',
        2 => '/user',
    ],
    'routers' => [
        'get' => [
            'p' => [
                '/pet' => [
                    '/api/v1/petLeevel/{petId:[A-Za-z]+}/' => [
                        'bind' => 'AppScanRouter/Controllers/Pet/petLeevel',
                        'var'  => [
                            0 => 'petId',
                        ],
                    ],
                    'regex' => [
                        0 => '~^(?|/api/v1/petLeevel/([A-Za-z]+)/)$~x',
                    ],
                    'map' => [
                        0 => [
                            2 => '/api/v1/petLeevel/{petId:[A-Za-z]+}/',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
