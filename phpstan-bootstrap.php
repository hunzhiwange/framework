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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * phpstan 静态检查启动文件.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.05.13
 *
 * @version 1.0
 */
require_once __DIR__.'/tests/bootstrap.php'; // @codeCoverageIgnore

/**
 * 导入助手函数.
 */
// @codeCoverageIgnoreStart
$fnDirs = [
    __DIR__.'/src/Leevel/Support/Type',
    __DIR__.'/src/Leevel/Support/Arr',
    __DIR__.'/src/Leevel/Support/Str',
    __DIR__.'/src/Leevel/Cache/Helper',
    __DIR__.'/src/Leevel/Filesystem/Fso',
    __DIR__.'/src/Leevel/Debug/Helper',
    __DIR__.'/src/Leevel/Encryption/Safe',
    __DIR__.'/src/Leevel/Encryption/Helper',
    __DIR__.'/src/Leevel/I18n/Helper',
    __DIR__.'/src/Leevel/Kernel/Helper',
    __DIR__.'/src/Leevel/Log/Helper',
    __DIR__.'/src/Leevel/Option/Helper',
    __DIR__.'/src/Leevel/Router/Helper',
    __DIR__.'/src/Leevel/Session/Helper',
    __DIR__.'/src/Leevel/Validate/Helper',
];

foreach ($fnDirs as $dir) {
    $files = glob($dir.'/*.php');

    foreach ($files as $file) {
        include_once $file;
    }
}
// @codeCoverageIgnoreEnd
