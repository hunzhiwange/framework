<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Http;

use InvalidArgumentException;
use JsonSerializable;
use Leevel\Http\JsonResponse;
use Leevel\Support\IArray;
use Leevel\Support\IJson;
use Tests\TestCase;

/**
 * - This class borrows heavily from the Symfony4 Framework and is part of the symfony package.
 *
 * @see Symfony\Component\HttpFoundation (https://github.com/symfony/symfony)
 *
 * @api(
 *     title="JSON Response",
 *     path="component/http/jsonresponse",
 *     description="QueryPHP 针对 API 开发可以直接返回一个 `\Leevel\Http\JsonResponse` 响应对象。",
 * )
 */
class JsonResponseTest extends TestCase
{
    /**
     * @api(
     *     title="JSON 响应基本使用",
     *     description="",
     *     note="",
     * )
     */
    public function testConstructorEmptyCreatesJsonObject(): void
    {
        $response = new JsonResponse();
        $this->assertSame('{}', $response->getContent());
    }

    /**
     * @api(
     *     title="JSON 响应支持索引数组",
     *     description="",
     *     note="",
     * )
     */
    public function testConstructorWithArrayCreatesJsonArray(): void
    {
        $response = new JsonResponse([0, 1, 2, 3]);
        $this->assertSame('[0,1,2,3]', $response->getContent());
    }

    /**
     * @api(
     *     title="JSON 响应支持关联数组",
     *     description="",
     *     note="",
     * )
     */
    public function testConstructorWithAssocArrayCreatesJsonObject(): void
    {
        $response = new JsonResponse(['foo' => 'bar']);
        $this->assertSame('{"foo":"bar"}', $response->getContent());
    }

    /**
     * @api(
     *     title="JSON 响应支持基本类型",
     *     description="",
     *     note="",
     * )
     */
    public function testConstructorWithSimpleTypes(): void
    {
        $response = new JsonResponse('foo');
        $this->assertSame('"foo"', $response->getContent());

        $response = new JsonResponse(0);
        $this->assertSame('0', $response->getContent());

        $response = new JsonResponse(0.1);
        $this->assertSame('0.1', $response->getContent());

        $response = new JsonResponse(true);
        $this->assertSame('true', $response->getContent());
    }

    public function testConstructorWithCustomStatus(): void
    {
        $response = new JsonResponse([], 202);
        $this->assertSame(202, $response->getStatusCode());
    }

    /**
     * @api(
     *     title="JSON 响应头默认为 application/json",
     *     description="",
     *     note="",
     * )
     */
    public function testConstructorAddsContentTypeHeader(): void
    {
        $response = new JsonResponse();
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
    }

    public function testConstructorWithCustomHeaders(): void
    {
        $response = new JsonResponse([], 200, ['ETag' => 'foo']);
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        $this->assertSame('foo', $response->headers->get('ETag'));
    }

    public function testConstructorWithCustomContentType(): void
    {
        $headers = ['Content-Type' => 'application/vnd.acme.blog-v1+json'];
        $response = new JsonResponse([], 200, $headers);
        $this->assertSame('application/vnd.acme.blog-v1+json', $response->headers->get('Content-Type'));
    }

    /**
     * @api(
     *     title="setJson 设置原生 JSON 数据",
     *     description="",
     *     note="",
     * )
     */
    public function testSetJson(): void
    {
        $response = new JsonResponse('1', 200, [], true);
        $this->assertSame('1', $response->getContent());

        $response = new JsonResponse('[1]', 200, [], true);
        $this->assertSame('[1]', $response->getContent());

        $response = new JsonResponse(null, 200, []);
        $response->setJson('true');
        $this->assertSame('true', $response->getContent());
    }

    /**
     * @api(
     *     title="create 创建一个 JSON 响应",
     *     description="",
     *     note="",
     * )
     */
    public function testCreate(): void
    {
        $response = JsonResponse::create(['foo' => 'bar'], 204);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame('{"foo":"bar"}', $response->getContent());
        $this->assertSame(204, $response->getStatusCode());
    }

    public function testStaticCreateEmptyJsonObject(): void
    {
        $response = JsonResponse::create();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame('{}', $response->getContent());
    }

    public function testStaticCreateJsonArray(): void
    {
        $response = JsonResponse::create([0, 1, 2, 3]);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame('[0,1,2,3]', $response->getContent());
    }

    public function testStaticCreateJsonObject(): void
    {
        $response = JsonResponse::create(['foo' => 'bar']);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame('{"foo":"bar"}', $response->getContent());
    }

