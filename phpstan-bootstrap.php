<?php

declare(strict_types=1);

// phpstan 静态检查启动文件.
require_once __DIR__.'/tests/bootstrap.php';

// 导入助手函数.
$fnDirs = [
    __DIR__.'/src/Leevel/Support/Type',
    __DIR__.'/src/Leevel/Support/Arr',
    __DIR__.'/src/Leevel/Support/Str',
    __DIR__.'/src/Leevel/Filesystem/Helper',
    __DIR__.'/src/Leevel/Debug/Helper',
    __DIR__.'/src/Leevel/Encryption/Helper',
    __DIR__.'/src/Leevel/Kernel/Helper',
    __DIR__.'/src/Leevel/Validate/Helper',
];
foreach ($fnDirs as $dir) {
    foreach (glob($dir.'/*.php') as $file) {
        include_once $file;
    }
}

include_once __DIR__.'/src/Leevel/I18n/gettext.php';
