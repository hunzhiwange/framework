<?php

declare(strict_types=1);

namespace Tests\Kernel\Utils\Assert\Doc;

#[Api([
    'zh-CN:title' => 'demo1',
    'path' => 'demo1',
    'zh-CN:description' => <<<'EOT'
demo doc
just test
EOT,
])]
class Demo1
{
    #[Api([
        'zh-CN:title' => 'title',
        'zh-CN:description' => <<<'EOT'
hello
world
EOT,
    ])]
    public function test1(): void
    {
    }
}