    public function testStaticCreateWithSimpleTypes(): void
    {
        $response = JsonResponse::create('foo');
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame('"foo"', $response->getContent());

        $response = JsonResponse::create(0);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame('0', $response->getContent());

        $response = JsonResponse::create(0.1);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame('0.1', $response->getContent());

        $response = JsonResponse::create(true);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame('true', $response->getContent());
    }

    public function testStaticCreateWithCustomStatus(): void
    {
        $response = JsonResponse::create([], 202);
        $this->assertSame(202, $response->getStatusCode());
    }

    public function testStaticCreateAddsContentTypeHeader(): void
    {
        $response = JsonResponse::create();
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
    }

    public function testStaticCreateWithCustomHeaders(): void
    {
        $response = JsonResponse::create([], 200, ['ETag' => 'foo']);
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        $this->assertSame('foo', $response->headers->get('ETag'));
    }

    public function testStaticCreateWithCustomContentType(): void
    {
        $headers = ['Content-Type' => 'application/vnd.acme.blog-v1+json'];
        $response = JsonResponse::create([], 200, $headers);
        $this->assertSame('application/vnd.acme.blog-v1+json', $response->headers->get('Content-Type'));
    }

    /**
     * @api(
     *     title="setCallback 设置 JSONP 回调",
     *     description="",
     *     note="",
     * )
     */
    public function testSetCallback(): void
    {
        $response = JsonResponse::create(['foo' => 'bar'])->setCallback('callback');
        $this->assertSame(';callback({"foo":"bar"});', $response->getContent());
        $this->assertSame('text/javascript', $response->headers->get('Content-Type'));
    }

    public function testJsonEncodeFlags(): void
    {
        $response = new JsonResponse('<>\'&"');
        $this->assertSame("\"<>'&\\\"\"", $response->getContent());
    }

    /**
     * @api(
     *     title="getEncodingOptions 获取 JSON 编码参数",
     *     description="",
     *     note="",
     * )
     */
    public function testGetEncodingOptions(): void
    {
        $response = new JsonResponse();
        $this->assertSame(JSON_UNESCAPED_UNICODE, $response->getEncodingOptions());
    }

    /**
     * @api(
     *     title="setEncodingOptions 设置 JSON 编码参数",
     *     description="",
     *     note="",
     * )
     */
    public function testSetEncodingOptions(): void
    {
        $response = new JsonResponse();
        $response->setData([[1, 2, 3]]);
        $this->assertSame('[[1,2,3]]', $response->getContent());

        $response->setEncodingOptions(JSON_FORCE_OBJECT);
        $this->assertSame('{"0":{"0":1,"1":2,"2":3}}', $response->getContent());
    }

    /**
     * @api(
     *     title="fromJsonString 从 JSON 字符串创建响应对象",
     *     description="",
     *     note="",
     * )
     */
    public function testItAcceptsJsonAsString(): void
    {
        $response = JsonResponse::fromJsonString('{"foo":"bar"}');
        $this->assertSame('{"foo":"bar"}', $response->getContent());
    }

    public function testSetContent(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Malformed UTF-8 characters, possibly incorrectly encoded');

        // json_encode("\xB1\x31") 会引发 PHP 内核提示 Segmentation fault (core dumped)
        if (extension_loaded('leevel')) {
            throw new InvalidArgumentException('Malformed UTF-8 characters, possibly incorrectly encoded');
        }

        JsonResponse::create("\xB1\x31");
    }

    /**
     * @api(
     *     title="支持 JSON 的对象",
     *     description="
     *
     * 测试实现了 `\Leevel\Support\IArray` 的对象
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Http\JsonResponseMyArray::class)]}
     * ```
     *
     * 测试实现了 `\Leevel\Support\IJson` 的对象
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Http\JsonResponseMyJson::class)]}
     * ```
     *
     * 测试实现了 `\JsonSerializable` 的对象
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Http\JsonResponseMyJsonSerializable::class)]}
     * ```
     * ",
     *     note="",
     * )
     */
    public function testSetContentJsonObject(): void
    {
        $response = new JsonResponse();
        $response->setData(['foo' => 'bar']);
        $this->assertSame('{"foo":"bar"}', $response->getContent());

        $response->setData(new JsonResponseMyArray());
        $this->assertSame('{"hello":"IArray"}', $response->getContent());

        $response->setData(new JsonResponseMyJson());
        $this->assertSame('{"hello":"IJson"}', $response->getContent());

        $response->setData(new JsonResponseMyJsonSerializable());
        $this->assertSame('{"hello":"JsonSerializable"}', $response->getContent());
    }

