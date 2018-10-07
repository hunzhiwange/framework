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
  * 配置
  *
  * @author Xiangmin Liu <635750556@qq.com>
  *
  * @since 2018.10.27
  *
  * @version 1.0
  */

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
