<?php

declare(strict_types=1);

$baseDir = dirname(__DIR__);

return [
    'app' => [
        'foo'   => 'bar',
        'hello' => 'world',
        'path'  => $baseDir.'/assertRelative/option/testdir/relativedir',
        ':env'  => [
            'ENVIRONMENT' => 'development',
            'DEBUG'       => 'true',
            'AUTH_KEY'    => '7becb888f518b20224a988906df51e05',
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
            'options' => [
            ],
            'i18ns' => [
            ],
            'i18n-paths' => [
            ],
            'metas' => [
            ],
        ],
    ],
];
