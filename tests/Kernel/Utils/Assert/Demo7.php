<?php

declare(strict_types=1);

namespace Tests\Kernel\Utils\Assert;

use Leevel\Kernel\App;
use Leevel\Kernel\Utils\Api;

#[Api([
    'zh-CN:title' => 'demo7',
    'path' => 'demo7',
    'zh-CN:description' => <<<'EOT'
demo doc
just test
EOT,
])]
class Demo7
{
    #[Api([
        'zh-CN:title' => 'title1',
        'zh-CN:description' => <<<'EOT'
hello
world
EOT,
        'note' => <<<'EOT'
note
EOT,
    ])]
    public function doc1(): string
    {
        return App::class;
    }
}
