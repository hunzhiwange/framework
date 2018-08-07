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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Http;

use DateTime;
use DateTimeZone;
use InvalidArgumentException;
use JsonSerializable;
use Leevel\Cookie\ICookie;
use Leevel\Http\Response;
use Leevel\Support\IArray;
use Leevel\Support\IJson;
use ReflectionProperty;
use Tests\TestCase;

/**
 * Response test
 * This class borrows heavily from the Symfony4 Framework and is part of the symfony package.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.03.13
 *
 * @version 1.0
 *
 * @see Symfony\Component\HttpFoundation (https://github.com/symfony/symfony)
 */
class ResponseTest extends TestCase
{
    public function testCreate()
    {
        $response = Response::create('foo', 301, ['Foo' => 'bar']);
        $this->assertInstanceOf('Leevel\Http\IResponse', $response);
        $this->assertInstanceOf('Leevel\Http\Response', $response);
        $this->assertSame(301, $response->getStatusCode());
        $this->assertSame('bar', $response->headers->get('foo'));
    }

    public function testSendHeaders()
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

    public function testSend()
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

    public function testGetCharset()
    {
        $response = new Response();
        $charsetOrigin = 'UTF-8';
        $response->setCharset($charsetOrigin);
        $charset = $response->getCharset();
        $this->assertSame($charsetOrigin, $charset);
    }

    public function testSetNotModified()
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

    public function testIsSuccessful()
    {
        $response = new Response();
        $this->assertTrue($response->isSuccessful());
    }

    public function testGetSetProtocolVersion()
    {
        $response = new Response();
        $this->assertSame('1.0', $response->getProtocolVersion());
        $response->setProtocolVersion('1.1');
        $this->assertSame('1.1', $response->getProtocolVersion());
    }

    public function testContentTypeCharset()
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

    public function testSetCache()
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

    public function testSendContent()
    {
        $response = new Response('test response rendering', 200);
        ob_start();
        $response->sendContent();
        $string = ob_get_clean();
        $this->assertContains('test response rendering', $string);
    }

    public function testSetJsonData()
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

