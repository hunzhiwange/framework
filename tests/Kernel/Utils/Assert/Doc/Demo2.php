<?php

declare(strict_types=1);

namespace Tests\Kernel\Utils\Assert\Doc;

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

            /*
             * This file is part of the your app package.
             *
             * The PHP Application For Code Poem For You.
             * (c) 2018-2099 http://yourdomian.com All rights reserved.
             *
             * For the full copyright and license information, please view the LICENSE
             * file that was distributed with this source code.
             */

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
