<?php

declare(strict_types=1);

namespace Tests\Kernel\Utils\Assert\Doc;

use Leevel\Kernel\Utils\Api;

#[Api([
    'zh-CN:title' => 'demo4',
    'path' => 'demo4',
    'zh-CN:description' => <<<'EOT'
demo doc
just test
EOT,
])]
class Demo4
{
    #[Api([
        'zh-CN:title' => 'title1',
        'zh-CN:description' => <<<'EOT'
hello
world
EOT,
    ])]
    public function doc1(): void
    {
    }

    public function __doc2(): void
    {
    }

    protected function doc3(): void
    {
    }
}
