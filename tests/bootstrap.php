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

/*
 * 框架内部测试启动文件
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.05.09
 * @version 1.0
 */
error_reporting(-1);

ini_set('xdebug.max_nesting_level', '200');

ini_set('memory_limit', '512M');

$vendorDir = __DIR__.'/../vendor';

require_once __DIR__.'/function.php';

if (false === is_file($vendorDir.'/autoload.php')) {
    throw new Exception('You must set up the project dependencies, run the following commands:
        wget http://getcomposer.org/composer.phar
        php composer.phar install');
}

include $vendorDir.'/autoload.php';

spl_autoload_register(function ($class) {
    if (0 === stripos($class, 'Tests\\')) {
        $path = __DIR__.'/'.strtr(substr($class, 5), '\\', '/').'.php';

        if (true === is_file($path) && true === is_readable($path)) {
            require_once $path;

            return true;
        }
    }
});
