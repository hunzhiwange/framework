<?php

declare(strict_types=1);

namespace Tests\Docs;

use Leevel\Kernel\Utils\Api;

#[Api([
    'zh-CN:title' => '自动化测试',
    'path' => 'test/index',
    'zh-CN:description' => <<<'EOT'
QueryPHP 自身经过大量的单元测试用例验证过，取得了非常好的效果，对于业务层测试来说，我们也提供了基础的测试功能。
EOT,
])]
class TestDoc
{
    #[Api([
        'zh-CN:title' => '基本使用方法',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**tests/Example/ExampleTest.php**

``` php
{[file_get_contents('tests/Example/ExampleTest.php')]}
```
EOT,
        'lang' => 'shell',
    ])]
    public function doc1(): void
    {
    }
}
