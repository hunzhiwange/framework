<?php

declare(strict_types=1);

namespace Tests\Database;

use Leevel\Kernel\Utils\Api;

#[Api([
    'title' => 'Summary',
    'zh-CN:title' => '概述',
    'zh-TW:title' => '概述',
    'path' => 'database/index',
    'description' => <<<'EOT'
QueryPHP database 非常强大.
EOT,
    'zh-CN:description' => <<<'EOT'
QueryPHP 数据库非常强大
EOT,
    'zh-TW:description' => <<<'EOT'
QueryPHP 資料庫非常强大
EOT,
])]
class SummaryDoc
{
}
