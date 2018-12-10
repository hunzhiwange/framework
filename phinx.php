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

/**
 * ---------------------------------------------------------------
 * 读取配置
 * ---------------------------------------------------------------.
 *
 * 读取配置并且返回配置值
 */
require_once __DIR__.'/tests/config.php'; // @codeCoverageIgnore

// @codeCoverageIgnoreStart
return [
    'paths' => [
        'migrations'    => 'tests/assert/database/migrations',
        'seeds'         => 'tests/assert/database/seeds',
    ],
    'environments'   => [
        'defaut_migration_table'    => 'phinxlog',
        'default_database'          => 'development',
        'production'                => [
            'adapter'   => 'mysql',
            'host'      => '127.0.0.1',
            'name'      => 'production_db',
            'user'      => 'root',
            'pass'      => '',
            'port'      => 3306,
            'charset'   => 'utf8',
        ],
        'development'   => [
            'adapter'   => 'mysql',
            'host'      => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['HOST'],
            'name'      => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['NAME'],
            'user'      => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['USER'],
            'pass'      => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PASSWORD'],
            'port'      => $GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL']['PORT'],
            'charset'   => 'utf8',
        ],
        'testing'   => [
            'adapter'   => 'mysql',
            'host'      => '127.0.0.1',
            'name'      => 'test',
            'user'      => 'root',
            'pass'      => '',
            'port'      => 3306,
            'charset'   => 'utf8',
        ],
    ],
];
// @codeCoverageIgnoreEnd
