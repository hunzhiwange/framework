<?php
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
namespace Tests\Router;

use DateTime;
use Tests\TestCase;
use JsonSerializable;
use ReflectionProperty;
use InvalidArgumentException;
use Queryyetsimple\Http\Request;
use Queryyetsimple\Http\Response;
use Queryyetsimple\Support\IJson;
use Queryyetsimple\Support\IArray;

/**
 * Response test
 * This class borrows heavily from the Symfony2 Framework and is part of the symfony package
 * 
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.03.13
 * @version 1.0
 * @see Symfony\Component\HttpFoundation (https://github.com/symfony/symfony)
 */
class ResponseTest extends TestCase
{

    public function testCreate()
    {
        $response = Response::create('foo', 301, array('Foo' => 'bar'));
        $this->assertInstanceOf('Queryyetsimple\Http\IResponse', $response);
        $this->assertInstanceOf('Queryyetsimple\Http\Response', $response);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertEquals('bar', $response->headers->get('foo'));
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
        $this->assertEquals($charsetOrigin, $charset);
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
        $this->assertEquals(304, $modified->getStatusCode());
    }

    public function testIsSuccessful()
    {
        $response = new Response();
        $this->assertTrue($response->isSuccessful());
    }

    public function testGetSetProtocolVersion()
    {
        $response = new Response();
        $this->assertEquals('1.0', $response->getProtocolVersion());
        $response->setProtocolVersion('1.1');
        $this->assertEquals('1.1', $response->getProtocolVersion());
    }

    public function testContentTypeCharset()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/css');
        $this->assertEquals('text/css', $response->headers->get('Content-Type'));
        $response->headers->remove('Content-Type');
        $this->assertNull($response->headers->get('Content-Type'));

        $response->setContentType('text/css');
        $this->assertEquals('text/css', $response->headers->get('Content-Type'));
        $response->headers->remove('Content-Type');
        $this->assertNull($response->headers->get('Content-Type'));

        $response->setContentType('text/css', 'UTF-8');
        $this->assertEquals('text/css; charset=UTF-8', $response->headers->get('Content-Type'));
        $response->headers->remove('Content-Type');
        $this->assertNull($response->headers->get('Content-Type'));

        $response->setCharset('GBK');
        $response->setContentType('text/css');
        $this->assertEquals('text/css; charset=GBK', $response->headers->get('Content-Type'));
        $response->headers->remove('Content-Type');
        $this->assertNull($response->headers->get('Content-Type'));
    }

    public function testSetCache()
    {
        $response = new Response();

        $response->setCache(5);
        $this->assertTrue($response->headers->has('expires'));
        $this->assertEquals($response->headers->get('cache-control'), 'max-age=300');

        $response->setExpires(null);
        $this->assertNull($response->headers->get('Expires'), '->setExpires() remove the header when passed null');

        $response->setEtag('hello-world-etag');
        $this->assertEquals($response->headers->get('Etag'), 'hello-world-etag');

        $date = new DateTime();
        $date->modify('+' . 5 . 'minutes');

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
        $this->assertEquals('{"foo":"bar"}', $response->getContent());

        $response->setData(new MyArray());
        $this->assertEquals('{"hello":"IArray"}', $response->getContent());

        $response->setData(new MyJson());
        $this->assertEquals('{"hello":"IJson"}', $response->getContent());

        $response->setData(new MyJsonSerializable());
        $this->assertEquals('{"hello":"JsonSerializable"}', $response->getContent());
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
     */
    public function testSetStatusCode($code, $text, $expectedText)
    {
        $response = new Response();
        $response->setStatusCode($code, $text);
        $statusText = new ReflectionProperty($response, 'statusText');
        $statusText->setAccessible(true);
        $this->assertEquals($expectedText, $statusText->getValue($response));
    }

    public function getStatusCodeFixtures()
    {
        return array(
            array('200', null, 'OK'),
            array('200', false, ''),
            array('200', 'foo', 'foo'),
            array('199', null, 'unknown status'),
            array('199', false, ''),
            array('199', 'foo', 'foo'),
        );
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
        foreach (array(301, 302, 303, 307) as $code) {
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

        $response = new Response('', 301, array('Location' => '/good-uri'));
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
        foreach (array(204, 304) as $code) {
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
     */
    public function testSetContent($content)
    {
        $response = new Response();
        $response->setContent($content);
        $this->assertEquals((string) $content, $response->getContent());
    }

    public function validContentProvider()
    {
        return array(
            'obj' => array(new StringableObject()),
            'string' => array('Foo'),
            'int' => array(2),
        );
    }

    /**
     * @expectedException \UnexpectedValueException
     * @dataProvider invalidContentProvider
     */
    public function testSetContentInvalid($content)
    {
        $response = new Response();
        $response->setContent($content);
    }

    public function invalidContentProvider()
    {
        return array(
            'obj' => array(new \stdClass())
        );
    }
}

class MyArray implements IArray {

    /**
     * 对象转数组
     *
     * @return array
     */
    public function toArray() {
        return ['hello' => 'IArray'];
    }
}

class MyJson implements IJson {

    /**
     * 对象转 JSON
     *
     * @param integer $option
     * @return string
     */
    public function toJson($option = JSON_UNESCAPED_UNICODE) {
        return json_encode(['hello' => 'IJson'], $option);
    }
}

class MyJsonSerializable implements JsonSerializable {

    public function jsonSerialize() {
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
