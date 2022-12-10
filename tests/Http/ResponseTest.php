<?php

declare(strict_types=1);

namespace Tests\Http;

use Leevel\Http\Response;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="HTTP Response",
 *     path="component/http/response",
 *     zh-CN:description="
 * QueryPHP 响应对象构建在 Symfony HttpFoundation 之上，增加了少量的功能。
 *
 * ::: warning 注意
 * 为了一致性或者更好与 RoadRunner 对接，请统一使用响应对象返回，框架会自动处理返回结果，请避免直接使用 `echo`、`die` 等中断后续处理。
 * :::
 * ",
 * )
 */
class ResponseTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="setHeader 设置响应头",
     *     zh-CN:description="",
     *     zh-CN:note="",
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
     *     zh-CN:title="withHeaders 批量设置响应头",
     *     zh-CN:description="",
     *     zh-CN:note="",
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
     *     zh-CN:title="setCookie 设置 COOKIE",
     *     zh-CN:description="",
     *     zh-CN:note="",
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
     *     zh-CN:title="withCookies 批量设置 COOKIE",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testWithCookies(): void
    {
        $response = new Response();
        $response->withCookies(['hello' => 'world']);
        $this->assertCount(1, $response->headers->getCookies());
    }

    public function testSetCookieButExpireIsInvalid(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cookie expire date must greater than or equal 0.');

        $response = new Response();
        $response->setCookie('foo', 'bar', ['expire' => -20]);
    }
}