    public function testIsInvalid()
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
     */
    public function testSetStatusCode($code, $text, $expectedText)
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
            ['200', false, ''],
            ['200', 'foo', 'foo'],
            ['199', null, 'unknown status'],
            ['199', false, ''],
            ['199', 'foo', 'foo'],
        ];
    }

    public function testIsInformational()
    {
        $response = new Response('', 100);
        $this->assertTrue($response->isInformational());
        $response = new Response('', 200);
        $this->assertFalse($response->isInformational());
    }

    public function testIsRedirectRedirection()
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

    public function testIsNotFound()
    {
        $response = new Response('', 404);
        $this->assertTrue($response->isNotFound());
        $response = new Response('', 200);
        $this->assertFalse($response->isNotFound());
    }

    public function testIsEmpty()
    {
        foreach ([204, 304] as $code) {
            $response = new Response('', $code);
            $this->assertTrue($response->isEmpty());
        }
        $response = new Response('', 200);
        $this->assertFalse($response->isEmpty());
    }

    public function testIsForbidden()
    {
        $response = new Response('', 403);
        $this->assertTrue($response->isForbidden());
        $response = new Response('', 200);
        $this->assertFalse($response->isForbidden());
    }

    public function testIsOk()
    {
        $response = new Response('', 200);
        $this->assertTrue($response->isOk());
        $response = new Response('', 404);
        $this->assertFalse($response->isOk());
    }

    public function testIsServerOrClientError()
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
    public function testSetContent($content)
    {
        $response = new Response();
        $response->setContent($content);
        $this->assertSame((string) $content, $response->getContent());
    }

    public function validContentProvider()
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
    public function testSetContentInvalid($content)
    {
        $this->expectException(\UnexpectedValueException::class);

        $response = new Response();
        $response->setContent($content);
    }

    public function invalidContentProvider()
    {
        return [
            'obj' => [new \stdClass()],
        ];
    }

    public function testSetContentAsJon()
    {
        $response = new Response();

        $response->setContent(new MyArray());

        $this->assertSame('{"hello":"IArray"}', $response->getContent());
        $this->assertTrue($response->isOk());
        $this->assertSame(['hello' => 'IArray'], $response->getData());
    }

    public function testSetContentAsJonWithIJson()
    {
        $response = new Response();

        $response->setContent(new MyJson());

        $this->assertSame('{"hello":"IJson"}', $response->getContent());
        $this->assertTrue($response->isOk());
        $this->assertSame(['hello' => 'IJson'], $response->getData());
    }

    public function testAppendContent()
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

    public function testWithHeaders()
    {
        $response = new Response();

        $response->withHeaders(['foo' => 'bar']);

        $this->assertSame('bar', $response->headers->get('foo'));
    }

    public function testCookieResolverNotSet()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Cookie resolver is not set.'
        );

        $response = new Response();

        $response->cookie('foo', 'bar');
    }

    public function testCookie()
    {
        $response = new Response();

        $cookie = $this->createMock(ICookie::class);

        Response::setCookieResolver(function () use ($cookie) {
            return $cookie;
        });

        $cookie->method('set')->willReturn(null);
        $this->assertNull($cookie->set('foo', 'bar'));

        $allCookies = [
            'q_foo' => [
                'q_foo',
                'bar',
                time() + 86400,
                '/',
                '',
                false,
                false,
            ],
            'q_hello' => [
                'q_hello',
                'world',
                time() + 86400,
                '/',
                '',
                false,
                false,
            ],
        ];

        $cookie->method('all')->willReturn($allCookies);
        $this->assertSame($allCookies, $cookie->all());

        $response->cookie('foo', 'bar');
        $response->withCookies(['hello' => 'world']);

        $this->assertSame($allCookies, $response->getCookies());

        Response::setCookieResolver(null);
    }

    public function testContent()
    {
        $response = new Response();

        $response->setContent('hello');

        $this->assertSame('hello', $response->content());
        $this->assertTrue($response->isOk());
    }

    public function testGetOriginal()
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

    public function testStatus()
    {
        $response = new Response();

        $response->setContent('hello');

        $this->assertSame(200, $response->status());
    }

    public function testSetContentLength()
    {
        $response = new Response();

        $response->setContentLength(100);

        $this->assertSame(100, $response->headers->get('Content-Length'));
    }

    public function testIsJson()
    {
        $response = new Response();

        $response->setContent('foo');

        $this->assertFalse($response->isJson());

        $response->setContent(new MyArray());

        $this->assertTrue($response->isJson());
    }

    public function testSetContentFlow()
    {
        $condition = false;

        $response = new Response();

        $response->

        ifs($condition)->

        setContent('foo')->

        elses()->

        setContent('bar')->

        endIfs();

        $this->assertSame('bar', $response->getContent());
        $this->assertTrue($response->isOk());
    }

    public function testSetContentFlow2()
    {
        $condition = true;

        $response = new Response();

        $response->

        ifs($condition)->

        setContent('foo')->

        elses()->

        setContent('bar')->

        endIfs();

        $this->assertSame('foo', $response->getContent());
        $this->assertTrue($response->isOk());
    }

    public function testAppendContentFlow()
    {
        $condition = false;

        $response = new Response('hello');

        $response->

        ifs($condition)->

        appendContent('foo')->

        elses()->

        appendContent('bar')->

        endIfs();

        $this->assertSame('hellobar', $response->getContent());
        $this->assertTrue($response->isOk());
    }

    public function testAppendContentFlow2()
    {
        $condition = true;

        $response = new Response('hello');

        $response->

        ifs($condition)->

        appendContent('foo')->

        elses()->

        appendContent('bar')->

        endIfs();

        $this->assertSame('hellofoo', $response->getContent());
        $this->assertTrue($response->isOk());
    }

    public function testSetHeaderFlow()
    {
        $condition = false;

        $response = new Response('hello');

        $response->

        ifs($condition)->

        setHeader('foo', 'bar')->

        elses()->

        setHeader('foo', 'bar2')->

        endIfs();

        $this->assertSame('bar2', $response->headers->get('foo'));
        $this->assertTrue($response->isOk());
    }

    public function testSetHeaderFlow2()
    {
        $condition = true;

        $response = new Response('hello');

        $response->

        ifs($condition)->

        setHeader('foo', 'bar')->

        elses()->

        setHeader('foo', 'bar2')->

        endIfs();

        $this->assertSame('bar', $response->headers->get('foo'));
        $this->assertTrue($response->isOk());
    }

    public function testWithHeadersFlow()
    {
        $condition = false;

        $response = new Response('hello');

        $response->

        ifs($condition)->

        withHeaders(['foo' => 'bar'])->

        elses()->

        withHeaders(['foo' => 'bar2'])->

        endIfs();

        $this->assertSame('bar2', $response->headers->get('foo'));
        $this->assertTrue($response->isOk());
    }

    public function testWithHeadersFlow2()
    {
        $condition = true;

        $response = new Response('hello');

        $response->

        ifs($condition)->

        withHeaders(['foo' => 'bar'])->

        elses()->

        withHeaders(['foo' => 'bar2'])->

        endIfs();

        $this->assertSame('bar', $response->headers->get('foo'));
        $this->assertTrue($response->isOk());
    }

    public function testSetCookieFlow()
    {
        $condition = false;

        $response = new Response('hello');

        $response->

        ifs($condition)->

        setCookie('foo', 'bar')->

        elses()->

        endIfs();

        $this->assertSame('hello', $response->getContent());
        $this->assertTrue($response->isOk());
    }

    public function testSetCookieFlow2()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Cookie resolver is not set.'
        );

        $condition = true;

        $response = new Response('hello');

        $response->

        ifs($condition)->

        setCookie('foo', 'bar')->

        elses()->

        endIfs();
    }

    public function testWithCookiesFlow()
    {
        $condition = false;

        $response = new Response('hello');

        $response->

        ifs($condition)->

        withCookies(['foo' => 'bar'])->

        elses()->

        endIfs();

        $this->assertSame('hello', $response->getContent());
        $this->assertTrue($response->isOk());
    }

    public function testWithCookiesFlow2()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Cookie resolver is not set.'
        );

        $condition = true;

        $response = new Response('hello');

        $response->

        ifs($condition)->

        withCookies(['foo' => 'bar'])->

        elses()->

        endIfs();

        $this->assertSame('hello', $response->getContent());
        $this->assertTrue($response->isOk());
    }

    public function testSetDataFlow()
    {
        $condition = false;

        $response = new Response('hello');

        $this->assertSame('hello', $response->getContent());

        $response->

        ifs($condition)->

        setData('foo')->

        elses()->

        setData('bar')->

        endIfs();

        $this->assertSame('"bar"', $response->getContent());
        $this->assertTrue($response->isOk());
    }

    public function testSetDataFlow2()
    {
        $condition = true;

        $response = new Response('hello');

        $this->assertSame('hello', $response->getContent());

        $response->

        ifs($condition)->

        setData('foo')->

        elses()->

        setData('bar')->

        endIfs();

        $this->assertSame('"foo"', $response->getContent());
        $this->assertTrue($response->isOk());
    }

    public function testSetProtocolVersionFlow()
    {
        $condition = false;

        $response = new Response('hello');

        $response->

        ifs($condition)->

        setProtocolVersion('1.0')->

        elses()->

        setProtocolVersion('1.1')->

        endIfs();

        $this->assertSame('hello', $response->getContent());
        $this->assertSame('1.1', $response->getProtocolVersion());
        $this->assertTrue($response->isOk());
    }

    public function testSetProtocolVersionFlow2()
    {
        $condition = true;

        $response = new Response('hello');

        $response->

        ifs($condition)->

        setProtocolVersion('1.0')->

        elses()->

        setProtocolVersion('1.1')->

        endIfs();

        $this->assertSame('hello', $response->getContent());
        $this->assertSame('1.0', $response->getProtocolVersion());
        $this->assertTrue($response->isOk());
    }

    public function testSetStatusCodeFlow()
    {
        $condition = false;

        $response = new Response('hello');

        $response->

        ifs($condition)->

        setStatusCode(500)->

        elses()->

        setStatusCode(200)->

        endIfs();

        $this->assertSame('hello', $response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
    }

    public function testSetStatusCodeFlow2()
    {
        $condition = true;

        $response = new Response('hello');

        $response->

        ifs($condition)->

        setStatusCode(500)->

        elses()->

        setStatusCode(200)->

        endIfs();

        $this->assertSame('hello', $response->getContent());
        $this->assertSame(500, $response->getStatusCode());
        $this->assertFalse($response->isOk());
    }

    public function testCharsetFlow()
    {
        $condition = false;

        $response = new Response('hello');

        $response->

        ifs($condition)->

        charset('UTF-8')->

        elses()->

        charset('GBK')->

        endIfs();

        $this->assertSame('GBK', $response->getCharset());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
    }

    public function testCharsetFlow2()
    {
        $condition = true;

        $response = new Response('hello');

        $response->

        ifs($condition)->

        charset('UTF-8')->

        elses()->

        charset('GBK')->

        endIfs();

        $this->assertSame('UTF-8', $response->getCharset());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
    }

    public function testSetExpiresFlow()
    {
        $condition = false;

        $response = new Response('hello');

        $response->

        ifs($condition)->

        setExpires()->

        elses()->

        setExpires(new DateTime('2018-08-07'))->

        endIfs();

        $this->assertSame('hello', $response->getContent());
        $this->assertSame('Tue, 07 Aug 2018 00:00:00 GMT', $response->headers->get('Expires'));
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
    }

    public function testSetExpiresFlow2()
    {
        $condition = true;

        $response = new Response('hello');

        $response->

        ifs($condition)->

        setExpires()->

        elses()->

        setExpires(new DateTime('2018-08-07'))->

        endIfs();

        $this->assertSame('hello', $response->getContent());
        $this->assertFalse($response->headers->has('Expires'));
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
    }

    public function testSetLastModifiedFlow()
    {
        $condition = false;

        $response = new Response('hello');

        $response->

        ifs($condition)->

        setLastModified()->

        elses()->

        setLastModified(new DateTime('2018-08-07'))->

        endIfs();

        $this->assertSame('hello', $response->getContent());
        $this->assertSame('Tue, 07 Aug 2018 00:00:00 GMT', $response->headers->get('Last-Modified'));
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
    }

    public function testSetLastModifiedFlow2()
    {
        $condition = true;

        $response = new Response('hello');

        $response->

        ifs($condition)->

        SetLastModified()->

        elses()->

        SetLastModified(new DateTime('2018-08-07'))->

        endIfs();

        $this->assertSame('hello', $response->getContent());
        $this->assertFalse($response->headers->has('Last-Modified'));
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
    }

    public function testSetCacheFlow()
    {
        $condition = false;

        $response = new Response('hello');

        $response->

        ifs($condition)->

        setCache(1)->

        elses()->

        setCache(5)->

        endIfs();

        $date = new DateTime();
        $date->modify('+5minutes');
        $date->setTimezone(new DateTimeZone('UTC'));
        $result = $date->format('D, d M Y H:i:s').' GMT';

        $this->assertSame('hello', $response->getContent());
        $this->assertSame($result, $response->headers->get('Expires'));
        $this->assertSame('max-age=300', $response->headers->get('Cache-Control'));
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
    }

    public function testSetCacheFlow2()
    {
        $condition = true;

        $response = new Response('hello');

        $response->

        ifs($condition)->

        setCache(1)->

        elses()->

        setCache(5)->

        endIfs();

        $date = new DateTime();
        $date->modify('+1minutes');
        $date->setTimezone(new DateTimeZone('UTC'));
        $result = $date->format('D, d M Y H:i:s').' GMT';

        $this->assertSame('hello', $response->getContent());
        $this->assertSame($result, $response->headers->get('Expires'));
        $this->assertSame('max-age=60', $response->headers->get('Cache-Control'));
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
    }

    public function testSetNotModifiedFlow()
    {
        $condition = false;

        $response = new Response('hello');

        $response->

        ifs($condition)->

        setNotModified()->

        elses()->

        endIfs();

        $this->assertSame('hello', $response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
    }

    public function testSetNotModifiedFlow2()
    {
        $condition = true;

        $response = new Response('hello');

        $response->

        ifs($condition)->

        setNotModified()->

        elses()->

        endIfs();

        $this->assertSame('hello', $response->getContent());
        $this->assertSame(304, $response->getStatusCode());
        $this->assertFalse($response->isOk());
    }

    public function testSetContentTypeFlow()
    {
        $condition = false;

        $response = new Response('hello');

        $response->

        ifs($condition)->

        setContentType('text/html')->

        elses()->

        setContentType('text/plain');

        $this->assertSame('hello', $response->getContent());
        $this->assertSame('text/plain', $response->headers->get('Content-Type'));
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
    }

    public function testSetContentTypeFlow2()
    {
        $condition = true;

        $response = new Response('hello');

        $response->

        ifs($condition)->

        setContentType('text/html')->

        elses()->

        setContentType('text/plain');

        $this->assertSame('hello', $response->getContent());
        $this->assertSame('text/html', $response->headers->get('Content-Type'));
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
    }

    public function testSetContentLengthFlow()
    {
        $condition = false;

        $response = new Response('hello');

        $response->

        ifs($condition)->

        setContentLength(10)->

        elses()->

        setContentLength(50);

        $this->assertSame('hello', $response->getContent());
        $this->assertSame(50, $response->headers->get('Content-Length'));
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
    }

    public function testSetContentLengthFlow2()
    {
        $condition = true;

        $response = new Response('hello');

        $response->

        ifs($condition)->

        setContentLength(10)->

        elses()->

        setContentLength(50);

        $this->assertSame('hello', $response->getContent());
        $this->assertSame(10, $response->headers->get('Content-Length'));
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
    }

    public function testSetEtagFlow()
    {
        $condition = false;

        $response = new Response('hello');

        $response->

        ifs($condition)->

        setEtag('foo')->

        elses()->

        setEtag('bar');

        $this->assertSame('hello', $response->getContent());
        $this->assertSame('bar', $response->headers->get('Etag'));
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
    }

    public function testSetEtagFlow2()
    {
        $condition = true;

        $response = new Response('hello');

        $response->

        ifs($condition)->

        setEtag('foo')->

        elses()->

        setEtag('bar');

        $this->assertSame('hello', $response->getContent());
        $this->assertSame('foo', $response->headers->get('Etag'));
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
    }

    public function testSetDataException()
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
    /**
     * 对象转数组.
     *
     * @return array
     */
    public function toArray()
    {
        return ['hello' => 'IArray'];
    }
}

class MyJson implements IJson
{
    /**
     * 对象转 JSON.
     *
     * @param int $option
     *
     * @return string
     */
    public function toJson($option = JSON_UNESCAPED_UNICODE)
    {
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
