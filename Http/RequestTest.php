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

use Leevel\Http\Request;
use Tests\TestCase;

/**
 * Request test
 * This class borrows heavily from the Symfony4 Framework and is part of the symfony package.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.03.09
 *
 * @version 1.0
 *
 * @see Symfony\Component\HttpFoundation (https://github.com/symfony/symfony)
 */
class RequestTest extends TestCase
{
    /**
     * 初始化.
     */
    protected function setUp()
    {
    }

    /**
     * 测试 reset 方法.
     */
    public function testReset()
    {
        $request = new Request();
        $request->reset(['foo' => 'bar']);
        $this->assertSame('bar', $request->query->get('foo'), '->reset() takes an array of query parameters as its first argument');

        $request->reset([], ['foo' => 'bar']);
        $this->assertSame('bar', $request->request->get('foo'), '->reset() takes an array of request parameters as its second argument');

        $request->reset([], [], ['foo' => 'bar']);
        $this->assertSame('bar', $request->params->get('foo'), '->reset() takes an array of params as its third argument');

        $request->reset([], [], [], [], [], ['HTTP_FOO' => 'bar']);
        $this->assertSame('bar', $request->headers->get('FOO'), '->reset() takes an array of HTTP headers as its sixth argument');
    }

    /**
     * 测试 language 方法.
     */
    public function testLanguage()
    {
        $request = new Request();

        $request->setLanguage('zh-cn');

        $this->assertSame($request->getLanguage(), 'zh-cn');
    }

    /**
     * 测试 getUri 方法.
     */
    public function testGetUri()
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
     * 测试 getSchemeAndHttpHost 方法.
     */
    public function testGetSchemeAndHttpHost()
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
    public function testGetQueryString($query, $expectedQuery, $msg)
    {
        $request = new Request();
        $request->server->set('QUERY_STRING', $query);
        $this->assertSame($expectedQuery, $request->getQueryString(), $msg);
    }

    public function getQueryStringNormalizationData()
    {
        return [
            ['foo', 'foo', 'works with valueless parameters'],
            ['foo=', 'foo=', 'includes a dangling equal sign'],
            ['bar=&foo=bar', 'bar=&foo=bar', '->works with empty parameters'],
            ['foo=bar&bar=', 'foo=bar&bar=', ''],
            ['him=John%20Doe&her=Jane+Doe', 'him=John%20Doe&her=Jane+Doe', ''],
            ['foo[]=1&foo[]=2', 'foo[]=1&foo[]=2', 'allows array notation'],
            ['foo=1&foo=2', 'foo=1&foo=2', 'allows repeated parameters'],
            ['pa%3Dram=foo%26bar%3Dbaz&test=test', 'pa%3Dram=foo%26bar%3Dbaz&test=test', 'works with encoded delimiters'],
            ['0', '0', 'allows "0"'],
            ['Jane Doe&John%20Doe', 'Jane Doe&John%20Doe', ''],
            ['her=Jane Doe&him=John%20Doe', 'her=Jane Doe&him=John%20Doe', ''],
            ['foo=bar&&&test&&', 'foo=bar&test', 'removes unneeded delimiters'],
            ['formula=e=m*c^2', 'formula=e=m*c^2', ''],
            // remove _url 用于 nginx url 重写
            ['foo=bar&_url=/hello/world', 'foo=bar', ''],
        ];
    }

    public function testGetQueryStringReturnsNull()
    {
        $request = new Request();
        $this->assertNull($request->getQueryString(), '->getQueryString() returns null for non-existent query string');

        $request->server->set('QUERY_STRING', '');
        $this->assertNull($request->getQueryString(), '->getQueryString() returns null for empty query string');
    }

    public function testGetHost()
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

    public function testGetPort()
    {
        $request = new Request([], [], [], [], [], [
            'HTTP_X_FORWARDED_PROTO' => 'https',
            'HTTP_X_FORWARDED_PORT'  => '443',
        ]);
        $port = $request->getPort();
        $this->assertSame(80, $port, 'Without trusted proxies FORWARDED_PROTO and FORWARDED_PORT are ignored.');
    }

