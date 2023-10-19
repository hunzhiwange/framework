<?php

declare(strict_types=1);

namespace Tests\Http;

use Leevel\Http\JsonResponse;
use Leevel\Kernel\Utils\Api;
use Leevel\Support\IArray;
use Leevel\Support\IJson;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => 'JSON Response',
    'path' => 'component/http/jsonresponse',
    'zh-CN:description' => <<<'EOT'
QueryPHP 针对 API 开发可以直接返回一个 `\Leevel\Http\JsonResponse` 响应对象。
EOT,
])]
final class JsonResponseTest extends TestCase
{
    #[Api([
        'zh-CN:title' => 'getEncodingOptions 获取 JSON 编码参数',
    ])]
    public function testGetEncodingOptions(): void
    {
        $response = new JsonResponse();
        static::assertSame(JSON_UNESCAPED_UNICODE, $response->getEncodingOptions());
    }

    #[Api([
        'zh-CN:title' => 'setData 设置 JSON 数据支持 JSON 编码参数',
    ])]
    public function testSetDataWithEncodingOptions(): void
    {
        $response = new JsonResponse();

        $response->setData(['成都', 'QueryPHP']);
        static::assertSame('["成都","QueryPHP"]', $response->getContent());

        $response->setEncodingOptions(0);
        $response->setData(['成都', 'QueryPHP']);
        static::assertSame('["\u6210\u90fd","QueryPHP"]', $response->getContent());

        $response->setEncodingOptions(JSON_FORCE_OBJECT);
        $response->setData(['成都', 'QueryPHP']);
        static::assertSame('{"0":"\u6210\u90fd","1":"QueryPHP"}', $response->getContent());
    }

    #[Api([
        'zh-CN:title' => '支持 JSON 的对象',
        'zh-CN:description' => <<<'EOT'
测试实现了 `\Leevel\Support\IArray` 的对象

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Http\JsonResponseMyArray::class)]}
```

测试实现了 `\Leevel\Support\IJson` 的对象

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Http\JsonResponseMyJson::class)]}
```

测试实现了 `\JsonSerializable` 的对象

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Http\JsonResponseMyJsonSerializable::class)]}
```
EOT,
    ])]
    public function testSetEncodingOptions(): void
    {
        $response = new JsonResponse();
        $response->setData(['foo' => 'bar']);
        static::assertSame('{"foo":"bar"}', $response->getContent());

        $response->setData(new JsonResponseMyArray());
        static::assertSame('{"hello":"IArray"}', $response->getContent());

        $response->setData(new JsonResponseMyJson());
        static::assertSame('{"hello":"IJson"}', $response->getContent());

        $response->setData(new JsonResponseMyJsonSerializable());
        static::assertSame('{"hello":"JsonSerializable"}', $response->getContent());
    }
}

class JsonResponseMyArray implements IArray
{
    public function toArray(): array
    {
        return ['hello' => 'IArray'];
    }
}

class JsonResponseMyJson implements IJson
{
    public function toJson(?int $option = null): string
    {
        if (null === $option) {
            $option = JSON_UNESCAPED_UNICODE;
        }

        return json_encode(['hello' => 'IJson'], $option);
    }
}

class JsonResponseMyJsonSerializable implements \JsonSerializable
{
    public function jsonSerialize(): mixed
    {
        return ['hello' => 'JsonSerializable'];
    }
}
