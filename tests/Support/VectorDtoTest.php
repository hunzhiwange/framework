<?php

declare(strict_types=1);

namespace Tests\Support;

use Leevel\Kernel\Utils\Api;
use Leevel\Support\VectorDto;
use Tests\Support\DemoProject\Template;
use Tests\Support\DemoProject\TemplateData;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => 'VectorDto 动态数组',
    'path' => 'component/collection/vectordto',
])]
final class VectorDtoTest extends TestCase
{
    #[Api([
        'zh-CN:title' => '基本使用',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Support\DemoProject\TemplateData::class)]}
```

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Support\DemoProject\Template::class)]}
```
EOT,
    ])]
    public function testBaseUse(): void
    {
        $templateData = VectorDto::fromRequest($sourceTemplateData = [
            [
                'title' => 'hello',
                'tag' => 'world',
                'description' => 'foo',
            ],
            [
                'title' => 'hello1',
                'tag' => 'world1',
                'description' => 'foo1',
            ],
        ], TemplateData::class);
        $data = [
            'key' => 'hello',
            'title' => 'world',
            'data' => $templateData,
        ];

        $collection = new Template($data);
        static::assertSame($collection['key'], 'hello');
        static::assertSame($collection['title'], 'world');
        static::assertInstanceOf(VectorDto::class, $collection['data']);
        static::assertSame($collection['data'], $templateData);
        static::assertSame($collection['data']->toArray(), $sourceTemplateData);
    }

    public function testError(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('The value of a collection value type requires the following types `Leevel\\Support\\Dto`');

        $data = [
            1, 'string', 3, 4,
        ];

        new VectorDto($data);
    }

    public function testError1(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Dto class not_found_class must be subclass of Leevel\\Support\\Dto');

        VectorDto::fromRequest([
            [
                'title' => 'hello',
                'tag' => 'world',
                'description' => 'foo',
            ],
            [
                'title' => 'hello1',
                'tag' => 'world1',
                'description' => 'foo1',
            ],
        ], 'not_found_class');
    }
}
