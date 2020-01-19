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

use Leevel\Http\Cookie;
use stdClass;
use Tests\TestCase;

/**
 * @api(
 *     title="Cookie",
 *     path="component/http/cookie",
 *     description="QueryPHP 最终的 Cookie 响应都将通过 `\Leevel\Http\Cookie` 对象进行处理。",
 * )
 */
class CookieTest extends TestCase
{
    /**
     * @api(
     *     title="COOKIE 基本使用",
     *     description="",
     *     note="",
     * )
     */
    public function testBaseUse(): void
    {
        $cookie = new Cookie();

        $cookie->set('foo', 'bar');

        $this->assertSame([
            'foo' => [
                'foo',
                'bar',
                time() + 86400,
                '/',
                '',
                false,
                false,
            ],
        ], $cookie->all());
    }

    /**
     * @api(
     *     title="set.get.delete 设置 COOKIE、删除 COOKIE 和获取 COOKIE",
     *     description="",
     *     note="",
     * )
     */
    public function testSetGetDelete(): void
    {
        $cookie = new Cookie();

        $cookie->set('foo', 'bar');

        $this->assertSame('bar', $cookie->get('foo'));

        $cookie->delete('foo');

        $this->assertNull($cookie->get('foo'));
    }

    /**
     * @api(
     *     title="setOption 设置配置",
     *     description="",
     *     note="",
     * )
     */
    public function testSetOption(): void
    {
        $cookie = new Cookie();

        $cookie->setOption('domain', 'queryphp.com');
        $cookie->set('foo', 'bar');

        $this->assertSame([
            'foo' => [
                'foo',
                'bar',
                time() + 86400,
                '/',
                'queryphp.com',
                false,
                false,
            ],
        ], $cookie->all());
    }

    /**
     * @api(
     *     title="set 设置 COOKIE 支持数组",
     *     description="",
     *     note="",
     * )
     */
    public function testSetArray(): void
    {
        $cookie = new Cookie();

        $cookie->set('foo', ['bar']);

        $this->assertSame([
            'foo' => [
                'foo',
                '["bar"]',
                time() + 86400,
                '/',
                '',
                false,
                false,
            ],
        ], $cookie->all());

        $this->assertSame(['bar'], $cookie->get('foo'));
    }

    public function testSetWithOptionExpire(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cookie expire date must greater than or equal 0.');

        $cookie = new Cookie();
        $cookie->set('foo', 'bar', ['expire' => -10]);
    }

    public function testSetWithOptionExpire3(): void
    {
        $cookie = new Cookie();

        $cookie->set('foo', 'bar', ['expire' => 0]);

        $this->assertSame([
            'foo' => [
                'foo',
                'bar',
                0,
                '/',
                '',
                false,
                false,
            ],
        ], $cookie->all());
    }

    public function testSetException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cookie value must be string,array or null.');

        $cookie = new Cookie();

