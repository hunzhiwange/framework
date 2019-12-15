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

use Leevel\Http\IRequest;
use Leevel\Http\Request;
use Leevel\Http\UploadedFile;
use Tests\TestCase;

/**
 * - This class borrows heavily from the Symfony4 Framework and is part of the symfony package.
 *
 * @see Symfony\Component\HttpFoundation (https://github.com/symfony/symfony)
 *
 * @api(
 *     title="HTTP Request",
 *     path="component/http/request",
 *     description="
 * QueryPHP 请求对象基于 Symfony 二次开发，功能非常强大，做了一些精简和优化。
 *
 * ## 使用方式
 *
 * 使用容器 request 服务
 *
 * ``` php
 * \App::make('request')->get(string $key, $defaults = null);
 * \App::make('request')->all(): array;
 * ```
 *
 * 依赖注入
 *
 * ``` php
 * class Demo
 * {
 *     private $request;
 *
 *     public function __construct(\Leevel\Http\IRequest $request)
 *     {
 *         $this->request = $request;
 *     }
 * }
 * ```
 *
 * 使用静态代理
 *
 * ``` php
 * \Leevel\Router\Proxy\Request::get(string $key, $defaults = null);
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
    protected function setUp(): void
    {
        $dir = sys_get_temp_dir().'/form_test';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    protected function tearDown(): void
    {
        foreach (glob(sys_get_temp_dir().'/form_test/*') as $file) {
            unlink($file);
        }

        rmdir(sys_get_temp_dir().'/form_test');
    }

    /**
     * @api(
     *     title="reset 重置请求数据",
     *     description="",
     *     note="",
     * )
     */
    public function testReset(): void
    {
        $request = new Request();
        $request->reset(['foo' => 'bar']);
        $this->assertSame('bar', $request->query->get('foo'), '->reset() takes an array of query params as its first argument');

        $request->reset([], ['foo' => 'bar']);
        $this->assertSame('bar', $request->request->get('foo'), '->reset() takes an array of request params as its second argument');

        $request->reset([], [], ['foo' => 'bar']);
        $this->assertSame('bar', $request->params->get('foo'), '->reset() takes an array of params as its third argument');

        $request->reset([], [], [], [], [], ['HTTP_FOO' => 'bar']);
        $this->assertSame('bar', $request->headers->get('FOO'), '->reset() takes an array of HTTP headers as its sixth argument');
    }

    /**
     * @api(
     *     title="setLanguage.getLanguage 请求语言",
     *     description="",
     *     note="",
     * )
     */
    public function testLanguage(): void
    {
        $request = new Request();

        $request->setLanguage('zh-cn');

        $this->assertSame($request->getLanguage(), 'zh-cn');
    }

    /**
     * @api(
     *     title="setLanguage.language 请求语言别名",
     *     description="",
     *     note="",
     * )
     */
    public function testLanguageAlia(): void
    {
        $request = new Request();

        $request->setLanguage('zh-cn');

        $this->assertSame($request->language(), 'zh-cn');
    }

    /**
     * @api(
     *     title="getUri 获取当前 URL 地址",
     *     description="",
     *     note="",
     * )
     */
    public function testGetUri(): void
    {
        $server = [];

        // Standard Request on non default PORT
        // http://host:8080/index.php/path/info?query=string

        $server['HTTP_HOST'] = 'host:8080';
        $server['SERVER_NAME'] = 'servername';
        $server['SERVER_PORT'] = '8080';

        $server['QUERY_STRING'] = 'query=string';
        $server['REQUEST_URI'] = '/index.php/path/info?query=string';
        $server['SCRIPT_NAME'] = '/index.php';
        $server['PATH_INFO'] = '/path/info';
        $server['PATH_TRANSLATED'] = 'redirect:/index.php/path/info';
        $server['PHP_SELF'] = '/index_dev.php/path/info';
        $server['SCRIPT_FILENAME'] = '/some/where/index.php';

        $request = new Request();
        $request->reset([], [], [], [], [], $server);

        $this->assertSame('http://host:8080/index.php/path/info?query=string', $request->getUri(), '->getUri() with non default port');

        // Use std port number
        $server['HTTP_HOST'] = 'host';
        $server['SERVER_NAME'] = 'servername';
        $server['SERVER_PORT'] = '80';

        $request->reset([], [], [], [], [], $server);

        $this->assertSame('http://host/index.php/path/info?query=string', $request->getUri(), '->getUri() with default port');

        // Without HOST HEADER
        unset($server['HTTP_HOST']);
        $server['SERVER_NAME'] = 'servername';
        $server['SERVER_PORT'] = '80';

        $request->reset([], [], [], [], [], $server);

        $this->assertSame('http://servername/index.php/path/info?query=string', $request->getUri(), '->getUri() with default port without HOST_HEADER');

        // Request with URL REWRITING (hide index.php)
        //   RewriteCond %{REQUEST_FILENAME} !-f
        //   RewriteRule ^(.*)$ index.php [QSA,L]
        // http://host:8080/path/info?query=string
        $server = [];
        $server['HTTP_HOST'] = 'host:8080';
        $server['SERVER_NAME'] = 'servername';
        $server['SERVER_PORT'] = '8080';

        $server['REDIRECT_QUERY_STRING'] = 'query=string';
        $server['REDIRECT_URL'] = '/path/info';
        $server['SCRIPT_NAME'] = '/index.php';
        $server['QUERY_STRING'] = 'query=string';
        $server['REQUEST_URI'] = '/path/info?toto=test&1=1';
        $server['SCRIPT_NAME'] = '/index.php';
        $server['PHP_SELF'] = '/index.php';
        $server['SCRIPT_FILENAME'] = '/some/where/index.php';

        $request->reset([], [], [], [], [], $server);

        $this->assertSame('http://host:8080/path/info?query=string', $request->getUri(), '->getUri() with rewrite');

        // Use std port number
        //  http://host/path/info?query=string
        $server['HTTP_HOST'] = 'host';
        $server['SERVER_NAME'] = 'servername';
        $server['SERVER_PORT'] = '80';

        $request->reset([], [], [], [], [], $server);

        $this->assertSame('http://host/path/info?query=string', $request->getUri(), '->getUri() with rewrite and default port');

        // Without HOST HEADER
        unset($server['HTTP_HOST']);
        $server['SERVER_NAME'] = 'servername';
        $server['SERVER_PORT'] = '80';

        $request->reset([], [], [], [], [], $server);

        $this->assertSame('http://servername/path/info?query=string', $request->getUri(), '->getUri() with rewrite, default port without HOST_HEADER');

        // With encoded characters
        $server = [
            'HTTP_HOST'       => 'host:8080',
            'SERVER_NAME'     => 'servername',
            'SERVER_PORT'     => '8080',
            'QUERY_STRING'    => 'query=string',
            'REQUEST_URI'     => '/ba%20se/index_dev.php/foo%20bar/in+fo?query=string',
            'SCRIPT_NAME'     => '/ba se/index_dev.php',
            'PATH_TRANSLATED' => 'redirect:/index.php/foo bar/in+fo',
            'PHP_SELF'        => '/ba se/index_dev.php/path/info',
            'SCRIPT_FILENAME' => '/some/where/ba se/index_dev.php',
        ];

        $request->reset([], [], [], [], [], $server);

        $this->assertSame(
            'http://host:8080/ba%20se/index_dev.php/foo%20bar/in+fo?query=string',
            $request->getUri()
        );
    }

    /**
     * @api(
     *     title="getSchemeAndHttpHost 取得 Scheme 和 Host",
     *     description="",
     *     note="",
     * )
     */
    public function testGetSchemeAndHttpHost(): void
    {
        $request = new Request();

        $server = [];
        $server['SERVER_NAME'] = 'servername';
        $server['SERVER_PORT'] = '90';

        $request->reset([], [], [], [], [], $server);

        $this->assertSame('http://servername:90', $request->getSchemeAndHttpHost());
    }

    /**
     * @dataProvider getQueryStringNormalizationData
     *
     * @param mixed $query
     * @param mixed $expectedQuery
     * @param mixed $msg
     */
    public function testGetQueryString($query, $expectedQuery, $msg): void
    {
        $request = new Request();
        $request->server->set('QUERY_STRING', $query);
        $this->assertSame($expectedQuery, $request->getQueryString(), $msg);
    }

    public function getQueryStringNormalizationData()
    {
        return [
            ['foo', 'foo', 'works with valueless params'],
            ['foo=', 'foo=', 'includes a dangling equal sign'],
            ['bar=&foo=bar', 'bar=&foo=bar', '->works with empty params'],
            ['foo=bar&bar=', 'foo=bar&bar=', ''],
            ['him=John%20Doe&her=Jane+Doe', 'him=John%20Doe&her=Jane+Doe', ''],
            ['foo[]=1&foo[]=2', 'foo[]=1&foo[]=2', 'allows array notation'],
            ['foo=1&foo=2', 'foo=1&foo=2', 'allows repeated params'],
            ['pa%3Dram=foo%26bar%3Dbaz&test=test', 'pa%3Dram=foo%26bar%3Dbaz&test=test', 'works with encoded delimiters'],
            ['0', '0', 'allows "0"'],
            ['Jane Doe&John%20Doe', 'Jane Doe&John%20Doe', ''],
            ['her=Jane Doe&him=John%20Doe', 'her=Jane Doe&him=John%20Doe', ''],
            ['foo=bar&&&test&&', 'foo=bar&test', 'removes unneeded delimiters'],
            ['formula=e=m*c^2', 'formula=e=m*c^2', ''],
        ];
    }

    public function testGetQueryStringReturnsNull(): void
    {
        $request = new Request();
        $this->assertNull($request->getQueryString(), '->getQueryString() returns null for non-existent query string');

        $request->server->set('QUERY_STRING', '');
        $this->assertNull($request->getQueryString(), '->getQueryString() returns null for empty query string');
    }

    /**
     * @api(
     *     title="getHost 获取 host",
     *     description="",
     *     note="",
     * )
     */
    public function testGetHost(): void
    {
        $request = new Request();

        $request->reset(['foo' => 'bar']);
        $this->assertSame('', $request->getHost(), '->getHost() return empty string if not resetd');

        $request->reset([], [], [], [], [], ['HTTP_HOST' => 'www.example.com']);
        $this->assertSame('www.example.com', $request->getHost(), '->getHost() from Host Header');

        // Host header with port number
        $request->reset([], [], [], [], [], ['HTTP_HOST' => 'www.example.com:8080']);
        $this->assertSame('www.example.com', $request->getHost(), '->getHost() from Host Header with port number');

        // Server values
        $request->reset([], [], [], [], [], ['SERVER_NAME' => 'www.example.com']);
        $this->assertSame('www.example.com', $request->getHost(), '->getHost() from server name');

        $request->reset([], [], [], [], [], ['SERVER_NAME' => 'www.example.com', 'HTTP_HOST' => 'www.host.com']);
        $this->assertSame('www.host.com', $request->getHost(), '->getHost() value from Host header has priority over SERVER_NAME ');
    }

    /**
     * @api(
     *     title="getPort 服务器端口",
     *     description="",
     *     note="",
     * )
     */
    public function testGetPort(): void
    {
        $request = new Request([], [], [], [], [], [
            'HTTP_X_FORWARDED_PROTO' => 'https',
            'HTTP_X_FORWARDED_PORT'  => '443',
        ]);
        $port = $request->getPort();
        $this->assertSame(80, $port, 'Without trusted proxies FORWARDED_PROTO and FORWARDED_PORT are ignored.');
    }

    /**
     * @api(
     *     title="setMethod.setMethod 请求类型",
     *     description="",
     *     note="",
     * )
     */
    public function testGetSetMethod(): void
    {
        $request = new Request();
        $this->assertSame('GET', $request->getMethod(), '->getMethod() returns GET if no method is defined');
        $request->setMethod('get');
        $this->assertSame('GET', $request->getMethod(), '->getMethod() returns an uppercased string');
        $request->setMethod('PURGE');
        $this->assertSame('PURGE', $request->getMethod(), '->getMethod() returns the method even if it is not a standard one');
        $request->setMethod('POST');
        $this->assertSame('POST', $request->getMethod(), '->getMethod() returns the method POST if no '.IRequest::VAR_METHOD.' is defined');
        $request->setMethod('POST');
        $request->request->set(IRequest::VAR_METHOD, 'purge');
        $this->assertSame('PURGE', $request->getMethod(), '->getMethod() does not return the method from '.IRequest::VAR_METHOD.' if defined and POST but support not enabled');

        $request = new Request();
        $request->setMethod('POST');
        $request->request->set(IRequest::VAR_METHOD, 'purge');
        $this->assertTrue('PURGE' === $request->getMethod(), '');

        $request = new Request();
        $request->setMethod('POST');
        $request->headers->set('X-HTTP-METHOD-OVERRIDE', 'delete');
        $this->assertSame('DELETE', $request->getMethod(), '->getMethod() returns the method from X-HTTP-Method-Override even though '.IRequest::VAR_METHOD.' is set if defined and POST');
    }

    /**
     * @dataProvider provideOverloadedMethods
     *
     * @param mixed $method
     *
     * @api(
     *     title="createFromGlobals 全局变量创建一个 Request",
     *     description="
     * **支持的类型**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Http\RequestTest::class, 'provideOverloadedMethods')]}
     * ```
     * ",
     *     note="",
     * )
     */
    public function testCreateFromGlobals($method): void
    {
        $normalizedMethod = strtoupper($method);
        $_GET['foo1'] = 'bar1';
        $_POST['foo2'] = 'bar2';
        $_COOKIE['foo3'] = 'bar3';
        $_SERVER['foo5'] = 'bar5';
        $request = Request::createFromGlobals();

        $this->assertSame('bar1', $request->query->get('foo1'), '::fromGlobals() uses values from $_GET');
        $this->assertSame('bar2', $request->request->get('foo2'), '::fromGlobals() uses values from $_POST');
        $this->assertSame('bar3', $request->cookies->get('foo3'), '::fromGlobals() uses values from $_COOKIE');
        $this->assertSame('bar5', $request->server->get('foo5'), '::fromGlobals() uses values from $_SERVER');

        unset($_GET['foo1'], $_POST['foo2'], $_COOKIE['foo3'], $_SERVER['foo5']);
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';

        $request = RequestContentProxy::createFromGlobals();
        $this->assertSame($normalizedMethod, $request->getMethod());
        $this->assertSame('mycontent', $request->request->get('content'));

        unset($_SERVER['REQUEST_METHOD'], $_SERVER['CONTENT_TYPE']);
    }

    public function provideOverloadedMethods()
    {
        return [
            ['PUT'],
            ['DELETE'],
            ['PATCH'],
            ['put'],
            ['delete'],
            ['patch'],
        ];
    }

    /**
     * @dataProvider provideOverloadedMethods2
     *
     * @param mixed $method
     */
    public function testCreateFromGlobalWithApplicationJson($method): void
    {
        $normalizedMethod = strtoupper($method);
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['CONTENT_TYPE'] = 'application/json';

        $request = RequestContentApplicationJson::createFromGlobals();
        $this->assertSame($normalizedMethod, $request->getMethod());
        $this->assertSame('admin', $request->request->get('name'));
        $this->assertSame('123456', $request->request->get('password'));
        unset($_SERVER['REQUEST_METHOD'], $_SERVER['CONTENT_TYPE']);
    }

    public function provideOverloadedMethods2()
    {
        return [
            ['POST'],
            ['DELETE'],
            ['PATCH'],
            ['put'],
            ['delete'],
            ['patch'],
            ['post'],
            ['get'],
            ['options'],
        ];
    }

    public function testGetScriptName(): void
    {
        $request = new Request();
        $this->assertSame('', $request->getScriptName());
    }

    /**
     * @api(
     *     title="getScriptName 取得脚本名字",
     *     description="",
     *     note="",
     * )
     */
    public function testGetScriptName2(): void
    {
        $server = [];
        $server['SCRIPT_NAME'] = '/index.php';
        $request = new Request([], [], [], [], [], $server);
        $this->assertSame('/index.php', $request->getScriptName());
    }

    public function testGetScriptName3(): void
    {
        $server = [];
        $server['ORIG_SCRIPT_NAME'] = '/frontend.php';
        $request = new Request([], [], [], [], [], $server);
        $this->assertSame('/frontend.php', $request->getScriptName());
    }

    public function testGetScriptName4(): void
    {
        $server = [];
        $server['SCRIPT_NAME'] = '/index.php';
        $server['ORIG_SCRIPT_NAME'] = '/frontend.php';
        $request = new Request([], [], [], [], [], $server);
        $this->assertSame('/index.php', $request->getScriptName());
    }

    public function testGetBaseUrl(): void
    {
        $request = new Request();
        $this->assertSame('', $request->getBaseUrl());
    }

    /**
     * @api(
     *     title="getBaseUrl 获取基础 URL",
     *     description="",
     *     note="",
     * )
     */
    public function testGetBaseUrl2(): void
    {
        $server = [];
        $server['SCRIPT_NAME'] = '/foo/index.php';
        $server['SCRIPT_FILENAME'] = '/bar/index.php';
        $server['REQUEST_URI'] = '/goods/info/index.php/foo/bar';
        $request = new Request([], [], [], [], [], $server);
        $this->assertSame('/foo/index.php/', $request->getBaseUrl());
    }

    public function testGetBaseUrl3(): void
    {
        $server = [];
        $server['SCRIPT_NAME'] = '/foo/index.php';
        $server['SCRIPT_FILENAME'] = '/bar/index.php';
        $server['REQUEST_URI'] = '/goods/foo/index.php/foo/bar';
        $request = new Request([], [], [], [], [], $server);
        $this->assertSame('/goods/foo/index.php/', $request->getBaseUrl());
    }

    public function testGetBaseUrl4(): void
    {
        $server = [];
        $server['SCRIPT_NAME'] = '/foo/index.php';
        $server['SCRIPT_FILENAME'] = '/bar/index.php';
        $server['REQUEST_URI'] = 'goods/foo/index.php/foo/bar';
        $request = new Request([], [], [], [], [], $server);
        $this->assertSame('/goods/foo/index.php/', $request->getBaseUrl());
    }

    /**
     * @api(
     *     title="getBasePath 获取基础路径",
     *     description="",
     *     note="",
     * )
     */
    public function testGetBasePath(): void
    {
        $request = new Request();
        $this->assertSame('', $request->getBasePath());
    }

    public function testGetBasePath2(): void
    {
        $server = [];
        $server['SCRIPT_FILENAME'] = '/some/where/index.php';
        $request = new Request([], [], [], [], [], $server);
        $this->assertSame('', $request->getBasePath());
    }

    public function testGetBasePath3(): void
    {
        $server = [];
        $server['SCRIPT_FILENAME'] = '/some/where/index.php';
        $server['SCRIPT_NAME'] = '/index.php';
        $request = new Request([], [], [], [], [], $server);
        $this->assertSame('', $request->getBasePath());
    }

    public function testGetBasePath4(): void
    {
        $server = [];
        $server['SCRIPT_FILENAME'] = '/some/where/index.php';
        $server['PHP_SELF'] = '/index.php';
        $request = new Request([], [], [], [], [], $server);
        $this->assertSame('', $request->getBasePath());
    }

    public function testGetBasePath5(): void
    {
        $server = [];
        $server['SCRIPT_FILENAME'] = '/some/where/index.php';
        $server['ORIG_SCRIPT_NAME'] = '/index.php';
        $request = new Request([], [], [], [], [], $server);
        $this->assertSame('', $request->getBasePath());
        $this->assertSame('', $request->getBasePath()); // 缓存
    }

    public function testGetBasePath6(): void
    {
        $server = [];
        $server['SCRIPT_NAME'] = '/foo/index.php';
        $server['SCRIPT_FILENAME'] = '/bar/index.php';
        $server['REQUEST_URI'] = '/goods/info/index.php/foo/bar';
        $request = new Request([], [], [], [], [], $server);
        $this->assertSame('/foo', $request->getBasePath());
        $this->assertSame('/foo', $request->getBasePath()); // 缓存
    }

    public function testGetBasePath7(): void
    {
        $server = [];
        $server['SCRIPT_NAME'] = '/foo/index.php';
        $server['SCRIPT_FILENAME'] = '/bar/hello.php';
        $server['REQUEST_URI'] = '/goods/info/index.php/foo/bar';
        $request = new Request([], [], [], [], [], $server);
        $this->assertSame('', $request->getBasePath());
        $this->assertSame('', $request->getBasePath()); // 缓存
    }

    public function testGetBasePath8(): void
    {
        $server = [];
        $server['SCRIPT_NAME'] = '/hello/foo/'.PHP_EOL.'ind'.PHP_EOL.'ex.php';
        $server['SCRIPT_FILENAME'] = '/hello/foo/'.PHP_EOL.'/ind'.PHP_EOL.'ex.php';
        $server['REQUEST_URI'] = '/foo/'.PHP_EOL.'/index.php/foo/bar';
        $request = new Request([], [], [], [], [], $server);
        $this->assertSame('', $request->getBasePath());
    }

    public function testGetPathInfo(): void
    {
        $request = new Request();
        $this->assertSame('/', $request->getPathInfo());
    }

    /**
     * @api(
     *     title="getPathInfo 获取 pathInfo",
     *     description="",
     *     note="",
     * )
     */
    public function testGetPathInfo2(): void
    {
        $server = [];
        $server['REQUEST_URI'] = '/path/info';
        $request = new Request([], [], [], [], [], $server);
        $this->assertSame('/path/info', $request->getPathInfo());
    }

    public function testGetPathInfo3(): void
    {
        $server = [];
        $server['REQUEST_URI'] = '/path%20test/info';
        $request = new Request([], [], [], [], [], $server);
        $this->assertSame('/path%20test/info', $request->getPathInfo());
    }

    public function testGetPathInfo4(): void
    {
        $server = [];
        $server['REQUEST_URI'] = '?a=b';
        $request = new Request([], [], [], [], [], $server);
        $this->assertSame('/', $request->getPathInfo());
    }

    public function testGetPathInfo5(): void
    {
        $server = [];
        $server['SCRIPT_NAME'] = '/foo999999999999999999999999999999999999/index.php';
        $server['SCRIPT_FILENAME'] = '/bar999999999999999999999999999999999999/index.php';
        $server['REQUEST_URI'] = '/goods/info/index.php/foo/bar?a=b';
        $request = new Request([], [], [], [], [], $server);
        $this->assertSame('/', $request->getPathInfo());
    }

    public function testGetPathInfo6(): void
    {
        $server = [];
        $server['SCRIPT_NAME'] = '/foo/index.php';
        $server['SCRIPT_FILENAME'] = '/bar/index.php';
        $server['REQUEST_URI'] = '/goods/info/index.php/foo/bar?a=b';
        $request = new Request([], [], [], [], [], $server);
        $this->assertSame('/ex.php/foo/bar', $request->getPathInfo());
    }

    public function testGetPathInfoWithExt(): void
    {
        $server = [];
        $server['REQUEST_URI'] = '/path/info.html';
        $request = new Request([], [], [], [], [], $server);
        $this->assertSame('/path/info', $request->getPathInfo());
    }

    /**
     * @api(
     *     title="getRequestUri 获取请求参数",
     *     description="",
     *     note="",
     * )
     */
    public function testGetRequestUri(): void
    {
        $server = [];
        $server['REQUEST_URI'] = '/goods/info/index.php/foo/bar';
        $request = new Request([], [], [], [], [], $server);
        $this->assertSame('/goods/info/index.php/foo/bar', $request->getRequestUri());
    }

    public function testGetRequestUriWithQueryString(): void
    {
        $server = [];
        $server['REQUEST_URI'] = '/goods/info/index.php/foo/bar?foo=bar';
        $request = new Request([], [], [], [], [], $server);
        $this->assertSame('/goods/info/index.php/foo/bar?foo=bar', $request->getRequestUri());
    }

    public function testGetRequestUri2(): void
    {
        $server = [];
        $server['ORIG_PATH_INFO'] = '/goods/info/index.php/foo/bar';
        $request = new Request([], [], [], [], [], $server);
        $this->assertSame('/goods/info/index.php/foo/bar', $request->getRequestUri());
    }

    public function testGetRequestUriWithQueryString2(): void
    {
        $server = [];
        $server['ORIG_PATH_INFO'] = '/goods/info/index.php/foo/bar';
        $server['QUERY_STRING'] = 'foo=bar';
        $request = new Request([], [], [], [], [], $server);
        $this->assertSame('/goods/info/index.php/foo/bar?foo=bar', $request->getRequestUri());
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

        // (isAjax && ! isPjax) && isAcceptAny
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
     *     title="get 获取参数",
     *     description="获取参数支持优先权，优先权依次为 `query`、`params`、`request`。",
     *     note="",
     * )
     */
    public function testGetParamPrecedence(): void
    {
        $request = new Request();
        $request->params->set('foo', 'attr');
        $request->query->set('foo', 'query');
        $request->request->set('foo', 'body');
        $this->assertSame('query', $request->get('foo'));

        $request->query->remove('foo');
        $this->assertSame('attr', $request->get('foo'));

        $request->params->remove('foo');
        $this->assertNull($request->get('foo'));
    }

    /**
     * @api(
     *     title="isXmlHttpRequest 是否为 Ajax 请求行为真实",
     *     description="",
     *     note="",
     * )
     */
    public function testIsXmlHttpRequest(): void
    {
        $request = new Request();
        $this->assertFalse($request->isXmlHttpRequest());

        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $this->assertTrue($request->isXmlHttpRequest());

        $request->headers->remove('X-Requested-With');
        $this->assertFalse($request->isXmlHttpRequest());
    }

    /**
     * @api(
     *     title="isMethod 是否为指定的方法",
     *     description="",
     *     note="",
     * )
     */
    public function testIsMethod(): void
    {
        $request = new Request();
        $request->setMethod('POST');
        $this->assertTrue($request->isMethod('POST'));
        $this->assertTrue($request->isMethod('post'));
        $this->assertFalse($request->isMethod('GET'));
        $this->assertFalse($request->isMethod('get'));

        $request->setMethod('GET');
        $this->assertTrue($request->isMethod('GET'));
        $this->assertTrue($request->isMethod('get'));
        $this->assertFalse($request->isMethod('POST'));
        $this->assertFalse($request->isMethod('post'));
    }

    /**
     * @api(
     *     title="魔术方法 __isset 支持",
     *     description="",
     *     note="",
     * )
     */
    public function testMagicIsset(): void
    {
        $request = new Request(['foo' => 'bar']);
        $this->assertTrue(isset($request->foo));
        $this->assertFalse(isset($request->helloNot));
    }

    /**
     * @api(
     *     title="魔术方法 __get 支持",
     *     description="",
     *     note="",
     * )
     */
    public function testMagicGet(): void
    {
        $request = new Request(['foo' => 'bar']);
        $this->assertSame('bar', $request->foo);
    }

    public function testMagicGetNotFound(): void
    {
        $request = new Request(['foo' => 'bar']);
        $this->assertNull($request->helloNot);
    }

    public function testCoroutineContext(): void
    {
        $this->assertTrue(Request::coroutineContext());
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
     *     title="has 请求是否包含非空",
     *     description="",
     *     note="",
     * )
     */
    public function testHas(): void
    {
        $request = new Request(['foo' => 'bar', 'hello' => 'world', 'e' => '']);
        $this->assertTrue($request->has(['foo']));
        $this->assertTrue($request->has(['foo', 'hello']));
        $this->assertTrue($request->has(['notFound']));
        $this->assertFalse($request->has(['e']));
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
     *     title="input 获取输入数据",
     *     description="",
     *     note="",
     * )
     */
    public function testInput(): void
    {
        $request = new Request(['foo' => 'bar', 'hello' => 'world']);
        $this->assertSame('bar', $request->input('foo'));
        $this->assertNull($request->input('not'));
        $this->assertSame('world', $request->input('hello'));
    }

    /**
     * @api(
     *     title="input 获取所有输入数据",
     *     description="",
     *     note="",
     * )
     */
    public function testInputAll(): void
    {
        $request = new Request(['foo' => 'bar', 'hello' => 'world']);
        $this->assertSame(['foo' => 'bar', 'hello' => 'world'], $request->input());
    }

    /**
     * @api(
     *     title="input 获取输入数据支持默认值",
     *     description="如果输入值为 `null`，那么将会返回默认值。",
     *     note="",
     * )
     */
    public function testInputNullWithDefault(): void
    {
        $request = new Request(['foo' => 'bar', 'hello' => 'world']);
        $this->assertSame('default', $request->input('not', 'default'));
    }

    /**
     * @api(
     *     title="query 获取 query",
     *     description="",
     *     note="",
     * )
     */
    public function testQuery(): void
    {
        $request = new Request(['foo' => 'bar', 'hello' => 'world']);
        $this->assertSame('bar', $request->query('foo'));
        $this->assertNull($request->query('not'));
        $this->assertSame('world', $request->query('hello'));
    }

    /**
     * @api(
     *     title="query 获取所有 query",
     *     description="",
     *     note="",
     * )
     */
    public function testQueryAll(): void
    {
        $request = new Request(['foo' => 'bar', 'hello' => 'world']);
        $this->assertSame(['foo' => 'bar', 'hello' => 'world'], $request->query());
    }

    /**
     * @api(
     *     title="query 获取 query 支持默认值",
     *     description="如果 query 值为 `null`，那么将会返回默认值。",
     *     note="",
     * )
     */
    public function testQueryNullWithDefault(): void
    {
        $request = new Request(['foo' => 'bar', 'hello' => 'world']);
        $this->assertSame('default', $request->query('not', 'default'));
    }

    /**
     * @api(
     *     title="cookie 获取 cookie",
     *     description="",
     *     note="",
     * )
     */
    public function testCookie(): void
    {
        $request = new Request([], [], [], ['foo' => 'bar', 'hello' => 'world']);
        $this->assertSame('bar', $request->cookie('foo'));
        $this->assertNull($request->cookie('not'));
        $this->assertSame('world', $request->cookie('hello'));
    }

    /**
     * @api(
     *     title="cookie 获取所有 cookie",
     *     description="",
     *     note="",
     * )
     */
    public function testCookieAll(): void
    {
        $request = new Request([], [], [], ['foo' => 'bar', 'hello' => 'world']);
        $this->assertSame(['foo' => 'bar', 'hello' => 'world'], $request->cookie());
    }

    /**
     * @api(
     *     title="query 获取 cookie 支持默认值",
     *     description="如果 cookie 值为 `null`，那么将会返回默认值。",
     *     note="",
     * )
     */
    public function testCookieNullWithDefault(): void
    {
        $request = new Request([], [], [], ['foo' => 'bar', 'hello' => 'world']);
        $this->assertSame('default', $request->cookie('not', 'default'));
    }

    /**
     * @api(
     *     title="hasCookie 请求是否存在 COOKIE",
     *     description="",
     *     note="",
     * )
     */
    public function testHasCookie(): void
    {
        $request = new Request([], [], [], ['foo' => 'bar', 'hello' => 'world']);
        $this->assertTrue($request->hasCookie('foo'));
        $this->assertFalse($request->hasCookie('not'));
    }

    /**
     * @api(
     *     title="file 获取文件",
     *     description="",
     *     note="",
     * )
     */
    public function testFile(): void
    {
        $tmpFile = $this->createTempFile();

        $file = new UploadedFile($tmpFile, basename($tmpFile), 'text/plain');

        $files = [
            'file' => [
                'name'     => basename($tmpFile),
                'type'     => 'text/plain',
                'tmp_name' => $tmpFile,
                'error'    => 0,
                'size'     => null,
            ],
        ];

        $request = new Request([], [], [], [], $files);
        $this->assertInstanceOf(UploadedFile::class, $request->file('file'));
        $this->assertEquals($file, $request->file('file'));
        $this->assertNull($request->file('not'));
    }

    /**
     * @api(
     *     title="file 获取所有文件",
     *     description="",
     *     note="",
     * )
     */
    public function testFileAll(): void
    {
        $tmpFile = $this->createTempFile();

        $file = new UploadedFile($tmpFile, basename($tmpFile), 'text/plain');

        $files = [
            'file' => [
                'name'     => basename($tmpFile),
                'type'     => 'text/plain',
                'tmp_name' => $tmpFile,
                'error'    => 0,
                'size'     => null,
            ],
        ];

        $request = new Request([], [], [], [], $files);
        $files = $request->file();
        $this->assertInstanceOf(UploadedFile::class, $files['file']);
        $this->assertEquals($file, $files['file']);
        $this->assertIsArray($files);
        $this->assertCount(1, $files);
    }

    /**
     * @api(
     *     title="hasFile 文件是否存在已上传的文件",
     *     description="",
     *     note="",
     * )
     */
    public function testHasFile(): void
    {
        $tmpFile = $this->createTempFile();

        $file = new UploadedFile($tmpFile, basename($tmpFile), 'text/plain');

        $files = [
            'file' => [
                'name'     => basename($tmpFile),
                'type'     => 'text/plain',
                'tmp_name' => $tmpFile,
                'error'    => 0,
                'size'     => null,
            ],
        ];

        $request = new Request([], [], [], [], $files);
        $this->assertTrue($request->hasFile('file'));
        $this->assertEquals($file, $request->file('file'));
        $this->assertFalse($request->hasFile('not'));
    }

    /**
     * @api(
     *     title="file 获取多个文件",
     *     description="",
     *     note="",
     * )
     */
    public function testMultiFile(): void
    {
        $tmpFile = $this->createTempFile();
        $tmpFile2 = $this->createTempFile();
        $file = new UploadedFile($tmpFile, basename($tmpFile), 'text/plain');
        $file2 = new UploadedFile($tmpFile2, basename($tmpFile2), 'text/plain');

        $files = [
            'file' => [
                'name'     => [basename($tmpFile), basename($tmpFile2)],
                'type'     => ['text/plain', 'text/plain'],
                'tmp_name' => [$tmpFile, $tmpFile2],
                'error'    => [0, 0],
                'size'     => [null, null],
            ],
        ];

        $request = new Request([], [], [], [], $files);
        $files = $request->file();
        $this->assertInstanceOf(UploadedFile::class, $files['file\\0']);
        $this->assertInstanceOf(UploadedFile::class, $files['file\\1']);
        $this->assertEquals($file, $files['file\\0']);
        $this->assertEquals($file2, $files['file\\1']);
        $this->assertIsArray($files);
        $this->assertCount(2, $files);
    }

    /**
     * @api(
     *     title="file 获取子文件",
     *     description="",
     *     note="",
     * )
     */
    public function testMultiFileGetArr(): void
    {
        $tmpFile = $this->createTempFile();
        $tmpFile2 = $this->createTempFile();
        $file = new UploadedFile($tmpFile, basename($tmpFile), 'text/plain');
        $file2 = new UploadedFile($tmpFile2, basename($tmpFile2), 'text/plain');

        $files = [
            'file' => [
                'name'     => [basename($tmpFile), basename($tmpFile2)],
                'type'     => ['text/plain', 'text/plain'],
                'tmp_name' => [$tmpFile, $tmpFile2],
                'error'    => [0, 0],
                'size'     => [null, null],
            ],
        ];

        $request = new Request([], [], [], [], $files);
        $files = $request->file('file\\');
        $this->assertInstanceOf(UploadedFile::class, $files[0]);
        $this->assertInstanceOf(UploadedFile::class, $files[1]);
        $this->assertInstanceOf(UploadedFile::class, $request->file('file\\0')[0]);
        $this->assertInstanceOf(UploadedFile::class, $request->file('file\\1')[0]);
        $this->assertEquals($file, $files[0]);
        $this->assertEquals($file2, $files[1]);
        $this->assertIsArray($files);
        $this->assertCount(2, $files);
    }

    /**
     * @api(
     *     title="header 获取响应头",
     *     description="",
     *     note="",
     * )
     */
    public function testHeader(): void
    {
        $request = new Request([], [], [], [], [], ['HTTP_HOST' => '127.0.0.1', 'HTTP_REFERER' => 'https://www.queryphp.com']);
        $this->assertSame('127.0.0.1', $request->header('host'));
        $this->assertSame('127.0.0.1', $request->header('HOST'));
        $this->assertSame('https://www.queryphp.com', $request->header('referer'));
        $this->assertSame('https://www.queryphp.com', $request->header('REFERER'));
    }

    /**
     * @api(
     *     title="header 获取所有响应头",
     *     description="",
     *     note="",
     * )
     */
    public function testHeaderAll(): void
    {
        $request = new Request([], [], [], [], [], ['HTTP_HOST' => '127.0.0.1', 'HTTP_REFERER' => 'https://www.queryphp.com']);
        $this->assertSame(['host' => '127.0.0.1', 'referer' => 'https://www.queryphp.com'], $request->header());
    }

    /**
     * @api(
     *     title="header 获取响应头支持默认值",
     *     description="如果 header 值为 `null`，那么将会返回默认值。",
     *     note="",
     * )
     */
    public function testHeaderNullWithDefault(): void
    {
        $request = new Request([], [], [], [], [], ['HTTP_HOST' => '127.0.0.1', 'HTTP_REFERER' => 'https://www.queryphp.com']);
        $this->assertNull($request->header('notFound'));
        $this->assertSame('default', $request->header('notFound', 'default'));
    }

    /**
     * @api(
     *     title="server 获取 server",
     *     description="",
     *     note="",
     * )
     */
    public function testServer(): void
    {
        $request = new Request([], [], [], [], [], ['HTTP_HOST' => '127.0.0.1', 'HTTP_REFERER' => 'https://www.queryphp.com']);
        $this->assertSame('127.0.0.1', $request->server('HTTP_HOST'));
        $this->assertSame('https://www.queryphp.com', $request->server('HTTP_REFERER'));
    }

    /**
     * @api(
     *     title="server 获取所有 server",
     *     description="",
     *     note="",
     * )
     */
    public function testServerAll(): void
    {
        $request = new Request([], [], [], [], [], ['HTTP_HOST' => '127.0.0.1', 'HTTP_REFERER' => 'https://www.queryphp.com']);
        $this->assertSame(['HTTP_HOST' => '127.0.0.1', 'HTTP_REFERER' => 'https://www.queryphp.com'], $request->server());
    }

    /**
     * @api(
     *     title="server 获取 server 支持默认值",
     *     description="如果 server 值为 `null`，那么将会返回默认值。",
     *     note="",
     * )
     */
    public function testServiceNullWithDefault(): void
    {
        $request = new Request([], [], [], [], [], ['HTTP_HOST' => '127.0.0.1', 'HTTP_REFERER' => 'https://www.queryphp.com']);
        $this->assertNull($request->server('notFound'));
        $this->assertSame('default', $request->server('notFound', 'default'));
    }

    /**
     * @api(
     *     title="merge 合并输入",
     *     description="",
     *     note="",
     * )
     */
    public function testMerge(): void
    {
        $request = new Request(['foo' => 'bar']);
        $this->assertSame(['foo' => 'bar'], $request->input());
        $request->merge(['hello' => 'world']);
        $this->assertSame(['foo' => 'bar', 'hello' => 'world'], $request->input());
    }

    /**
     * @api(
     *     title="replace 替换输入",
     *     description="",
     *     note="",
     * )
     */
    public function testReplace(): void
    {
        $request = new Request(['foo' => 'bar']);
        $this->assertSame(['foo' => 'bar'], $request->input());
        $request->replace(['hello' => 'world']);
        $this->assertSame(['hello' => 'world'], $request->input());
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
     *     title="isGet 是否为 GET 请求行为",
     *     description="",
     *     note="",
     * )
     */
    public function testIsGet(): void
    {
        $request = new Request();
        $this->assertTrue($request->isGet());
    }

    public function testIsGetWillReturnTrue(): void
    {
        $request = new Request();
        $request->setMethod(IRequest::METHOD_GET);
        $this->assertTrue($request->isGet());
    }

    /**
     * @dataProvider provideIsMethod
     *
     * @param mixed $method
     */
    public function testIsMethodCheck(string $method): void
    {
        $request = new Request();
        $isMethod = 'is'.ucfirst($method);
        $this->assertFalse($request->{$isMethod}());
    }

    /**
     * @dataProvider provideIsMethod
     *
     * @param mixed $method
     *
     * @api(
     *     title="setMethod 改变请求行为",
     *     description="
     * **测试类型**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Http\RequestTest::class, 'provideIsMethod')]}
     * ```
     * ",
     *     note="",
     * )
     */
    public function testIsMethodCheckWillReturnTrue(string $method): void
    {
        $request = new Request();
        $isMethod = 'is'.ucfirst($method);
        $constMethod = 'METHOD_'.strtoupper($method);
        $request->setMethod(constant(IRequest::class.'::'.$constMethod));
        $this->assertTrue($request->{$isMethod}());
    }

    public function provideIsMethod()
    {
        return [
            ['put'],
            ['patch'],
            ['post'],
            ['head'],
            ['options'],
            ['purge'],
            ['trace'],
            ['connect'],
        ];
    }

    /**
     * @api(
     *     title="getClientIp 获取 IP 地址",
     *     description="",
     *     note="",
     * )
     */
    public function testGetClientIp(): void
    {
        $request = new Request([], [], [], [], [], ['HTTP_CLIENT_IP' => '127.0.0.1']);
        $this->assertSame('127.0.0.1', $request->getClientIp());
    }

    public function testGetClientIpWithRemoteAddr(): void
    {
        $request = new Request([], [], [], [], [], ['REMOTE_ADDR' => '127.0.0.1']);
        $this->assertSame('127.0.0.1', $request->getClientIp());
    }

    public function testGetClientIpWithDefault(): void
    {
        $request = new Request();
        $this->assertSame('0.0.0.0', $request->getClientIp());
    }

    /**
     * @api(
     *     title="getRealMethod 实际请求类型",
     *     description="",
     *     note="",
     * )
     */
    public function testGetRealMethod(): void
    {
        $request = new Request();
        $this->assertSame('GET', $request->getRealMethod());
    }

    /**
     * @api(
     *     title="getContent 取得请求内容",
     *     description="",
     *     note="",
     * )
     */
    public function testGetContent(): void
    {
        $request = new Request([], [], [], [], [], [], 'helloworld');
        $this->assertSame('helloworld', $request->getContent());
    }

    public function testGetContentFromPhpInput(): void
    {
        $request = new Request();
        $this->assertSame('', $request->getContent());
    }

    public function testGetContentFromResource(): void
    {
        $file = __DIR__.'/testGetContentFromResource.txt';
        file_put_contents($file, 'hello');
        $text = fopen($file, 'r');
        $this->assertIsResource($text);
        $request = new Request([], [], [], [], [], [], $text);
        $this->assertSame('hello', $request->getContent());
        unlink($file);
    }

    /**
     * @api(
     *     title="getRoot 获取根 URL",
     *     description="",
     *     note="",
     * )
     */
    public function testGetRoot(): void
    {
        $request = new Request([], [], [], [], [], ['SERVER_NAME' => 'servername', 'SERVER_PORT' => '90']);
        $this->assertSame('http://servername:90', $request->getRoot());
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

    /**
     * @api(
     *     title="数组访问 ArrayAccess.offsetExists 支持",
     *     description="",
     *     note="",
     * )
     */
    public function testOffsetExists(): void
    {
        $request = new Request(['foo' => 'bar', 'hello' => 'world']);
        $this->assertTrue(isset($request['foo']));
        $this->assertFalse(isset($request['notfound']));
    }

    /**
     * @api(
     *     title="数组访问 ArrayAccess.offsetGet 支持",
     *     description="",
     *     note="",
     * )
     */
    public function testOffsetGet(): void
    {
        $request = new Request(['foo' => 'bar', 'hello' => 'world']);
        $this->assertSame('bar', $request['foo']);
        $this->assertNull($request['notfound']);
    }

    /**
     * @api(
     *     title="数组访问 ArrayAccess.offsetSet 支持",
     *     description="",
     *     note="",
     * )
     */
    public function testOffsetSet(): void
    {
        $request = new Request(['foo' => 'bar', 'hello' => 'world']);
        $this->assertSame('bar', $request['foo']);
        $request['foo'] = 'newbar';
        $this->assertSame('newbar', $request['foo']);
    }

    /**
     * @api(
     *     title="数组访问 ArrayAccess.offsetUnset 支持",
     *     description="",
     *     note="",
     * )
     */
    public function testOffsetUnset(): void
    {
        $request = new Request(['foo' => 'bar', 'hello' => 'world']);
        $this->assertSame('bar', $request['foo']);
        unset($request['foo']);
        $this->assertNull($request['foo']);
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
     *     title="isSecure 是否启用 https",
     *     description="",
     *     note="",
     * )
     */
    public function testIsSecure(): void
    {
        $request = new Request();

        $this->assertFalse($request->isSecure());
        $request->server->set('HTTPS', 'on');
        $this->assertTrue($request->isSecure());
    }

    public function testIsSecure2(): void
    {
        $request = new Request();

        $this->assertFalse($request->isSecure());
        $request->server->set('HTTPS', '1');
        $this->assertTrue($request->isSecure());
    }

    public function testIsSecure3(): void
    {
        $request = new Request();

        $this->assertFalse($request->isSecure());
        $request->server->set('HTTPS', 1);
        $this->assertTrue($request->isSecure());
    }

    public function testIsSecureByPort(): void
    {
        $request = new Request();

        $this->assertFalse($request->isSecure());
        $request->server->set('SERVER_PORT', 443);
        $this->assertTrue($request->isSecure());
    }

    public function testGetUrlEncodedPrefix(): void
    {
        $request = new Request();
        $value = '/hello/'.PHP_EOL.'/index.php';
        $prefix = '/hello/'.PHP_EOL;
        $this->assertFalse($this->invokeTestMethod($request, 'getUrlEncodedPrefix', [$value, $prefix]));
    }

    protected function createTempFile(): string
    {
        $tempFile = sys_get_temp_dir().'/form_test/'.md5(time().rand()).'.tmp';
        file_put_contents($tempFile, '1');

        return $tempFile;
    }
}

class RequestContentProxy extends Request
{
    /**
     * 全局变量创建一个 Request.
     *
     * @return static
     */
    public static function createFromGlobals(): IRequest
    {
        $request = new static($_GET, $_POST, [], $_COOKIE, $_FILES, $_SERVER, null);
        $request = static::normalizeRequestFromContent($request);

        return $request;
    }

    public function getContent(): string
    {
        return http_build_query([IRequest::VAR_METHOD => 'PUT', 'content' => 'mycontent'], '', '&');
    }
}

class RequestContentApplicationJson extends Request
{
    /**
     * 全局变量创建一个 Request.
     *
     * @return static
     */
    public static function createFromGlobals(): IRequest
    {
        $request = new static($_GET, $_POST, [], $_COOKIE, $_FILES, $_SERVER, null);
        $request = static::normalizeRequestFromContent($request);

        return $request;
    }

    public function getContent(): string
    {
        return '{"name":"admin","password":"123456"}';
    }
}
