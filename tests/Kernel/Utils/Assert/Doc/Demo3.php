<?php

declare(strict_types=1);

namespace Tests\Kernel\Utils\Assert\Doc;

use Leevel\Kernel\Utils\Api;

#[Api([
    'zh-CN:title' => 'demo3',
    'path' => 'demo3',
    'zh-CN:description' => <<<'EOT'
{['hello world' . ' php' . ' expression']}
EOT,
])]
class Demo3
{
}
