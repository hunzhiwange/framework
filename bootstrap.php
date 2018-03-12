<?php
/*
 * This file is part of the ************************ package.
 * ##########################################################
 * #   ____                          ______  _   _ ______   #
 * #  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
 * # |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
 * #  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
 * #       \__   | \___ |_|    \__  || |    | | | || |      #
 * #     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
 * #                          |___ /  Since 2010.10.03      #
 * ##########################################################
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * 框架内部测试启动文件
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.05.09
 * @version 1.0
 */
error_reporting(-1);

ini_set('xdebug.max_nesting_level', 200);

ini_set('memory_limit', '512M');

$vendorDir = __DIR__ . '/../vendor';

if (false === is_file($vendorDir . '/autoload.php')) {
    throw new Exception('You must set up the project dependencies, run the following commands:
        wget http://getcomposer.org/composer.phar
        php composer.phar install');
} else {
    include $vendorDir . '/autoload.php';
}

spl_autoload_register(function($class) {
    if (0 === strpos($class, 'Tests\\')) {
        $path = __DIR__ . '/' . strtr(substr($class, 5), '\\', '/') . '.php';
        
        if (is_file($path) === true && is_readable($path) === true) {
            require_once $path;

            return true;
        }
    }
});
