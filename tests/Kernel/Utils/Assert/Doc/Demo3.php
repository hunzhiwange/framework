<?php

declare(strict_types=1);

namespace Tests\Kernel\Utils\Assert\Doc;

#[Api([
    'zh-CN:title' => 'demo2',
    'path' => 'demo3',
    'zh-CN:description' => <<<'EOT'
{['hello world' . ' php' . ' expression']}
EOT,
])]
class Demo3
{
}
