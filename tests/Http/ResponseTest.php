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
use DateTimeZone;
use InvalidArgumentException;
use JsonSerializable;
use Leevel\Http\Response;
use Leevel\Support\IArray;
use Leevel\Support\IJson;
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
 * QueryPHP 响应对象基于 Symfony 二次开发，功能非常强大，做了一些精简和优化。
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
        $this->assertObjectHasAttribute('protocolVersion', $headers);
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
        $this->assertObjectHasAttribute('protocolVersion', $responseSend);
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
        $this->assertObjectHasAttribute('protocolVersion', $modified);
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

        $response->setContentType('text/css');
        $this->assertSame('text/css', $response->headers->get('Content-Type'));
        $response->headers->remove('Content-Type');
        $this->assertNull($response->headers->get('Content-Type'));

        $response->setContentType('text/css', 'UTF-8');
        $this->assertSame('text/css; charset=UTF-8', $response->headers->get('Content-Type'));
        $response->headers->remove('Content-Type');
        $this->assertNull($response->headers->get('Content-Type'));

        $response->setCharset('GBK');
        $response->setContentType('text/css');
        $this->assertSame('text/css; charset=GBK', $response->headers->get('Content-Type'));
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

        $response->setCache(5);
        $this->assertTrue($response->headers->has('expires'));
        $this->assertSame($response->headers->get('cache-control'), 'max-age=300');

        $response->setExpires(null);
        $this->assertNull($response->headers->get('Expires'), '->setExpires() remove the header when passed null');

        $response->setEtag('hello-world-etag');
        $this->assertSame($response->headers->get('Etag'), 'hello-world-etag');

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
     *     title="setData 设置 JSON 数据",
     *     description="",
     *     note="",
     * )
     */
    public function testSetJsonData(): void
    {
        $response = new Response();
        $response->setData(['foo' => 'bar']);
        $this->assertSame('{"foo":"bar"}', $response->getContent());

        $response->setData(new MyArray());
        $this->assertSame('{"hello":"IArray"}', $response->getContent());

        $response->setData(new MyJson());
        $this->assertSame('{"hello":"IJson"}', $response->getContent());

        $response->setData(new MyJsonSerializable());
        $this->assertSame('{"hello":"JsonSerializable"}', $response->getContent());
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
     *     title="setContent 支持设置实现了 \Leevel\Support\IArray JSON 类数据",
     *     description="
     * 实现了 `\Leevel\Support\IArray` 的对象也会被转化为 JSON。
     *
     * 测试对象
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Http\MyArray::class)]}
     * ```
     * ",
     *     note="",
     * )
     */
    public function testSetContentAsJon(): void
    {
        $response = new Response();

        $response->setContent(new MyArray());

        $this->assertSame('{"hello":"IArray"}', $response->getContent());
        $this->assertTrue($response->isOk());
        $this->assertSame(['hello' => 'IArray'], $response->getData());
    }

    /**
     * @api(
     *     title="setContent 支持设置实现了 \Leevel\Support\IJson JSON 类数据",
     *     description="
     * 实现了 `\Leevel\Support\IJson` 的对象也会被转化为 JSON。
     *
     * 测试对象
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Http\MyJson::class)]}
     * ```
     * ",
     *     note="",
     * )
     */
    public function testSetContentAsJonWithIJson(): void
    {
        $response = new Response();

        $response->setContent(new MyJson());

        $this->assertSame('{"hello":"IJson"}', $response->getContent());
        $this->assertTrue($response->isOk());
        $this->assertSame(['hello' => 'IJson'], $response->getData());
    }

    /**
     * @api(
     *     title="appendContent 附加内容",
     *     description="",
     *     note="",
     * )
     */
    public function testAppendContent(): void
    {
        $response = new Response();

        $response->setContent('hello');

        $this->assertSame('hello', $response->getContent());
        $this->assertTrue($response->isOk());

        $response->appendContent('world');

        $this->assertSame('helloworld', $response->getContent());
        $this->assertTrue($response->isOk());
        $this->assertSame('helloworld', $response->getData());
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
     *     title="cookie.withCookies 设置 COOKIE",
     *     description="",
     *     note="",
     * )
     */
    public function testCookie(): void
    {
        $response = new Response();

        $allCookies = [
            'foo' => [
                'foo',
                'bar',
                time() + 86400,
                '/',
                '',
                false,
                false,
            ],
            'hello' => [
                'hello',
                'world',
                time() + 86400,
                '/',
                '',
                false,
                false,
            ],
        ];

        $response->cookie('foo', 'bar');
        $response->withCookies(['hello' => 'world']);

        $this->assertSame($allCookies, $response->getCookies());
    }

    public function testContent(): void
    {
        $response = new Response();
        $response->setContent('hello');

        $this->assertSame('hello', $response->content());
        $this->assertTrue($response->isOk());
    }

    /**
     * @api(
     *     title="getOriginal 获取原始内容",
     *     description="",
     *     note="",
     * )
     */
    public function testGetOriginal(): void
    {
        $response = new Response();
        $response->setContent('hello');

        $this->assertSame('hello', $response->content());
        $this->assertTrue($response->isOk());
        $this->assertSame('hello', $response->getOriginal());

        $response->setContent($myArr = new MyArray());

        $this->assertSame('{"hello":"IArray"}', $response->getContent());
        $this->assertTrue($response->isOk());
        $this->assertSame(['hello' => 'IArray'], $response->getData());
        $this->assertEquals($myArr, $response->getOriginal());
    }

    /**
     * @api(
     *     title="status 获取状态码",
     *     description="",
     *     note="",
     * )
     */
    public function testStatus(): void
    {
        $response = new Response();
        $response->setContent('hello');

        $this->assertSame(200, $response->status());
    }

    /**
     * @api(
     *     title="setContentLength 设置响应内容长度",
     *     description="",
     *     note="",
     * )
     */
    public function testSetContentLength(): void
    {
        $response = new Response();
        $response->setContentLength(100);

        $this->assertSame('100', $response->headers->get('Content-Length'));
    }

    /**
     * @api(
     *     title="isJson 响应是否为 JSON",
     *     description="",
     *     note="",
     * )
     */
    public function testIsJson(): void
    {
        $response = new Response();
        $response->setContent('foo');

        $this->assertFalse($response->isJson());

        $response->setContent(new MyArray());

        $this->assertTrue($response->isJson());
    }

    public function testSetContentFlow(): void
    {
        $condition = false;
        $response = new Response();

        $response
            ->if($condition)
            ->setContent('foo')
            ->else()
            ->setContent('bar')
            ->fi();

        $this->assertSame('bar', $response->getContent());
        $this->assertTrue($response->isOk());
    }

    public function testSetContentFlow2(): void
    {
        $condition = true;
        $response = new Response();

        $response
            ->if($condition)
            ->setContent('foo')
            ->else()
            ->setContent('bar')
            ->fi();

        $this->assertSame('foo', $response->getContent());
        $this->assertTrue($response->isOk());
    }

    public function testAppendContentFlow(): void
    {
        $condition = false;
        $response = new Response('hello');

        $response
            ->if($condition)
            ->appendContent('foo')
            ->else()
            ->appendContent('bar')
            ->fi();

        $this->assertSame('hellobar', $response->getContent());
        $this->assertTrue($response->isOk());
    }

    public function testAppendContentFlow2(): void
    {
        $condition = true;
        $response = new Response('hello');

        $response
            ->if($condition)
            ->appendContent('foo')
            ->else()
            ->appendContent('bar')
            ->fi();

        $this->assertSame('hellofoo', $response->getContent());
        $this->assertTrue($response->isOk());
    }

    public function testSetHeaderFlow(): void
    {
        $condition = false;
        $response = new Response('hello');

        $response
            ->if($condition)
            ->setHeader('foo', 'bar')
            ->else()
            ->setHeader('foo', 'bar2')
            ->fi();

        $this->assertSame('bar2', $response->headers->get('foo'));
        $this->assertTrue($response->isOk());
    }

    public function testSetHeaderFlow2(): void
    {
        $condition = true;
        $response = new Response('hello');

        $response
            ->if($condition)
            ->setHeader('foo', 'bar')
            ->else()
            ->setHeader('foo', 'bar2')
            ->fi();

        $this->assertSame('bar', $response->headers->get('foo'));
        $this->assertTrue($response->isOk());
    }

    public function testWithHeadersFlow(): void
    {
        $condition = false;
        $response = new Response('hello');

        $response
            ->if($condition)
            ->withHeaders(['foo' => 'bar'])
            ->else()
            ->withHeaders(['foo' => 'bar2'])
            ->fi();

        $this->assertSame('bar2', $response->headers->get('foo'));
        $this->assertTrue($response->isOk());
    }

    public function testWithHeadersFlow2(): void
    {
        $condition = true;
        $response = new Response('hello');

        $response
            ->if($condition)
            ->withHeaders(['foo' => 'bar'])
            ->else()
            ->withHeaders(['foo' => 'bar2'])
            ->fi();

        $this->assertSame('bar', $response->headers->get('foo'));
        $this->assertTrue($response->isOk());
    }

    public function testSetCookieFlow(): void
    {
        $condition = false;
        $response = new Response('hello');

        $response
            ->if($condition)
            ->setCookie('foo', 'bar')
            ->else()
            ->fi();

        $allCookies = [];

        $this->assertSame($allCookies, $response->getCookies());
    }

    public function testSetCookieFlow2(): void
    {
        $condition = true;
        $response = new Response('hello');

        $response
            ->if($condition)
            ->setCookie('foo', 'bar')
            ->else()
            ->fi();

        $allCookies = [
            'foo' => [
                'foo',
                'bar',
                time() + 86400,
                '/',
                '',
                false,
                false,
            ],
        ];

        $this->assertSame($allCookies, $response->getCookies());
    }

    public function testWithCookiesFlow(): void
    {
        $condition = false;
        $response = new Response('hello');

        $response
            ->if($condition)
            ->withCookies(['foo' => 'bar'])
            ->else()
            ->fi();

        $allCookies = [];

        $this->assertSame($allCookies, $response->getCookies());
    }

    public function testWithCookiesFlow2(): void
    {
        $condition = true;
        $response = new Response('hello');

        $response
            ->if($condition)
            ->withCookies(['foo' => 'bar'])
            ->else()
            ->fi();

        $allCookies = [
            'foo' => [
                'foo',
                'bar',
                time() + 86400,
                '/',
                '',
                false,
                false,
            ],
        ];

        $this->assertSame($allCookies, $response->getCookies());
    }

    public function testSetDataFlow(): void
    {
        $condition = false;
        $response = new Response('hello');

        $this->assertSame('hello', $response->getContent());

        $response
            ->if($condition)
            ->setData('foo')
            ->else()
            ->setData('bar')
            ->fi();

        $this->assertSame('"bar"', $response->getContent());
        $this->assertTrue($response->isOk());
    }

    public function testSetDataFlow2(): void
    {
        $condition = true;
        $response = new Response('hello');

        $this->assertSame('hello', $response->getContent());

        $response
            ->if($condition)
            ->setData('foo')
            ->else()
            ->setData('bar')
            ->fi();

        $this->assertSame('"foo"', $response->getContent());
        $this->assertTrue($response->isOk());
    }

    public function testSetProtocolVersionFlow(): void
    {
        $condition = false;
        $response = new Response('hello');

        $response
            ->if($condition)
            ->setProtocolVersion('1.0')
            ->else()
            ->setProtocolVersion('1.1')
            ->fi();

        $this->assertSame('hello', $response->getContent());
        $this->assertSame('1.1', $response->getProtocolVersion());
        $this->assertTrue($response->isOk());
    }

    public function testSetProtocolVersionFlow2(): void
    {
        $condition = true;
        $response = new Response('hello');

        $response
            ->if($condition)
            ->setProtocolVersion('1.0')
            ->else()
            ->setProtocolVersion('1.1')
            ->fi();

        $this->assertSame('hello', $response->getContent());
        $this->assertSame('1.0', $response->getProtocolVersion());
        $this->assertTrue($response->isOk());
    }

    public function testSetStatusCodeFlow(): void
    {
        $condition = false;
        $response = new Response('hello');

        $response
            ->if($condition)
            ->setStatusCode(500)
            ->else()
            ->setStatusCode(200)
            ->fi();

        $this->assertSame('hello', $response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
    }

    public function testSetStatusCodeFlow2(): void
    {
        $condition = true;
        $response = new Response('hello');

        $response
            ->if($condition)
            ->setStatusCode(500)
            ->else()
            ->setStatusCode(200)
            ->fi();

        $this->assertSame('hello', $response->getContent());
        $this->assertSame(500, $response->getStatusCode());
        $this->assertFalse($response->isOk());
    }

    public function testCharsetFlow(): void
    {
        $condition = false;
        $response = new Response('hello');

        $response
            ->if($condition)
            ->charset('UTF-8')
            ->else()
            ->charset('GBK')
            ->fi();

        $this->assertSame('GBK', $response->getCharset());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
    }

    public function testCharsetFlow2(): void
    {
        $condition = true;
        $response = new Response('hello');

        $response
            ->if($condition)
            ->charset('UTF-8')
            ->else()
            ->charset('GBK')
            ->fi();

        $this->assertSame('UTF-8', $response->getCharset());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
    }

    public function testSetExpiresFlow(): void
    {
        $condition = false;
        $response = new Response('hello');

        $response
            ->if($condition)
            ->setExpires()
            ->else()
            ->setExpires(new DateTime('2018-08-07'))
            ->fi();

        $this->assertSame('hello', $response->getContent());
        $this->assertSame('2018-08-07 00:00:00', date('Y-m-d H:i:s', strtotime($response->headers->get('Expires'))));
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
    }

    public function testSetExpiresFlow2(): void
    {
        $condition = true;
        $response = new Response('hello');

        $response
            ->if($condition)
            ->setExpires()
            ->else()
            ->setExpires(new DateTime('2018-08-07'))
            ->fi();

        $this->assertSame('hello', $response->getContent());
        $this->assertFalse($response->headers->has('Expires'));
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
    }

    public function testSetLastModifiedFlow(): void
    {
        $condition = false;
        $response = new Response('hello');

        $response
            ->if($condition)
            ->setLastModified()
            ->else()
            ->setLastModified(new DateTime('2018-08-07'))
            ->fi();

        $this->assertSame('hello', $response->getContent());
        $this->assertSame('2018-08-07 00:00:00', date('Y-m-d H:i:s', strtotime($response->headers->get('Last-Modified'))));
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
    }

    public function testSetLastModifiedFlow2(): void
    {
        $condition = true;
        $response = new Response('hello');

        $response
            ->if($condition)
            ->setLastModified()
            ->else()
            ->setLastModified(new DateTime('2018-08-07'))
            ->fi();

        $this->assertSame('hello', $response->getContent());
        $this->assertFalse($response->headers->has('Last-Modified'));
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
    }

    public function testSetCacheFlow(): void
    {
        $condition = false;
        $response = new Response('hello');

        $response
            ->if($condition)
            ->setCache(1)
            ->else()
            ->setCache(5)
            ->fi();

        $date = new DateTime();
        $date->modify('+5minutes');
        $date->setTimezone(new DateTimeZone('UTC'));
        $result = $date->format('D, d M Y H:i:s').' GMT';

        $date->modify('+1seconds');
        $result2 = $date->format('D, d M Y H:i:s').' GMT';

        $date->modify('+1seconds');
        $result3 = $date->format('D, d M Y H:i:s').' GMT';

        $this->assertSame('hello', $response->getContent());
        $this->assertTimeRange($response->headers->get('Expires'), $result, $result2, $result3);
        $this->assertSame('max-age=300', $response->headers->get('Cache-Control'));
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
    }

    public function testSetCacheFlow2(): void
    {
        $condition = true;

        $response = new Response('hello');

        $response
            ->if($condition)
            ->setCache(1)
            ->else()
            ->setCache(5)
            ->fi();

        $date = new DateTime();
        $date->modify('+1minutes');
        $date->setTimezone(new DateTimeZone('UTC'));
        $result = $date->format('D, d M Y H:i:s').' GMT';

        $date->modify('+1seconds');
        $result2 = $date->format('D, d M Y H:i:s').' GMT';

        $date->modify('+1seconds');
        $result3 = $date->format('D, d M Y H:i:s').' GMT';

        $this->assertSame('hello', $response->getContent());
        $this->assertTimeRange($response->headers->get('Expires'), $result, $result2, $result3);
        $this->assertSame('max-age=60', $response->headers->get('Cache-Control'));
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
    }

    public function testSetNotModifiedFlow(): void
    {
        $condition = false;
        $response = new Response('hello');

        $response
            ->if($condition)
            ->setNotModified()
            ->else()
            ->fi();

        $this->assertSame('hello', $response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
    }

    public function testSetNotModifiedFlow2(): void
    {
        $condition = true;
        $response = new Response('hello');

        $response
            ->if($condition)
            ->setNotModified()
            ->else()
            ->fi();

        $this->assertSame('hello', $response->getContent());
        $this->assertSame(304, $response->getStatusCode());
        $this->assertFalse($response->isOk());
    }

    public function testSetContentTypeFlow(): void
    {
        $condition = false;
        $response = new Response('hello');

        $response
            ->if($condition)
            ->setContentType('text/html')
            ->else()
            ->setContentType('text/plain');

        $this->assertSame('hello', $response->getContent());
        $this->assertSame('text/plain', $response->headers->get('Content-Type'));
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
    }

    public function testSetContentTypeFlow2(): void
    {
        $condition = true;
        $response = new Response('hello');

        $response
            ->if($condition)
            ->setContentType('text/html')
            ->else()
            ->setContentType('text/plain');

        $this->assertSame('hello', $response->getContent());
        $this->assertSame('text/html', $response->headers->get('Content-Type'));
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
    }

    public function testSetContentLengthFlow(): void
    {
        $condition = false;
        $response = new Response('hello');

        $response
            ->if($condition)
            ->setContentLength(10)
            ->else()
            ->setContentLength(50);

        $this->assertSame('hello', $response->getContent());
        $this->assertSame('50', $response->headers->get('Content-Length'));
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
    }

    public function testSetContentLengthFlow2(): void
    {
        $condition = true;
        $response = new Response('hello');

        $response
            ->if($condition)
            ->setContentLength(10)
            ->else()
            ->setContentLength(50);

        $this->assertSame('hello', $response->getContent());
        $this->assertSame('10', $response->headers->get('Content-Length'));
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
    }

    public function testSetEtagFlow(): void
    {
        $condition = false;
        $response = new Response('hello');

        $response
            ->if($condition)
            ->setEtag('foo')
            ->else()
            ->setEtag('bar');

        $this->assertSame('hello', $response->getContent());
        $this->assertSame('bar', $response->headers->get('Etag'));
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
    }

    public function testSetEtagFlow2(): void
    {
        $condition = true;
        $response = new Response('hello');

        $response
            ->if($condition)
            ->setEtag('foo')
            ->else()
            ->setEtag('bar');

        $this->assertSame('hello', $response->getContent());
        $this->assertSame('foo', $response->headers->get('Etag'));
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
    }

    public function testSetDataException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Malformed UTF-8 characters, possibly incorrectly encoded');

        // json_encode("\xB1\x31") 会引发 PHP 内核提示 Segmentation fault (core dumped)
        if (extension_loaded('leevel')) {
            throw new InvalidArgumentException('Malformed UTF-8 characters, possibly incorrectly encoded');
        }

        $response = new Response();

        $response->setData("\xB1\x31");
    }
}

class MyArray implements IArray
{
    public function toArray(): array
    {
        return ['hello' => 'IArray'];
    }
}

class MyJson implements IJson
{
    public function toJson(?int $option = null): string
    {
        if (null === $option) {
            $option = JSON_UNESCAPED_UNICODE;
        }

        return json_encode(['hello' => 'IJson'], $option);
    }
}

class MyJsonSerializable implements JsonSerializable
{
    public function jsonSerialize()
    {
        return ['hello' => 'JsonSerializable'];
    }
}

class StringableObject
{
    public function __toString()
    {
        return 'Foo';
    }
}
