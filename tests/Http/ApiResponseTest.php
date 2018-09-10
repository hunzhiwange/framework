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

use Leevel\Http\ApiResponse;
use Tests\TestCase;

/**
 * ApiResponse test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.04
 *
 * @version 1.0
 */
class ApiResponseTest extends TestCase
{
    public function testBaseUse()
    {
        $response = ApiResponse::create(['hello' => 'world']);

        $this->assertSame('{"hello":"world"}', $response->getContent());

        $this->assertSame(200, $response->getStatusCode());

        $this->assertSame('OK', $this->getTestProperty($response, 'statusText'));
    }

    public function testOk()
    {
        $response = new ApiResponse();

        $response->ok(['hello' => 'world']);

        $this->assertSame('{"hello":"world"}', $response->getContent());

        $this->assertSame(200, $response->getStatusCode());

        $this->assertSame('OK', $this->getTestProperty($response, 'statusText'));
    }

    public function testOk2()
    {
        $condition = false;

        $response = new ApiResponse();

        $response->

        ifs($condition)->

        ok(['hello' => 'world'])->

        elses()->

        ok(['hello2' => 'world2'])->

        endIfs();

        $this->assertSame('{"hello2":"world2"}', $response->getContent());

        $this->assertSame(200, $response->getStatusCode());

        $this->assertSame('OK', $this->getTestProperty($response, 'statusText'));
    }

    public function testCreated()
    {
        $response = new ApiResponse();

        $response->created('http://queryphp.com', ['hello' => 'world']);

        $this->assertSame('{"hello":"world"}', $response->getContent());

        $this->assertSame(201, $response->getStatusCode());

        $this->assertSame('http://queryphp.com', $response->headers->get('Location'));

        $this->assertSame('Created', $this->getTestProperty($response, 'statusText'));
    }

    public function testCreated2()
    {
        $condition = false;

        $response = new ApiResponse();

        $response->

        ifs($condition)->

        created('http://queryphp.com', ['hello' => 'world'])->

        elses()->

        created('http://queryphp2.com', ['hello2' => 'world2'])->

        endIfs();

        $this->assertSame('{"hello2":"world2"}', $response->getContent());

        $this->assertSame(201, $response->getStatusCode());

        $this->assertSame('http://queryphp2.com', $response->headers->get('Location'));

        $this->assertSame('Created', $this->getTestProperty($response, 'statusText'));
    }

    public function testAccepted()
    {
        $response = new ApiResponse();

        $response->accepted('http://queryphp.com', ['hello' => 'world']);

        $this->assertSame('{"hello":"world"}', $response->getContent());

        $this->assertSame(202, $response->getStatusCode());

        $this->assertSame('http://queryphp.com', $response->headers->get('Location'));

        $this->assertSame('Accepted', $this->getTestProperty($response, 'statusText'));
    }

    public function testAccepted2()
    {
        $condition = false;

        $response = new ApiResponse();

        $response->

        ifs($condition)->

        accepted('http://queryphp.com', ['hello' => 'world'])->

        elses()->

        accepted('http://queryphp2.com', ['hello2' => 'world2'])->

        endIfs();

        $this->assertSame('{"hello2":"world2"}', $response->getContent());

        $this->assertSame(202, $response->getStatusCode());

        $this->assertSame('http://queryphp2.com', $response->headers->get('Location'));

        $this->assertSame('Accepted', $this->getTestProperty($response, 'statusText'));
    }

    public function testNoContent()
    {
        $response = new ApiResponse();

        $response->noContent();

        $this->assertSame('{}', $response->getContent());

        $this->assertSame(204, $response->getStatusCode());

        $this->assertSame('No Content', $this->getTestProperty($response, 'statusText'));
    }

    public function testNoContent2()
    {
        $condition = false;

        $response = new ApiResponse();

        $response->

        ifs($condition)->

        noContent()->

        elses()->

        noContent()->

        endIfs();

        $this->assertSame('{}', $response->getContent());

        $this->assertSame(204, $response->getStatusCode());

        $this->assertSame('No Content', $this->getTestProperty($response, 'statusText'));
    }

