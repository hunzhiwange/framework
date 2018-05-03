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
namespace Tests\Http;

use Tests\TestCase;
use JsonSerializable;
use InvalidArgumentException;
use Queryyetsimple\Support\IJson;
use Queryyetsimple\Support\IArray;
use Queryyetsimple\Http\JsonResponse;

/**
 * JsonResponseTest test
 * This class borrows heavily from the Symfony4 Framework and is part of the symfony package
 * 
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.03.14
 * @version 1.0
 * @see Symfony\Component\HttpFoundation (https://github.com/symfony/symfony)
 */
class JsonResponseTest extends TestCase
{
    public function testConstructorEmptyCreatesJsonObject()
    {
        $response = new JsonResponse();
        $this->assertSame('{}', $response->getContent());
    }

    public function testConstructorWithArrayCreatesJsonArray()
    {
        $response = new JsonResponse(array(0, 1, 2, 3));
        $this->assertSame('[0,1,2,3]', $response->getContent());
    }

    public function testConstructorWithAssocArrayCreatesJsonObject()
    {
        $response = new JsonResponse(array('foo' => 'bar'));
        $this->assertSame('{"foo":"bar"}', $response->getContent());
    }

    public function testConstructorWithSimpleTypes()
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

    public function testConstructorWithCustomStatus()
    {
        $response = new JsonResponse(array(), 202);
        $this->assertSame(202, $response->getStatusCode());
    }

    public function testConstructorAddsContentTypeHeader()
    {
        $response = new JsonResponse();
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
    }

    public function testConstructorWithCustomHeaders()
    {
        $response = new JsonResponse(array(), 200, array('ETag' => 'foo'));
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        $this->assertSame('foo', $response->headers->get('ETag'));
    }

    public function testConstructorWithCustomContentType()
    {
        $headers = array('Content-Type' => 'application/vnd.acme.blog-v1+json');
        $response = new JsonResponse(array(), 200, $headers);
        $this->assertSame('application/vnd.acme.blog-v1+json', $response->headers->get('Content-Type'));
    }

    public function testSetJson()
    {
        $response = new JsonResponse('1', 200, array(), true);
        $this->assertEquals('1', $response->getContent());

        $response = new JsonResponse('[1]', 200, array(), true);
        $this->assertEquals('[1]', $response->getContent());

        $response = new JsonResponse(null, 200, array());
        $response->setJson('true');
        $this->assertEquals('true', $response->getContent());
    }

    public function testCreate()
    {
        $response = JsonResponse::create(array('foo' => 'bar'), 204);
        $this->assertInstanceOf('Queryyetsimple\Http\JsonResponse', $response);
        $this->assertEquals('{"foo":"bar"}', $response->getContent());
        $this->assertEquals(204, $response->getStatusCode());
    }

    public function testStaticCreateEmptyJsonObject()
    {
        $response = JsonResponse::create();
        $this->assertInstanceOf('Queryyetsimple\Http\JsonResponse', $response);
        $this->assertSame('{}', $response->getContent());
    }

    public function testStaticCreateJsonArray()
    {
        $response = JsonResponse::create(array(0, 1, 2, 3));
        $this->assertInstanceOf('Queryyetsimple\Http\JsonResponse', $response);
        $this->assertSame('[0,1,2,3]', $response->getContent());
    }

    public function testStaticCreateJsonObject()
    {
        $response = JsonResponse::create(array('foo' => 'bar'));
        $this->assertInstanceOf('Queryyetsimple\Http\JsonResponse', $response);
        $this->assertSame('{"foo":"bar"}', $response->getContent());
    }

    public function testStaticCreateWithSimpleTypes()
    {
        $response = JsonResponse::create('foo');
        $this->assertInstanceOf('Queryyetsimple\Http\JsonResponse', $response);
        $this->assertSame('"foo"', $response->getContent());

        $response = JsonResponse::create(0);
        $this->assertInstanceOf('Queryyetsimple\Http\JsonResponse', $response);
        $this->assertSame('0', $response->getContent());

        $response = JsonResponse::create(0.1);
        $this->assertInstanceOf('Queryyetsimple\Http\JsonResponse', $response);
        $this->assertSame('0.1', $response->getContent());

        $response = JsonResponse::create(true);
        $this->assertInstanceOf('Queryyetsimple\Http\JsonResponse', $response);
        $this->assertSame('true', $response->getContent());
    }

