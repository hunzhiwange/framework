<?php

declare(strict_types=1);

return [
    'app' => [
        'environment' => 'development',
        'debug' => false,
        ':env' => [
            'environment' => 'development',
            'debug' => true,
            'app_auth_key' => '7becb888f518b20224a988906df51e05',
            'foo' => null,
        ],
        ':deferred_providers' => [
            0 => [
            ],
            1 => [
            ],
        ],
        ':composer' => [
            'providers' => [
            ],
            'ignores' => [
            ],
            'commands' => [
            ],
            'configs' => [
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
