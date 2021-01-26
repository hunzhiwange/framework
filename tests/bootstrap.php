<?php

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('xdebug.max_nesting_level', '200');
ini_set('memory_limit', '512M');

$vendorDir = __DIR__.'/../vendor';
require_once __DIR__.'/function.php';
require_once __DIR__.'/config.php';

if (false === is_file($vendorDir.'/autoload.php')) {
    throw new Exception('You must set up the app dependencies, run the following commands:
        wget http://getcomposer.org/composer.phar
        php composer.phar install');
}

include $vendorDir.'/autoload.php';