    public function testUnprocessableEntity()
    {
        $response = new ApiResponse();

        $response->unprocessableEntity(['foo' => 'bar', 'hello' => 'world'], 'error message', 'status text');

        $this->assertSame('{"message":"error message","errors":{"foo":"bar","hello":"world"}}', $response->getContent());

        $this->assertSame(422, $response->getStatusCode());

        $this->assertSame('status text', $this->getTestProperty($response, 'statusText'));
    }

    public function testUnprocessableEntity2()
    {
        $condition = false;

        $response = new ApiResponse();

        $response->

        ifs($condition)->

        unprocessableEntity(['foo' => 'bar', 'hello' => 'world'], 'error message', 'status text')->

        elses()->

        unprocessableEntity(['foo2' => 'bar2', 'hello2' => 'world2'], 'error message2', 'status text2')->

        endIfs();

        $this->assertSame('{"message":"error message2","errors":{"foo2":"bar2","hello2":"world2"}}', $response->getContent());

        $this->assertSame(422, $response->getStatusCode());

        $this->assertSame('status text2', $this->getTestProperty($response, 'statusText'));
    }

    public function testError()
    {
        $response = new ApiResponse();

        $response->error('test message', 500, 'status text');

        $this->assertSame('{"message":"test message"}', $response->getContent());

        $this->assertSame(500, $response->getStatusCode());

        $this->assertSame('status text', $this->getTestProperty($response, 'statusText'));
    }

    public function testError2()
    {
        $condition = false;

        $response = new ApiResponse();

        $response->

        ifs($condition)->

        error('test message', 500, 'status text')->

        elses()->

        error('test message2', 500, 'status text2')->

        endIfs();

        $this->assertSame('{"message":"test message2"}', $response->getContent());

        $this->assertSame(500, $response->getStatusCode());

        $this->assertSame('status text2', $this->getTestProperty($response, 'statusText'));
    }

    public function testBadRequest()
    {
        $response = new ApiResponse();

        $response->badRequest('test message', 'status text');

        $this->assertSame('{"message":"test message"}', $response->getContent());

        $this->assertSame(400, $response->getStatusCode());

        $this->assertSame('status text', $this->getTestProperty($response, 'statusText'));
    }

    public function testUnauthorized()
    {
        $response = new ApiResponse();

        $response->unauthorized('test message', 'status text');

        $this->assertSame('{"message":"test message"}', $response->getContent());

        $this->assertSame(401, $response->getStatusCode());

        $this->assertSame('status text', $this->getTestProperty($response, 'statusText'));
    }

    public function testForbidden()
    {
        $response = new ApiResponse();

        $response->forbidden('test message', 'status text');

        $this->assertSame('{"message":"test message"}', $response->getContent());

        $this->assertSame(403, $response->getStatusCode());

        $this->assertSame('status text', $this->getTestProperty($response, 'statusText'));
    }

    public function testNotFound()
    {
        $response = new ApiResponse();

        $response->notFound('test message', 'status text');

        $this->assertSame('{"message":"test message"}', $response->getContent());

        $this->assertSame(404, $response->getStatusCode());

        $this->assertSame('status text', $this->getTestProperty($response, 'statusText'));
    }

    public function testMethodNotAllowed()
    {
        $response = new ApiResponse();

        $response->methodNotAllowed('test message', 'status text');

        $this->assertSame('{"message":"test message"}', $response->getContent());

        $this->assertSame(405, $response->getStatusCode());

        $this->assertSame('status text', $this->getTestProperty($response, 'statusText'));
    }

    public function testTooManyRequests()
    {
        $response = new ApiResponse();

        $response->tooManyRequests('test message', 'status text');

        $this->assertSame('{"message":"test message"}', $response->getContent());

        $this->assertSame(429, $response->getStatusCode());

        $this->assertSame('status text', $this->getTestProperty($response, 'statusText'));
    }

    public function testInternalServerError()
    {
        $response = new ApiResponse();

        $response->internalServerError('test message', 'status text');

        $this->assertSame('{"message":"test message"}', $response->getContent());

        $this->assertSame(500, $response->getStatusCode());

        $this->assertSame('status text', $this->getTestProperty($response, 'statusText'));
    }
}
