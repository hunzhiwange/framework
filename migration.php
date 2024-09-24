<?php

declare(strict_types=1);

// 读取配置并且返回配置值
require_once __DIR__.'/tests/config.php';

return [
    'migrations_path' => 'tests/assets/database/migrations',
    'seeds_path' => 'tests/assets/database/seeds',
    'database' => 'default',
    'databases' => [
        'default' => 'mysql',
    ],
];