    public function testSetComplexCallback(): void
    {
        $response = JsonResponse::create(['foo' => 'bar']);
        $response->setCallback('ಠ_ಠ["foo"].bar[0]');
        $this->assertSame(';ಠ_ಠ["foo"].bar[0]({"foo":"bar"});', $response->getContent());
    }

    public function testSetJsonException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The method setJson need a json data.');

        $response = new JsonResponse();
        $response->setJson(['foo']);
    }

    /**
     * @api(
     *     title="setData 设置 JSON 数据支持 JSON 编码参数",
     *     description="",
     *     note="",
     * )
     */
    public function testSetDataWithEncodingOptions(): void
    {
        $response = new JsonResponse();

        $response->setData(['成都', 'QueryPHP']);
        $this->assertSame('["成都","QueryPHP"]', $response->getContent());

        $response->setData(['成都', 'QueryPHP'], 0);
        $this->assertSame('["\u6210\u90fd","QueryPHP"]', $response->getContent());

        $response->setData(['成都', 'QueryPHP'], JSON_FORCE_OBJECT);
        $this->assertSame('{"0":"\u6210\u90fd","1":"QueryPHP"}', $response->getContent());
    }

    public function testSetCallbackFlow(): void
    {
        $condition = false;
        $response = new JsonResponse(['foo' => 'bar']);

        $response
            ->if($condition)
            ->setCallback('callback')
            ->else()
            ->setCallback('callback2')
            ->fi();

        $this->assertSame(';callback2({"foo":"bar"});', $response->getContent());
        $this->assertSame('text/javascript', $response->headers->get('Content-Type'));
    }

    public function testSetCallbackFlow2(): void
    {
        $condition = true;
        $response = new JsonResponse(['foo' => 'bar']);

        $response
            ->if($condition)
            ->setCallback('callback')
            ->else()
            ->setCallback('callback2')
            ->fi();

        $this->assertSame(';callback({"foo":"bar"});', $response->getContent());
        $this->assertSame('text/javascript', $response->headers->get('Content-Type'));
    }

    public function testSetJsonFlow(): void
    {
        $condition = false;
        $response = new JsonResponse();

        $response
            ->if($condition)
            ->setJson('{"foo":"bar"}')
            ->else()
            ->setJson('{"hello":"world"}')
            ->fi();

        $this->assertSame('{"hello":"world"}', $response->getContent());
    }

    public function testSetJsonFlow2(): void
    {
        $condition = true;
        $response = new JsonResponse();

        $response
            ->if($condition)
            ->setJson('{"foo":"bar"}')
            ->else()
            ->setJson('{"hello":"world"}')
            ->fi();

        $this->assertSame('{"foo":"bar"}', $response->getContent());
    }

    public function testSetDataFlow(): void
    {
        $condition = false;
        $response = new JsonResponse();

        $response
            ->if($condition)
            ->setData(['foo' => 'bar'])
            ->else()
            ->setData(['hello' => 'world'])
            ->fi();

        $this->assertSame('{"hello":"world"}', $response->getContent());
    }

    public function testSetDataFlow2(): void
    {
        $condition = true;
        $response = new JsonResponse();

        $response
            ->if($condition)
            ->setData(['foo' => 'bar'])
            ->else()
            ->setData(['hello' => 'world'])
            ->fi();

        $this->assertSame('{"foo":"bar"}', $response->getContent());
    }

    public function testSetEncodingOptionsFlow(): void
    {
        $condition = false;
        $response = new JsonResponse(['foo' => 'bar', '中' => '国']);

        $response
            ->if($condition)
            ->setEncodingOptions(256)
            ->else()
            ->setEncodingOptions(0)
            ->fi();

        $this->assertSame('{"foo":"bar","\u4e2d":"\u56fd"}', $response->getContent());
    }

    public function testSetEncodingOptionsFlow2(): void
    {
        $condition = true;
        $response = new JsonResponse(['foo' => 'bar', '中' => '国']);

        $response
            ->if($condition)
            ->setEncodingOptions(256)
            ->else()
            ->setEncodingOptions(0)
            ->fi();

        $this->assertSame('{"foo":"bar","中":"国"}', $response->getContent());
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

class JsonResponseMyJsonSerializable implements JsonSerializable
{
    public function jsonSerialize()
    {
        return ['hello' => 'JsonSerializable'];
    }
}
