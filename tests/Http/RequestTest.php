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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
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
    protected function setUp()
    {
        $dir = sys_get_temp_dir().'/form_test';

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    protected function tearDown()
    {
        foreach (glob(sys_get_temp_dir().'/form_test/*') as $file) {
            unlink($file);
        }

        rmdir(sys_get_temp_dir().'/form_test');
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

    public function testLanguageAlia()
    {
        $request = new Request();

        $request->setLanguage('zh-cn');

        $this->assertSame($request->language(), 'zh-cn');
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

    /**
     * @dataProvider provideOverloadedMethods2
     *
     * @param mixed $method
     */
    public function testCreateFromGlobalWithApplicationJson($method)
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
        $this->assertSame('', $request->getBasePath()); // 缓存
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

    public function testGetPathInfoWithExt()
    {
        $server = [];
        $server['REQUEST_URI'] = '/path/info.html';
        $request = new Request([], [], [], [], [], $server);
        $this->assertSame('/path/info', $request->getPathInfo());
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

    public function testMagicIsset()
    {
        $request = new Request(['foo' => 'bar']);
        $this->assertTrue(isset($request->foo));
        $this->assertFalse(isset($request->helloNot));
    }

    public function testMagicGet()
    {
        $request = new Request(['foo' => 'bar']);
        $this->assertSame('bar', $request->foo);
    }

    public function testMagicGetNotFound()
    {
        $request = new Request(['foo' => 'bar']);
        $this->assertNull($request->helloNot);
    }

    public function testCoroutineContext()
    {
        $this->assertTrue(Request::coroutineContext());
    }

    public function testExists()
    {
        $request = new Request(['foo' => 'bar', 'hello' => 'world']);
        $this->assertTrue($request->exists(['foo']));
        $this->assertTrue($request->exists(['foo', 'hello']));
        $this->assertFalse($request->exists(['notFound']));
    }

    public function testHas()
    {
        $request = new Request(['foo' => 'bar', 'hello' => 'world', 'e' => '']);
        $this->assertTrue($request->has(['foo']));
        $this->assertTrue($request->has(['foo', 'hello']));
        $this->assertTrue($request->has(['notFound']));
        $this->assertFalse($request->has(['e']));
    }

    public function testOnly()
    {
        $request = new Request(['foo' => 'bar', 'hello' => 'world']);
        $this->assertSame(['foo' => 'bar'], $request->only(['foo']));
        $this->assertSame(['foo' => 'bar', 'hello' => 'world'], $request->only(['foo', 'hello']));
        $this->assertSame(['foo' => 'bar', 'not' => null], $request->only(['foo', 'not']));
    }

    public function testExcept()
    {
        $request = new Request(['foo' => 'bar', 'hello' => 'world']);
        $this->assertSame(['hello' => 'world'], $request->except(['foo']));
        $this->assertSame([], $request->except(['foo', 'hello']));
        $this->assertSame(['hello' => 'world'], $request->except(['foo', 'not']));
    }

    public function testInput()
    {
        $request = new Request(['foo' => 'bar', 'hello' => 'world']);
        $this->assertSame('bar', $request->input('foo'));
        $this->assertNull($request->input('not'));
        $this->assertSame('world', $request->input('hello'));
    }

    public function testInputAll()
    {
        $request = new Request(['foo' => 'bar', 'hello' => 'world']);
        $this->assertSame(['foo' => 'bar', 'hello' => 'world'], $request->input());
    }

    public function testInputNullWithDefault()
    {
        $request = new Request(['foo' => 'bar', 'hello' => 'world']);
        $this->assertSame('default', $request->input('not', 'default'));
    }

    public function testQuery()
    {
        $request = new Request(['foo' => 'bar', 'hello' => 'world']);
        $this->assertSame('bar', $request->query('foo'));
        $this->assertNull($request->query('not'));
        $this->assertSame('world', $request->query('hello'));
    }

    public function testQueryAll()
    {
        $request = new Request(['foo' => 'bar', 'hello' => 'world']);
        $this->assertSame(['foo' => 'bar', 'hello' => 'world'], $request->query());
    }

    public function testQueryNullWithDefault()
    {
        $request = new Request(['foo' => 'bar', 'hello' => 'world']);
        $this->assertSame('default', $request->query('not', 'default'));
    }

    public function testCookie()
    {
        $request = new Request([], [], [], ['foo' => 'bar', 'hello' => 'world']);
        $this->assertSame('bar', $request->cookie('foo'));
        $this->assertNull($request->cookie('not'));
        $this->assertSame('world', $request->cookie('hello'));
    }

    public function testCookieAll()
    {
        $request = new Request([], [], [], ['foo' => 'bar', 'hello' => 'world']);
        $this->assertSame(['foo' => 'bar', 'hello' => 'world'], $request->cookie());
    }

    public function testCookieNullWithDefault()
    {
        $request = new Request([], [], [], ['foo' => 'bar', 'hello' => 'world']);
        $this->assertSame('default', $request->cookie('not', 'default'));
    }

    public function testHasCookie()
    {
        $request = new Request([], [], [], ['foo' => 'bar', 'hello' => 'world']);
        $this->assertTrue($request->hasCookie('foo'));
        $this->assertFalse($request->hasCookie('not'));
    }

    public function testFile()
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

    public function testFileAll()
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
        $this->assertInternalType('array', $files);
        $this->assertSame(1, count($files));
    }

    public function testHasFile()
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

    public function testMultiFile()
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
        $this->assertInternalType('array', $files);
        $this->assertSame(2, count($files));
    }

    public function testMultiFileGetArr()
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
        $this->assertInternalType('array', $files);
        $this->assertSame(2, count($files));
    }

    public function testHeader()
    {
        $request = new Request([], [], [], [], [], ['HTTP_HOST' => '127.0.0.1', 'HTTP_REFERER' => 'https://www.queryphp.com']);
        $this->assertSame('127.0.0.1', $request->header('host'));
        $this->assertSame('127.0.0.1', $request->header('HOST'));
        $this->assertSame('https://www.queryphp.com', $request->header('referer'));
        $this->assertSame('https://www.queryphp.com', $request->header('REFERER'));
    }

    public function testHeaderAll()
    {
        $request = new Request([], [], [], [], [], ['HTTP_HOST' => '127.0.0.1', 'HTTP_REFERER' => 'https://www.queryphp.com']);
        $this->assertSame(['host' => '127.0.0.1', 'referer' => 'https://www.queryphp.com'], $request->header());
    }

    public function testHeaderNullWithDefault()
    {
        $request = new Request([], [], [], [], [], ['HTTP_HOST' => '127.0.0.1', 'HTTP_REFERER' => 'https://www.queryphp.com']);
        $this->assertNull($request->header('notFound'));
        $this->assertSame('default', $request->header('notFound', 'default'));
    }

    public function testServer()
    {
        $request = new Request([], [], [], [], [], ['HTTP_HOST' => '127.0.0.1', 'HTTP_REFERER' => 'https://www.queryphp.com']);
        $this->assertSame('127.0.0.1', $request->server('HTTP_HOST'));
        $this->assertSame('https://www.queryphp.com', $request->server('HTTP_REFERER'));
    }

    public function testServerAll()
    {
        $request = new Request([], [], [], [], [], ['HTTP_HOST' => '127.0.0.1', 'HTTP_REFERER' => 'https://www.queryphp.com']);
        $this->assertSame(['HTTP_HOST' => '127.0.0.1', 'HTTP_REFERER' => 'https://www.queryphp.com'], $request->server());
    }

    public function testServiceNullWithDefault()
    {
        $request = new Request([], [], [], [], [], ['HTTP_HOST' => '127.0.0.1', 'HTTP_REFERER' => 'https://www.queryphp.com']);
        $this->assertNull($request->server('notFound'));
        $this->assertSame('default', $request->server('notFound', 'default'));
    }

    public function testMerge()
    {
        $request = new Request(['foo' => 'bar']);
        $this->assertSame(['foo' => 'bar'], $request->input());
        $request->merge(['hello' => 'world']);
        $this->assertSame(['foo' => 'bar', 'hello' => 'world'], $request->input());
    }

    public function testReplace()
    {
        $request = new Request(['foo' => 'bar']);
        $this->assertSame(['foo' => 'bar'], $request->input());
        $request->replace(['hello' => 'world']);
        $this->assertSame(['hello' => 'world'], $request->input());
    }

    public function testIsCli()
    {
        $request = new Request();
        $this->assertTrue($request->isCli());
    }

    public function testIsCliForSwoole()
    {
        $request = new Request([], [], [], [], [], ['SERVER_SOFTWARE' => 'swoole-http-server']);
        $this->assertFalse($request->isCli());
    }

    public function testIsCgi()
    {
        $request = new Request();
        $this->assertFalse($request->isCgi());
    }

    public function testIsJsonForContentType()
    {
        $request = new Request([], [], [], [], [], ['CONTENT_TYPE' => 'application/json']);
        $this->assertTrue($request->isJson());
    }

    public function testIsJsonForContentTypeButIsHtml()
    {
        $request = new Request([], [], [], [], [], ['CONTENT_TYPE' => 'text/html']);
        $this->assertFalse($request->isJson());
    }

    public function testIsGet()
    {
        $request = new Request();
        $this->assertTrue($request->isGet());
    }

    public function testIsGetWillReturnTrue()
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
    public function testIsMethodCheck(string $method)
    {
        $request = new Request();
        $isMethod = 'is'.ucfirst($method);
        $this->assertFalse($request->{$isMethod}());
    }

    /**
     * @dataProvider provideIsMethod
     *
     * @param mixed $method
     */
    public function testIsMethodCheckWillReturnTrue(string $method)
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

    public function testGetClientIp()
    {
        $request = new Request([], [], [], [], [], ['HTTP_CLIENT_IP' => '127.0.0.1']);
        $this->assertSame('127.0.0.1', $request->getClientIp());
    }

    public function testGetClientIpWithRemoteAddr()
    {
        $request = new Request([], [], [], [], [], ['REMOTE_ADDR' => '127.0.0.1']);
        $this->assertSame('127.0.0.1', $request->getClientIp());
    }

    public function testGetClientIpWithDefault()
    {
        $request = new Request();
        $this->assertSame('0.0.0.0', $request->getClientIp());
    }

    public function testGetRealMethod()
    {
        $request = new Request();
        $this->assertSame('GET', $request->getRealMethod());
    }

    public function testGetContent()
    {
        $request = new Request([], [], [], [], [], [], 'helloworld');
        $this->assertSame('helloworld', $request->getContent());
    }

    public function testGetContentFromPhpInput()
    {
        $request = new Request();
        $this->assertSame('', $request->getContent());
    }

    public function testGetContentFromResource()
    {
        $file = __DIR__.'/testGetContentFromResource.txt';
        file_put_contents($file, 'hello');
        $text = fopen($file, 'r');
        $this->assertInternalType('resource', $text);
        $request = new Request([], [], [], [], [], [], $text);
        $this->assertSame('hello', $request->getContent());
        unlink($file);
    }

    public function testGetRoot()
    {
        $request = new Request([], [], [], [], [], ['SERVER_NAME' => 'servername', 'SERVER_PORT' => '90']);
        $this->assertSame('http://servername:90', $request->getRoot());
    }

    public function testGetEnter()
    {
        $request = new Request();
        $this->assertSame('', $request->getEnter());
    }

    public function testGetEnterWasNotEmpty()
    {
        $request = new Request([], [], [], [], [], ['SERVER_SOFTWARE' => 'swoole-http-server', 'SCRIPT_NAME' => '/base/web/index_dev.php']);
        $this->assertSame('/base/web', $request->getEnter());
    }

    public function testToArray()
    {
        $request = new Request(['foo' => 'bar', 'hello' => 'world']);
        $this->assertSame(['foo' => 'bar', 'hello' => 'world'], $request->toArray());
    }

    public function testOffsetExists()
    {
        $request = new Request(['foo' => 'bar', 'hello' => 'world']);
        $this->assertTrue(isset($request['foo']));
        $this->assertFalse(isset($request['notfound']));
    }

    public function testOffsetGet()
    {
        $request = new Request(['foo' => 'bar', 'hello' => 'world']);
        $this->assertSame('bar', $request['foo']);
        $this->assertNull($request['notfound']);
    }

    public function testOffsetSet()
    {
        $request = new Request(['foo' => 'bar', 'hello' => 'world']);
        $this->assertSame('bar', $request['foo']);
        $request['foo'] = 'newbar';
        $this->assertSame('newbar', $request['foo']);
    }

    public function testOffsetUnset()
    {
        $request = new Request(['foo' => 'bar', 'hello' => 'world']);
        $this->assertSame('bar', $request['foo']);
        unset($request['foo']);
        $this->assertNull($request['foo']);
    }

    protected function createTempFile()
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
        return http_build_query(['_method' => 'PUT', 'content' => 'mycontent'], '', '&');
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
