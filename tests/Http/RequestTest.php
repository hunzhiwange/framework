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

use Leevel\Http\Request;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Tests\TestCase;

/**
 * @api(
 *     title="HTTP Request",
 *     path="component/http/request",
 *     description="
 * QueryPHP 请求对象构建在 Symfony HttpFoundation 之上，增加了少量的功能。
 *
 * ## 使用方式
 *
 * 使用容器 request 服务
 *
 * ``` php
 * \App::make('request')->get($key, $default = null);
 * \App::make('request')->all(): array;
 * ```
 *
 * 依赖注入
 *
 * ``` php
 * class Demo
 * {
 *     private \Leevel\Http\Request $request;
 *
 *     public function __construct(\Leevel\Http\Request $request)
 *     {
 *         $this->request = $request;
 *     }
 * }
 * ```
 *
 * 使用静态代理
 *
 * ``` php
 * \Leevel\Router\Proxy\Request::get(string $key, $default = null);
 * \Leevel\Router\Proxy\Request::all(): array;
 * ```
 *
 * ::: warning 注意
 * 为了一致性或者更好与 Swoole 对接，请统一使用请求对象处理输入，避免直接使用 `$_GET`、`$_POST`,`$_COOKIE`,`$_FILES`,`$_SERVER` 等全局变量。
 * :::
 * ",
 * )
 */
class RequestTest extends TestCase
{
    public function testCoroutineContext(): void
    {
        $this->assertTrue(Request::coroutineContext());
    }

    /**
     * @api(
     *     title="createFromSymfonyRequest 从 Symfony 请求创建 Leevel 请求",
     *     description="",
     *     note="",
     * )
     */
    public function testCreateFromSymfonyRequest(): void
    {
        $symfonyRequest = new SymfonyRequest(['foo' => 'bar', 'hello' => 'world'], [], [], [], [], [], 'content');
        $request = Request::createFromSymfonyRequest($symfonyRequest);

        $this->assertInstanceof(Request::class, $request);
        $this->assertSame(['foo' => 'bar', 'hello' => 'world'], $request->query->all());
        $this->assertSame('content', $request->getContent());
    }

    public function testCreateFromSymfonyRequestWithSelfRequest(): void
    {
        $selfRequest = new Request(['foo' => 'bar', 'hello' => 'world'], [], [], [], [], [], 'content');
        $request = Request::createFromSymfonyRequest($selfRequest);

        $this->assertInstanceof(Request::class, $request);
        $this->assertSame(['foo' => 'bar', 'hello' => 'world'], $request->query->all());
        $this->assertSame('content', $request->getContent());
    }

    /**
     * @api(
     *     title="all 获取所有请求参数",
     *     description="
     *  * 包含 request、query 和 attributes
     *  * 优先级从高到底依次为 attributes、query 和 request，优先级高的会覆盖优先级低的参数
     * ",
     *     note="",
     * )
     */
    public function testAll(): void
    {
        $request = new Request(['query' => '1'], ['request' => '2'], ['attributes' => '3']);
        $this->assertSame(['request' => '2', 'query' => '1', 'attributes' => '3'], $request->all());

        $request = new Request(['foo' => '1'], ['foo' => '2'], ['foo' => '3']);
        $this->assertSame(['foo' => '2'], $request->all());
    }

    /**
     * @api(
     *     title="exists 请求是否包含非空",
     *     description="",
     *     note="",
     * )
     */
    public function testExists(): void
    {
        $request = new Request(['foo' => 'bar', 'hello' => 'world']);
        $this->assertTrue($request->exists(['foo']));
        $this->assertTrue($request->exists(['foo', 'hello']));
        $this->assertFalse($request->exists(['notFound']));
    }

    /**
     * @api(
     *     title="only 取得给定的 keys 数据",
     *     description="",
     *     note="",
     * )
     */
    public function testOnly(): void
    {
        $request = new Request(['foo' => 'bar', 'hello' => 'world']);
        $this->assertSame(['foo' => 'bar'], $request->only(['foo']));
        $this->assertSame(['foo' => 'bar', 'hello' => 'world'], $request->only(['foo', 'hello']));
        $this->assertSame(['foo' => 'bar', 'not' => null], $request->only(['foo', 'not']));
    }

    /**
     * @api(
     *     title="except 取得排除给定的 keys 数据",
     *     description="",
     *     note="",
     * )
     */
    public function testExcept(): void
    {
        $request = new Request(['foo' => 'bar', 'hello' => 'world']);
        $this->assertSame(['hello' => 'world'], $request->except(['foo']));
        $this->assertSame([], $request->except(['foo', 'hello']));
        $this->assertSame(['hello' => 'world'], $request->except(['foo', 'not']));
    }

    /**
     * @api(
     *     title="isCli 是否为 PHP 运行模式命令行, 兼容 Swoole HTTP Service",
     *     description="Swoole HTTP 服务器也以命令行运行，也就是 Swoole 情况下会返回 false。",
     *     note="",
     * )
     */
    public function testIsCli(): void
    {
        $request = new Request();
        $this->assertTrue($request->isCli());
    }