        $cookie->set('foo', new stdClass());
    }

    /**
     * @api(
     *     title="set 设置 COOKIE 支持 HTTPS",
     *     description="",
     *     note="",
     * )
     */
    public function testSetHttps(): void
    {
        $cookie = new Cookie();

        $cookie->set('foo', 'bar', ['secure' => true]);

        $this->assertSame([
            'foo' => [
                'foo',
                'bar',
                time() + 86400,
                '/',
                '',
                true,
                false,
            ],
        ], $cookie->all());
    }

    /**
     * @api(
     *     title="get 获取 COOKIE 支持默认值",
     *     description="",
     *     note="",
     * )
     */
    public function testGetDefaults(): void
    {
        $cookie = new Cookie();

        $this->assertNull($cookie->get('notExists'));
        $this->assertSame('hello', $cookie->get('notExists', 'hello'));
    }

    /**
     * @api(
     *     title="clear 清空 COOKIE",
     *     description="",
     *     note="",
     * )
     */
    public function testClear(): void
    {
        $cookie = new Cookie();

        $cookie->set('hello', 'world');
        $cookie->set('foo', 'bar');

        $this->assertSame([
            'hello' => [
                'hello',
                'world',
                time() + 86400,
                '/',
                '',
                false,
                false,
            ],
            'foo' => [
                'foo',
                'bar',
                time() + 86400,
                '/',
                '',
                false,
                false,
            ],
        ], $cookie->all());

        $cookie->clear();

        $this->assertSame([
            'hello' => [
                'hello',
                null,
                time() + 86400,
                '/',
                '',
                false,
                false,
            ],
            'foo' => [
                'foo',
                null,
                time() + 86400,
                '/',
                '',
                false,
                false,
            ],
        ], $cookie->all());
    }

    /**
     * @api(
     *     title="put 批量插入",
     *     description="",
     *     note="",
     * )
     */
    public function testPut(): void
    {
        $cookie = new Cookie();

        $cookie->put('foo', 'bar');

        $this->assertSame([
            'foo' => [
                'foo',
                'bar',
                time() + 86400,
                '/',
                '',
                false,
                false,
            ],
        ], $cookie->all());

        $cookie->put(['hello' => 'world', 'test1' => 'value1']);

        $this->assertSame([
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
            'test1' => [
                'test1',
                'value1',
                time() + 86400,
                '/',
                '',
                false,
                false,
            ],
        ], $cookie->all());
    }

    /**
     * @api(
     *     title="format 格式化 COOKIE 为字符串",
     *     description="",
     *     note="",
     * )
     */
    public function testFormat(): void
    {
        $cookie = new Cookie();
        $cookie->set('foo', 'datakey_1');
        $test = $cookie->all()['foo'];
        $str = 'foo=datakey_1; expires='.gmdate('D, d-M-Y H:i:s T', $test[2]).'; Max-Age=86400; path=/';
        $this->assertSame($str, $cookie->format($test));
    }

    public function testFormatWithExpire(): void
    {
        $cookie = new Cookie();
        $cookie->set('foo', 'datakey_1', ['expire' => 60]);
        $test = $cookie->all()['foo'];
        $str = 'foo=datakey_1; expires='.gmdate('D, d-M-Y H:i:s T', $test[2]).'; Max-Age=60; path=/';
        $this->assertSame($str, $cookie->format($test));
    }

    public function testFormatWithDomain(): void
    {
        $cookie = new Cookie();
        $cookie->set('foo', 'datakey_1', ['domain' => 'queryphp.com']);
        $test = $cookie->all()['foo'];
        $str = 'foo=datakey_1; expires='.gmdate('D, d-M-Y H:i:s T', $test[2]).'; Max-Age=86400; path=/; domain=queryphp.com';
        $this->assertSame($str, $cookie->format($test));
    }

    public function testFormatWithHttponly(): void
    {
        $cookie = new Cookie();
        $cookie->set('foo', 'datakey_1', ['httponly' => true]);
        $test = $cookie->all()['foo'];
        $str = 'foo=datakey_1; expires='.gmdate('D, d-M-Y H:i:s T', $test[2]).'; Max-Age=86400; path=/; httponly';
        $this->assertSame($str, $cookie->format($test));
    }

    public function testFormatWithSecure(): void
    {
        $cookie = new Cookie();
        $cookie->set('foo', 'datakey_1', ['secure' => true]);
        $test = $cookie->all()['foo'];
        $str = 'foo=datakey_1; expires='.gmdate('D, d-M-Y H:i:s T', $test[2]).'; Max-Age=86400; path=/; secure';
        $this->assertSame($str, $cookie->format($test));
    }

    public function testFormatWithDelete(): void
    {
        $cookie = new Cookie();
        $cookie->set('foo', 'datakey_1');
        $cookie->delete('foo');
        $test = $cookie->all()['foo'];
        $str = 'foo=deleted; expires='.gmdate('D, d-M-Y H:i:s T', time() - 31536001).'; Max-Age=0; path=/';
        $this->assertSame($str, $cookie->format($test));
    }

    public function testFormatWithDeleteWhenSetEmptyString(): void
    {
        $cookie = new Cookie();
        $cookie->set('foo', '');
        $test = $cookie->all()['foo'];
        $str = 'foo=deleted; expires='.gmdate('D, d-M-Y H:i:s T', time() - 31536001).'; Max-Age=0; path=/';
        $this->assertSame($str, $cookie->format($test));
    }

    public function testFormatWithDeleteWhenSetNull(): void
    {
        $cookie = new Cookie();
        $cookie->set('foo', null);
        $test = $cookie->all()['foo'];
        $str = 'foo=deleted; expires='.gmdate('D, d-M-Y H:i:s T', time() - 31536001).'; Max-Age=0; path=/';
        $this->assertSame($str, $cookie->format($test));
    }

    public function testFormatInvalidCookieData(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid cookie data.');

        $cookie = new Cookie();
        $cookie->format([]);
    }
}
