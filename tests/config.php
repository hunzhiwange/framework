<?php

declare(strict_types=1);

if (file_exists(__DIR__.'/config.local.php')) {
    require __DIR__.'/config.local.php';

    return;
}

$GLOBALS['LEEVEL_ENV']['DATABASE']['MYSQL'] = [
    'HOST'     => '127.0.0.1',
    'PORT'     => 3306,
    'NAME'     => 'test',
    'USER'     => 'root',
    'PASSWORD' => '',
];

$GLOBALS['LEEVEL_ENV']['CACHE']['REDIS'] = [
    'HOST'     => '127.0.0.1',
    'PORT'     => 6379,
    'PASSWORD' => '',
];

$GLOBALS['LEEVEL_ENV']['SESSION']['REDIS'] = [
    'HOST'     => '127.0.0.1',
    'PORT'     => 6379,
    'PASSWORD' => '',
];
