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

use JsonSerializable;
use Leevel\Http\JsonResponse;
use Leevel\Support\IArray;
use Leevel\Support\IJson;
use Tests\TestCase;

/**
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

        $response->setEncodingOptions(0);
        $response->setData(['成都', 'QueryPHP']);
        $this->assertSame('["\u6210\u90fd","QueryPHP"]', $response->getContent());

        $response->setEncodingOptions(JSON_FORCE_OBJECT);
        $response->setData(['成都', 'QueryPHP']);
        $this->assertSame('{"0":"\u6210\u90fd","1":"QueryPHP"}', $response->getContent());
    }

    /**
     * @api(
     *     title="支持 JSON 的对象",
     *     description="
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
    public function testSetEncodingOptions(): void
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
