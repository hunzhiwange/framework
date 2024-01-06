<?php

declare(strict_types=1);

namespace Tests\Kernel\Utils\Assert\Doc;

use Leevel\Kernel\Utils\Api;

#[Api([
    'zh-CN:title' => 'demo2',
    'path' => 'demo2',
    'zh-CN:description' => <<<'EOT'
demo doc
just test
EOT,
])]
class Demo2
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

    #[Api([
        'zh-CN:title' => 'title2',
        'zh-CN:description' => <<<'EOT'
hello
world
EOT,
    ])]
    public function doc2(): void
    {
        <<<'EOT'
            <?php

            declare(strict_types=1);

            namespace Common;

            class Test
            {
                public function demo($a = 1, $b = 4)
                {
                    echo 1;
                }
            }
            EOT;
    }
}