    public function testGetSetMethod()
    {
        $request = new Request();
        $this->assertSame('GET', $request->getMethod(), '->getMethod() returns GET if no method is defined');
        $request->setMethod('get');
        $this->assertSame('GET', $request->getMethod(), '->getMethod() returns an uppercased string');
        $request->setMethod('PURGE');
        $this->assertSame('PURGE', $request->getMethod(), '->getMethod() returns the method even if it is not a standard one');
        $request->setMethod('POST');
        $this->assertSame('POST', $request->getMethod(), '->getMethod() returns the method POST if no _method is defined');
        $request->setMethod('POST');
        $request->request->set('_method', 'purge');
        $this->assertSame('PURGE', $request->getMethod(), '->getMethod() does not return the method from _method if defined and POST but support not enabled');

        $request = new Request();
        $request->setMethod('POST');
        $request->request->set('_method', 'purge');
        $this->assertTrue('PURGE' === $request->getMethod(), '');

        $request = new Request();
        $request->setMethod('POST');
        $request->headers->set('X-HTTP-METHOD-OVERRIDE', 'delete');
        $this->assertSame('DELETE', $request->getMethod(), '->getMethod() returns the method from X-HTTP-Method-Override even though _method is set if defined and POST');
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
     * @dataProvider provideOverloadedMethods
     *
     * @param mixed $method
     */
    public function testCreateFromGlobals($method)
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

    public function testGetScriptName()
    {
        $request = new Request();
        $this->assertSame('', $request->getScriptName());

        $server = [];
        $server['SCRIPT_NAME'] = '/index.php';
        $request->reset([], [], [], [], [], $server);
        $this->assertSame('/index.php', $request->getScriptName());

        $server = [];
        $server['ORIG_SCRIPT_NAME'] = '/frontend.php';
        $request->reset([], [], [], [], [], $server);
        $this->assertSame('/frontend.php', $request->getScriptName());

        $server = [];
        $server['SCRIPT_NAME'] = '/index.php';
        $server['ORIG_SCRIPT_NAME'] = '/frontend.php';
        $request->reset([], [], [], [], [], $server);
        $this->assertSame('/index.php', $request->getScriptName());
    }

    public function testGetBasePath()
    {
        $request = new Request();
        $this->assertSame('', $request->getBasePath());

        $server = [];
        $server['SCRIPT_FILENAME'] = '/some/where/index.php';
        $request->reset([], [], [], [], [], $server);
        $this->assertSame('', $request->getBasePath());

        $server = [];
        $server['SCRIPT_FILENAME'] = '/some/where/index.php';
        $server['SCRIPT_NAME'] = '/index.php';
        $request->reset([], [], [], [], [], $server);
        $this->assertSame('', $request->getBasePath());

        $server = [];
        $server['SCRIPT_FILENAME'] = '/some/where/index.php';
        $server['PHP_SELF'] = '/index.php';
        $request->reset([], [], [], [], [], $server);
        $this->assertSame('', $request->getBasePath());

        $server = [];
        $server['SCRIPT_FILENAME'] = '/some/where/index.php';
        $server['ORIG_SCRIPT_NAME'] = '/index.php';
        $request->reset([], [], [], [], [], $server);
        $this->assertSame('', $request->getBasePath());
    }

    public function testGetPathInfo()
    {
        $request = new Request();
        $this->assertSame('/', $request->getPathInfo());

        $server = [];
        $server['REQUEST_URI'] = '/path/info';
        $request->reset([], [], [], [], [], $server);
        $this->assertSame('/path/info', $request->getPathInfo());

        $server = [];
        $server['REQUEST_URI'] = '/path%20test/info';
        $request->reset([], [], [], [], [], $server);
        $this->assertSame('/path%20test/info', $request->getPathInfo());

        $server = [];
        $server['REQUEST_URI'] = '?a=b';
        $request->reset([], [], [], [], [], $server);
        $this->assertSame('/', $request->getPathInfo());
    }

    public function testIsJson()
    {
        $request = new Request();

        $this->assertFalse($request->isJson());
        $this->assertFalse($request->isRealJson());

        $request->query->set(Request::VAR_JSON, '1');
        $this->assertTrue($request->isJson());
        $this->assertFalse($request->isRealJson());
    }

    public function testIsAcceptAny()
    {
        $request = new Request();

        $this->assertTrue($request->isAcceptAny());

        $request->headers->set('accept', 'application/json');
        $this->assertFalse($request->isAcceptAny());

        $request->headers->set('accept', '*/*');
        $this->assertTrue($request->isAcceptAny());
    }

    public function testIsAcceptJson()
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

    public function testGetParameterPrecedence()
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

    public function testIsXmlHttpRequest()
    {
        $request = new Request();
        $this->assertFalse($request->isXmlHttpRequest());

        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $this->assertTrue($request->isXmlHttpRequest());

        $request->headers->remove('X-Requested-With');
        $this->assertFalse($request->isXmlHttpRequest());
    }

    public function testIsMethod()
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
}

class RequestContentProxy extends Request
{
    /**
     * 全局变量创建一个 Request.
     *
     * @return static
     */
    public static function createFromGlobals()
    {
        $request = new static($_GET, $_POST, [], $_COOKIE, $_FILES, $_SERVER, null);

        $request = static::normalizeRequestFromContent($request);

        return $request;
    }

    public function getContent()
    {
        return http_build_query(['_method' => 'PUT', 'content' => 'mycontent'], '', '&');
    }
}
