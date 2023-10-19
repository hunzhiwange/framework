<?php

declare(strict_types=1);

namespace Tests\Docs;

#[Api([
    'zh-CN:title' => '开发指南',
    'path' => 'guide/index',
    'zh-CN:description' => <<<'EOT'
这里将为大家讲解 QueryPHP 的基本开发问题，后续待完善。
EOT,
])]
class GuideDoc
{
}
