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

use Leevel\Http\Response;
use Tests\TestCase;

/**
 * @api(
 *     title="HTTP Response",
 *     path="component/http/response",
 *     description="
 * QueryPHP 响应对象构建在 Symfony HttpFoundation 之上，增加了少量的功能。
 *
 * ::: warning 注意
 * 为了一致性或者更好与 Swoole 对接，请统一使用响应对象返回，框架会自动处理返回结果，请避免直接使用 `echo`、`die` 等中断后续处理。
 * :::
 * ",
 * )
 */
class ResponseTest extends TestCase
{
    /**
     * @api(
     *     title="setHeader 设置响应头",
     *     description="",
     *     note="",
     * )
     */
    public function testSetHeader(): void
    {
        $response = new Response();
        $response->setHeader('foo', 'bar');
        $this->assertSame('bar', $response->headers->get('foo'));
    }

    /**
     * @api(
     *     title="withHeaders 批量设置响应头",
     *     description="",
     *     note="",
     * )
     */
    public function testWithHeaders(): void
    {
        $response = new Response();
        $response->withHeaders(['foo' => 'bar']);
        $this->assertSame('bar', $response->headers->get('foo'));
    }

    /**
     * @api(
     *     title="setCookie 设置 COOKIE",
     *     description="",
     *     note="",
     * )
     */
    public function testSetCookie(): void
    {
        $response = new Response();
        $response->setCookie('foo', 'bar');
        $this->assertCount(1, $response->headers->getCookies());
    }

    /**
     * @api(
     *     title="withCookies 批量设置 COOKIE",
     *     description="",
     *     note="",
     * )
     */
    public function testWithCookies(): void
    {
        $response = new Response();
        $response->withCookies(['hello' => 'world']);
        $this->assertCount(1, $response->headers->getCookies());
    }
}

class StringableObject
{
    public function __toString()
    {
        return 'Foo';
    }
}
