<?php

declare(strict_types=1);

// 读取配置并且返回配置值
require_once __DIR__.'/tests/config.php';

return [
    'paths' => [
        'migrations' => 'tests/assets/database/migrations',
        'seeds' => 'tests/assets/database/seeds',
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'production' => [
            'adapter' => 'mysql',
            'host' => '127.0.0.1',
            'name' => 'production_db',
            'user' => 'root',
            'pass' => '',
            'port' => 3306,
            'charset' => 'utf8',
        ],
        'development' => [
            'adapter' => 'mysql',
            'host' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['HOST'],
            'name' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['NAME'],
            'user' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['USER'],
            'pass' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PASSWORD'],
            'port' => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PORT'],
            'charset' => 'utf8',
        ],
        'testing' => [
            'adapter' => 'mysql',
            'host' => '127.0.0.1',
            'name' => 'test',
            'user' => 'root',
            'pass' => '',
            'port' => 3306,
            'charset' => 'utf8',
        ],
    ],
];