    public function testStaticCreateWithCustomStatus()
    {
        $response = JsonResponse::create(array(), 202);
        $this->assertSame(202, $response->getStatusCode());
    }

    public function testStaticCreateAddsContentTypeHeader()
    {
        $response = JsonResponse::create();
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
    }

    public function testStaticCreateWithCustomHeaders()
    {
        $response = JsonResponse::create(array(), 200, array('ETag' => 'foo'));
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        $this->assertSame('foo', $response->headers->get('ETag'));
    }

    public function testStaticCreateWithCustomContentType()
    {
        $headers = array('Content-Type' => 'application/vnd.acme.blog-v1+json');
        $response = JsonResponse::create(array(), 200, $headers);
        $this->assertSame('application/vnd.acme.blog-v1+json', $response->headers->get('Content-Type'));
    }

    public function testSetCallback()
    {
        $response = JsonResponse::create(array('foo' => 'bar'))->setCallback('callback');
        $this->assertEquals(';callback({"foo":"bar"});', $response->getContent());
        $this->assertEquals('text/javascript', $response->headers->get('Content-Type'));
    }

    public function testJsonEncodeFlags()
    {
        $response = new JsonResponse('<>\'&"');
        $this->assertEquals("\"<>'&\\\"\"", $response->getContent());
    }

    public function testGetEncodingOptions()
    {
        $response = new JsonResponse();
        
        $this->assertEquals(JSON_UNESCAPED_UNICODE, $response->getEncodingOptions());
    }

    public function testSetEncodingOptions()
    {
        $response = new JsonResponse();
        $response->setData(array(array(1, 2, 3)));
        $this->assertEquals('[[1,2,3]]', $response->getContent());

        $response->setEncodingOptions(JSON_FORCE_OBJECT);
        $this->assertEquals('{"0":{"0":1,"1":2,"2":3}}', $response->getContent());
    }

    public function testItAcceptsJsonAsString()
    {
        $response = JsonResponse::fromJsonString('{"foo":"bar"}');
        $this->assertSame('{"foo":"bar"}', $response->getContent());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetContent()
    {
        // json_encode("\xB1\x31") 会引发 PHP 内核提示 Segmentation fault (core dumped)
        if (extension_loaded('queryyetsimple')) {
            throw new InvalidArgumentException('wow! error.');
        } else {
            JsonResponse::create("\xB1\x31");
        }
    }

    public function testSetContentJsonObject()
    {
        $response = new JsonResponse();
        $response->setData(['foo' => 'bar']);
        $this->assertEquals('{"foo":"bar"}', $response->getContent());

        $response->setData(new JsonResponseMyArray());
        $this->assertEquals('{"hello":"IArray"}', $response->getContent());

        $response->setData(new JsonResponseMyJson());
        $this->assertEquals('{"hello":"IJson"}', $response->getContent());

        $response->setData(new JsonResponseMyJsonSerializable());
        $this->assertEquals('{"hello":"JsonSerializable"}', $response->getContent());
    }

    public function testSetComplexCallback()
    {
        $response = JsonResponse::create(array('foo' => 'bar'));
        $response->setCallback('ಠ_ಠ["foo"].bar[0]');
        $this->assertEquals(';ಠ_ಠ["foo"].bar[0]({"foo":"bar"});', $response->getContent());
    }
}

class JsonResponseMyArray implements IArray {

    /**
     * 对象转数组
     *
     * @return array
     */
    public function toArray() {
        return ['hello' => 'IArray'];
    }
}

class JsonResponseMyJson implements IJson {

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

class JsonResponseMyJsonSerializable implements JsonSerializable {

    public function jsonSerialize() {
        return ['hello' => 'JsonSerializable'];
    }
}
