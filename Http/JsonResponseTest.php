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

use Tests\TestCase;
use Queryyetsimple\Http\JsonResponse;

/**
 * JsonResponseTest test
 * This class borrows heavily from the Symfony2 Framework and is part of the symfony package
 * 
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.03.14
 * @version 1.0
 * @see Symfony\Component\HttpFoundation (https://github.com/symfony/symfony)
 */
class JsonResponseTest extends TestCase
{
    public function t2estConstructorEmptyCreatesJsonObject()
    {
        $response = new JsonResponse();
        $this->assertSame('{}', $response->getContent());
    }

    public function t2estConstructorWithArrayCreatesJsonArray()
    {
        $response = new JsonResponse(array(0, 1, 2, 3));
        $this->assertSame('[0,1,2,3]', $response->getContent());
    }

    public function t2estConstructorWithAssocArrayCreatesJsonObject()
    {
        $response = new JsonResponse(array('foo' => 'bar'));
        $this->assertSame('{"foo":"bar"}', $response->getContent());
    }

    public function t2estConstructorWithSimpleTypes()
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

    public function t2estConstructorWithCustomStatus()
    {
        $response = new JsonResponse(array(), 202);
        $this->assertSame(202, $response->getStatusCode());
    }

    public function t2estConstructorAddsContentTypeHeader()
    {
        $response = new JsonResponse();
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
    }

    public function t2estConstructorWithCustomHeaders()
    {
        $response = new JsonResponse(array(), 200, array('ETag' => 'foo'));
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        $this->assertSame('foo', $response->headers->get('ETag'));
    }

    public function t2estConstructorWithCustomContentType()
    {
        $headers = array('Content-Type' => 'application/vnd.acme.blog-v1+json');
        $response = new JsonResponse(array(), 200, $headers);
        $this->assertSame('application/vnd.acme.blog-v1+json', $response->headers->get('Content-Type'));
    }

    public function t2estSetJson()
    {
        $response = new JsonResponse('1', 200, array(), true);
        $this->assertEquals('1', $response->getContent());

        $response = new JsonResponse('[1]', 200, array(), true);
        $this->assertEquals('[1]', $response->getContent());

        $response = new JsonResponse(null, 200, array());
        $response->setJson('true');
        $this->assertEquals('true', $response->getContent());
    }

    public function t2estCreate()
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

    public function t2estItAcceptsJsonAsString()
    {
        $response = JsonResponse::fromJsonString('{"foo":"bar"}');
        $this->assertSame('{"foo":"bar"}', $response->getContent());
    }
    /**
     * @expectedException \InvalidArgumentException
     */
    public function t2estSetCallbackInvalidIdentifier()
    {
        $response = new JsonResponse('foo');
        $response->setCallback('+invalid');
    }
    /**
     * @expectedException \InvalidArgumentException
     */
    public function t2estSetContent()
    {
        JsonResponse::create("\xB1\x31");
    }
    /**
     * @expectedException \Exception
     * @expectedExceptionMessage This error is expected
     */
    public function t2estSetContentJsonSerializeError()
    {
        if (!interface_exists('JsonSerializable', false)) {
            $this->markTestSkipped('JsonSerializable is required.');
        }
        $serializable = new JsonSerializableObject();
        JsonResponse::create($serializable);
    }
    public function t2estSetComplexCallback()
    {
        $response = JsonResponse::create(array('foo' => 'bar'));
        $response->setCallback('ಠ_ಠ["foo"].bar[0]');
        $this->assertEquals('/**/ಠ_ಠ["foo"].bar[0]({"foo":"bar"});', $response->getContent());
    }
}