    /**
     * @api(
     *     title="isCli 是否为 PHP 运行模式命令行，Swoole 场景测试",
     *     description="",
     *     note="",
     * )
     */
    public function testIsCliForSwoole(): void
    {
        $request = new Request([], [], [], [], [], ['SERVER_SOFTWARE' => 'swoole-http-server']);
        $this->assertFalse($request->isCli());
    }

    /**
     * @api(
     *     title="isCgi 是否为 PHP 运行模式 cgi",
     *     description="",
     *     note="",
     * )
     */
    public function testIsCgi(): void
    {
        $request = new Request();
        $this->assertFalse($request->isCgi());
    }

    /**
     * @api(
     *     title="isPjax 是否为 Pjax 请求行为",
     *     description="",
     *     note="",
     * )
     */
    public function testIsPjax(): void
    {
        $request = new Request();

        $this->assertFalse($request->isPjax());
        $request->request->set(Request::VAR_PJAX, true);
        $this->assertTrue($request->isPjax());
    }

    /**
     * @api(
     *     title="isJson.isRealJson 是否为 json 请求行为，支持伪装",
     *     description="",
     *     note="",
     * )
     */
    public function testIsJson(): void
    {
        $request = new Request();

        $this->assertFalse($request->isJson());
        $this->assertFalse($request->isRealJson());

        $request->query->set(Request::VAR_JSON, '1');
        $this->assertTrue($request->isJson());
        $this->assertFalse($request->isRealJson());
    }

    /**
     * @api(
     *     title="isJson 是否为 json 请求行为",
     *     description="",
     *     note="",
     * )
     */
    public function testIsJsonForContentType(): void
    {
        $request = new Request([], [], [], [], [], ['CONTENT_TYPE' => 'application/json']);
        $this->assertTrue($request->isJson());
    }

    public function testIsJsonForContentTypeButIsHtml(): void
    {
        $request = new Request([], [], [], [], [], ['CONTENT_TYPE' => 'text/html']);
        $this->assertFalse($request->isJson());
    }

    /**
     * @api(
     *     title="isAcceptAny 是否为接受任何请求，支持伪装",
     *     description="",
     *     note="",
     * )
     */
    public function testIsAcceptJson(): void
    {
        $request = new Request();

        $this->assertFalse($request->isRealAcceptJson());
        $this->assertFalse($request->isAcceptJson());

        $request->headers->set('accept', 'application/json, text/plain, */*');
        $this->assertTrue($request->isRealAcceptJson());
        $this->assertTrue($request->isAcceptJson());
        $request->headers->remove('accept');

        $this->assertFalse($request->isRealAcceptJson());
        $this->assertFalse($request->isAcceptJson());

        // (isAjax && !isPjax) && isAcceptAny
        $request->request->set(Request::VAR_AJAX, 1);
        $this->assertFalse($request->isRealAcceptJson());
        $this->assertTrue($request->isAcceptJson());
        $request->request->remove(Request::VAR_AJAX);

        // 伪装
        $request->query->set(Request::VAR_ACCEPT_JSON, '1');
        $this->assertTrue($request->isAcceptJson());
        $this->assertFalse($request->isRealAcceptJson());
    }

    public function testIsRealAcceptJsonIsFalse(): void
    {
        $request = new Request();

        $this->assertFalse($request->isRealAcceptJson());
        $this->assertFalse($request->isAcceptJson());

        $request->headers->set('accept', 'application/pdf, text/plain, */*');
        $this->assertFalse($request->isRealAcceptJson());
        $this->assertFalse($request->isAcceptJson());
    }

    /**
     * @api(
     *     title="isAcceptAny 是否为接受任何请求",
     *     description="",
     *     note="",
     * )
     */
    public function testIsAcceptAny(): void
    {
        $request = new Request();

        $this->assertTrue($request->isAcceptAny());

        $request->headers->set('accept', 'application/json');
        $this->assertFalse($request->isAcceptAny());

        $request->headers->set('accept', '*/*');
        $this->assertTrue($request->isAcceptAny());
    }

    /**
     * @api(
     *     title="getEnter 获取入口文件",
     *     description="",
     *     note="",
     * )
     */
    public function testGetEnter(): void
    {
        $request = new Request();
        $this->assertSame('', $request->getEnter());
    }

    public function testGetEnterWasNotEmpty(): void
    {
        $request = new Request([], [], [], [], [], ['SERVER_SOFTWARE' => 'swoole-http-server', 'SCRIPT_NAME' => '/base/web/index_dev.php']);
        $this->assertSame('/base/web', $request->getEnter());
    }

    /**
     * @api(
     *     title="setPathInfo 设置 pathInfo",
     *     description="",
     *     note="",
     * )
     */
    public function testSetPathInfo(): void
    {
        $request = new Request();
        $this->assertSame('/', $request->getPathInfo());
        $request->setPathInfo('/foo/bar');
        $this->assertSame('/foo/bar', $request->getPathInfo());
    }

    /**
     * @api(
     *     title="toArray 对象转数组",
     *     description="Request 请求对象实现了 `\Leevel\Support\IArray` 接口。",
     *     note="",
     * )
     */
    public function testToArray(): void
    {
        $request = new Request(['foo' => 'bar', 'hello' => 'world']);
        $this->assertSame(['foo' => 'bar', 'hello' => 'world'], $request->toArray());
    }
}
