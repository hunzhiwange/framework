<?php

declare(strict_types=1);

namespace Tests\Database\Ddd;

use Leevel\Kernel\Utils\Api;

#[Api([
    'title' => 'Summary',
    'zh-CN:title' => '概述',
    'zh-TW:title' => '概述',
    'path' => 'orm/index',
    'description' => <<<'EOT'
QueryPHP ORM 非常强大。
EOT,
    'zh-CN:description' => <<<'EOT'
QueryPHP ORM 非常强大
EOT,
    'zh-TW:description' => <<<'EOT'
QueryPHP ORM 非常强大
EOT,
])]
class SummaryDoc
{
}
