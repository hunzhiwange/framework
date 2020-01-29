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

use DateTime;
use InvalidArgumentException;
use Leevel\Http\Response;
use ReflectionProperty;
use Tests\TestCase;

/**
 * - This class borrows heavily from the Symfony4 Framework and is part of the symfony package.
 *
 * @see Symfony\Component\HttpFoundation (https://github.com/symfony/symfony)
 *
 * @api(
 *     title="HTTP Response",
 *     path="component/http/response",
 *     description="
 * QueryPHP 响应对象构建在 Symfony HttpFoundation 之上，增加了少量的功能，早期基于其二次开发，后来重构掉二次开发的代码，但是保留了单元测试作为文档作为输出。
 *
 * ::: warning 注意
 * 不要尝试对第三方代码做单元测试，因为早期基于 Symfony HttpFoundation 二次开发，我们仅仅将单元测试保留下来作为文档输出。
 * :::
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
     *     title="create 创建一个响应对象",
     *     description="",
     *     note="",
     * )
     */
    public function testCreate(): void
    {
        $response = Response::create('foo', 301, ['Foo' => 'bar']);
        $this->assertInstanceOf('Leevel\\Http\\Response', $response);
        $this->assertInstanceOf('Leevel\\Http\\Response', $response);
        $this->assertSame(301, $response->getStatusCode());
        $this->assertSame('bar', $response->headers->get('foo'));
    }

    /**
     * @api(
     *     title="sendHeaders 发送响应头",
     *     description="",
     *     note="",
     * )
     */
    public function testSendHeaders(): void
    {
        $response = new Response();
        $headers = $response->sendHeaders();

        $this->assertObjectHasAttribute('headers', $headers);
        $this->assertObjectHasAttribute('content', $headers);
        $this->assertObjectHasAttribute('version', $headers);
        $this->assertObjectHasAttribute('statusCode', $headers);
        $this->assertObjectHasAttribute('statusText', $headers);
        $this->assertObjectHasAttribute('charset', $headers);
    }

    /**
     * @api(
     *     title="send 发送 HTTP 响应",
     *     description="",
     *     note="",
     * )
     */
    public function testSend(): void
    {
        $response = new Response();
        $responseSend = $response->send();
        $this->assertObjectHasAttribute('headers', $responseSend);
        $this->assertObjectHasAttribute('content', $responseSend);
        $this->assertObjectHasAttribute('version', $responseSend);
        $this->assertObjectHasAttribute('statusCode', $responseSend);
        $this->assertObjectHasAttribute('statusText', $responseSend);
        $this->assertObjectHasAttribute('charset', $responseSend);
    }

    /**
     * @api(
     *     title="setCharset.getCharset 设置和获取编码",
     *     description="",
     *     note="",
     * )
     */
    public function testGetCharset(): void
    {
        $response = new Response();
        $charsetOrigin = 'UTF-8';
        $response->setCharset($charsetOrigin);
        $charset = $response->getCharset();
        $this->assertSame($charsetOrigin, $charset);
    }

    /**
     * @api(
     *     title="setNotModified 设置响应未修改",
     *     description="",
     *     note="",
     * )
     */
    public function testSetNotModified(): void
    {
        $response = new Response();
        $modified = $response->setNotModified();
        $this->assertObjectHasAttribute('headers', $modified);
        $this->assertObjectHasAttribute('content', $modified);
        $this->assertObjectHasAttribute('version', $modified);
        $this->assertObjectHasAttribute('statusCode', $modified);
        $this->assertObjectHasAttribute('statusText', $modified);
        $this->assertObjectHasAttribute('charset', $modified);
        $this->assertSame(304, $modified->getStatusCode());
    }

    /**
     * @api(
     *     title="isSuccessful 是否为正确响应",
     *     description="",
     *     note="",
     * )
     */
    public function testIsSuccessful(): void
    {
        $response = new Response();
        $this->assertTrue($response->isSuccessful());
    }

    /**
     * @api(
     *     title="getProtocolVersion 获取 HTTP 协议版本",
     *     description="",
     *     note="",
     * )
     */
    public function testGetSetProtocolVersion(): void
    {
        $response = new Response();
        $this->assertSame('1.0', $response->getProtocolVersion());
        $response->setProtocolVersion('1.1');
        $this->assertSame('1.1', $response->getProtocolVersion());
    }

    /**
     * @api(
     *     title="获取响应头 Content-Type",
     *     description="",
     *     note="",
     * )
     */
    public function testContentTypeCharset(): void
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/css');
        $this->assertSame('text/css', $response->headers->get('Content-Type'));
        $response->headers->remove('Content-Type');
        $this->assertNull($response->headers->get('Content-Type'));
    }

    /**
     * @api(
     *     title="设置响应缓存",
     *     description="",
     *     note="",
     * )
     */
    public function testSetCache(): void
    {
        $response = new Response();

        $response->setCache(['etag' => 'hello']);
        $this->assertTrue($response->headers->has('etag'));
        $this->assertSame($response->headers->get('etag'), '"hello"');

        $response->setExpires(null);
        $this->assertNull($response->headers->get('Expires'), '->setExpires() remove the header when passed null');

        $response->setEtag('hello-world-etag');
        $this->assertSame($response->headers->get('Etag'), '"hello-world-etag"');

        $date = new DateTime();
        $date->modify('+'. 5 .'minutes');

        $response->setLastModified($date);
        $this->assertTrue($response->headers->has('Last-Modified'));

        $response->setLastModified(null);
        $this->assertNull($response->headers->get('Last-Modified'), '->setLastModified() remove the header when passed null');
    }

    /**
     * @api(
     *     title="sendContent 发送响应内容",
     *     description="",
     *     note="",
     * )
     */
    public function testSendContent(): void
    {
        $response = new Response('test response rendering', 200);
        ob_start();
        $response->sendContent();
        $string = ob_get_clean();
        $this->assertStringContainsString('test response rendering', $string);
    }

    /**
     * @api(
     *     title="isInvalid 响应是否为无效的",
     *     description="",
     *     note="",
     * )
     */
    public function testIsInvalid(): void
    {
        $response = new Response();

        try {
            $response->setStatusCode(99);
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertTrue($response->isInvalid());
        }

        try {
            $response->setStatusCode(650);
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertTrue($response->isInvalid());
        }

        $response = new Response('', 200);
        $this->assertFalse($response->isInvalid());
    }

    /**
     * @dataProvider getStatusCodeFixtures
     *
     * @param mixed $code
     * @param mixed $text
     * @param mixed $expectedText
     *
     * @api(
     *     title="setStatusCode 设置响应状态码",
     *     description="
     * **测试的响应状态**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Http\ResponseTest::class, 'getStatusCodeFixtures')]}
     * ```
     * ",
     *     note="",
     * )
     */
    public function testSetStatusCode($code, $text, $expectedText): void
    {
        $response = new Response();
        $response->setStatusCode((int) $code, $text);
        $statusText = new ReflectionProperty($response, 'statusText');
        $statusText->setAccessible(true);
        $this->assertSame($expectedText, $statusText->getValue($response));
    }

    public function getStatusCodeFixtures()
    {
        return [
            ['200', null, 'OK'],
            ['200', '', ''],
            ['200', 'foo', 'foo'],
            ['199', null, 'unknown status'],
            ['199', '0', '0'],
            ['199', 'foo', 'foo'],
        ];
    }

    /**
     * @api(
     *     title="isInformational 是否为信息性响应",
     *     description="",
     *     note="",
     * )
     */
    public function testIsInformational(): void
    {
        $response = new Response('', 100);
        $this->assertTrue($response->isInformational());
        $response = new Response('', 200);
        $this->assertFalse($response->isInformational());
    }

    /**
     * @api(
     *     title="isRedirection.isRedirect 是否为重定向响应（包括表单重定向）",
     *     description="",
     *     note="",
     * )
     */
    public function testIsRedirectRedirection(): void
    {
        foreach ([301, 302, 303, 307] as $code) {
            $response = new Response('', $code);
            $this->assertTrue($response->isRedirection());
            $this->assertTrue($response->isRedirect());
        }

        $response = new Response('', 304);
        $this->assertTrue($response->isRedirection());
        $this->assertFalse($response->isRedirect());

        $response = new Response('', 200);
        $this->assertFalse($response->isRedirection());
        $this->assertFalse($response->isRedirect());

        $response = new Response('', 404);
        $this->assertFalse($response->isRedirection());
        $this->assertFalse($response->isRedirect());

        $response = new Response('', 301, ['Location' => '/good-uri']);
        $this->assertFalse($response->isRedirect('/bad-uri'));
        $this->assertTrue($response->isRedirect('/good-uri'));
    }

    /**
     * @api(
     *     title="isNotFound 是否为 404 NOT FOUND",
     *     description="",
     *     note="",
     * )
     */
    public function testIsNotFound(): void
    {
        $response = new Response('', 404);
        $this->assertTrue($response->isNotFound());
        $response = new Response('', 200);
        $this->assertFalse($response->isNotFound());
    }

    /**
     * @api(
     *     title="isEmpty 是否为空响应",
     *     description="",
     *     note="",
     * )
     */
    public function testIsEmpty(): void
    {
        foreach ([204, 304] as $code) {
            $response = new Response('', $code);
            $this->assertTrue($response->isEmpty());
        }
        $response = new Response('', 200);
        $this->assertFalse($response->isEmpty());
    }

    /**
     * @api(
     *     title="isForbidden 是否为受限响应",
     *     description="",
     *     note="",
     * )
     */
    public function testIsForbidden(): void
    {
        $response = new Response('', 403);
        $this->assertTrue($response->isForbidden());
        $response = new Response('', 200);
        $this->assertFalse($response->isForbidden());
    }

    /**
     * @api(
     *     title="isOk 是否为正常响应",
     *     description="",
     *     note="",
     * )
     */
    public function testIsOk(): void
    {
        $response = new Response('', 200);
        $this->assertTrue($response->isOk());
        $response = new Response('', 404);
        $this->assertFalse($response->isOk());
    }

    /**
     * @api(
     *     title="isClientError.isServerError 否为客户端或者服务端错误响应",
     *     description="",
     *     note="",
     * )
     */
    public function testIsServerOrClientError(): void
    {
        $response = new Response('', 404);
        $this->assertTrue($response->isClientError());
        $this->assertFalse($response->isServerError());
        $response = new Response('', 500);
        $this->assertFalse($response->isClientError());
        $this->assertTrue($response->isServerError());
    }

    /**
     * @dataProvider validContentProvider
     *
     * @param mixed $content
     */
    public function testSetContent($content): void
    {
        $response = new Response();
        $response->setContent($content);
        $this->assertSame((string) $content, $response->getContent());
    }

    public function validContentProvider(): array
    {
        return [
            'obj'    => [new StringableObject()],
            'string' => ['Foo'],
            'int'    => [2],
        ];
    }

    /**
     * @dataProvider invalidContentProvider
     *
     * @param mixed $content
     */
    public function testSetContentInvalid($content): void
    {
        $this->expectException(\UnexpectedValueException::class);

        $response = new Response();
        $response->setContent($content);
    }

    public function invalidContentProvider(): array
    {
        return [
            'obj' => [new \stdClass()],
        ];
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
     *     title="setCookie.withCookies 设置 COOKIE",
     *     description="",
     *     note="",
     * )
     */
    public function testCookie(): void
    {
        $response = new Response();
        $response->setCookie('foo', 'bar');
        $response->withCookies(['hello' => 'world']);

        $this->assertCount(2, $response->headers->getCookies());
    }

    public function testGetContent(): void
    {
        $response = new Response();
        $response->setContent('hello');

        $this->assertSame('hello', $response->getContent());
        $this->assertTrue($response->isOk());
    }

    /**
     * @api(
     *     title="getStatusCode 获取状态码",
     *     description="",
     *     note="",
     * )
     */
    public function testGetStatusCode(): void
    {
        $response = new Response();
        $response->setContent('hello');

        $this->assertSame(200, $response->getStatusCode());
    }
}

class StringableObject
{
    public function __toString()
    {
        return 'Foo';
    }
}
