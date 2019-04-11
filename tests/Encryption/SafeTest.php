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

namespace Tests\Encryption;

use Leevel\Encryption\Safe;
use Tests\TestCase;

/**
 * safe test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.15
 *
 * @version 1.0
 *
 * @api(
 *     title="安全过滤",
 *     path="component/safe",
 *     description="可以对用户输入数据进行过滤。",
 * )
 */
class SafeTest extends TestCase
{
    /**
     * @api(
     *     title="添加模式转义和移除魔术方法转义",
     *     description="",
     *     note="",
     * )
     */
    public function testBaseUse()
    {
        $strings = "O'Reilly?";
        $out = "O\\'Reilly?";

        $this->assertSame($out, Safe::customAddslashes($strings));

        $this->assertSame($strings, Safe::customStripslashes($out));

        $arrays = ["O'Reilly?" => "O'Reilly?"];
        $outs = ["O\\'Reilly?" => "O\\'Reilly?"];

        $this->assertSame($outs, Safe::customAddslashes($arrays));

        $this->assertSame($arrays, Safe::customStripslashes($outs));
    }

    /**
     * @api(
     *     title="深度过滤",
     *     description="",
     *     note="",
     * )
     */
    public function testDeepReplace()
    {
        $strings = 'You should eat fruits, vegetables, and fiber every day.';
        $out = 'You should eat fruits, vegetables, and fiber every .';

        $this->assertSame($out, Safe::deepReplace(['shoule', 'day'], $strings));
    }

    /**
     * @api(
     *     title="url 安全过滤",
     *     description="",
     *     note="",
     * )
     */
    public function testEscUrl()
    {
        $strings = 'You should eat fruits, vegetables, and fiber every day.';
        $out = 'You should eat fruits, vegetables, and fiber every .';

        $this->assertSame('', Safe::escUrl(''));

        $this->assertSame(
            'http://example.org/private.php?user=abc&email=abc@11.org',
            Safe::escUrl('example.org/private.php?user=abc&email=abc@11.org')
        );

        $this->assertSame(
            'http://example.org/private.php?user=abc&email=abc@11.org',
            Safe::escUrl('http;//example.org/private.php?user=abc&email=abc@11.org')
        );

        $this->assertSame(
            'http://example.org/private.php?user=abc&email=abc@11.org',
            Safe::escUrl('http://example.org/private.php?user=abc%0D%0A&email=abc@11.org')
        );
    }

    /**
     * @api(
     *     title="过滤 script",
     *     description="",
     *     note="",
     * )
     */
    public function testFilterScript()
    {
        $strings = '<script>hello world.';
        $out = '&lt;script>hello world.';

        $this->assertSame($out, Safe::filterScript($strings));
    }

    /**
     * @api(
     *     title="过滤十六进制字符串",
     *     description="",
     *     note="",
     * )
     */
    public function testCleanHex()
    {
        $strings = '0x63hello 0x6f world.';
        $out = '0hello 0 world.';

        $this->assertSame($out, Safe::cleanHex($strings));
    }

    public function testSqlFilter()
    {
        $strings = "'myuser' or % # 'foo' = 'foo'";
        $out = 'myuserorfoo=foo';

        $this->assertSame($out, Safe::sqlFilter($strings));
    }

    public function testFieldsFilter()
    {
        $strings = "'myuser' or % # 'foo' = 'foo'";
        $out = 'myuserorfoo=foo';

        $this->assertSame($out, Safe::fieldsFilter($strings));

        $strings = ["'myuser' or % # 'foo' = 'foo'"];
        $out = 'myuserorfoo=foo';

        $this->assertSame($out, Safe::fieldsFilter($strings));
    }

    public function testStrFilter()
    {
        $strings = 'This is some <b>bold</b> text.';
        $out = 'This is some &lt;b&gt;bold&lt;/b&gt; text.';

        $this->assertSame($out, Safe::strFilter($strings));
        $this->assertSame('', Safe::strFilter($strings, 5));

        $strings = ['This is some <b>bold</b> text.'];
        $out = ['This is some &lt;b&gt;bold&lt;/b&gt; text.'];

        $this->assertSame($out, Safe::strFilter($strings));
        $this->assertSame([''], Safe::strFilter($strings, 5));
    }

    public function testHtmlFilter()
    {
        $strings = "foo bar<script>.<span onclick='alert(5);'>yes</span>.";
        $out = 'foo bar&lt;script&gt;.<span >yes</span>.';

        $this->assertSame($out, Safe::htmlFilter($strings));
        $this->assertSame('', Safe::htmlFilter($strings, 5));

        $strings = ["foo bar<script>.<span onclick='alert(5);'>yes</span>."];
        $out = ['foo bar&lt;script&gt;.<span >yes</span>.'];

        $this->assertSame($out, Safe::htmlFilter($strings));
        $this->assertSame([''], Safe::htmlFilter($strings, 5));
    }

    public function testIntArrFilter()
    {
        $strings = '5wb,577,sss66zz';
        $out = '5,577,0';

        $this->assertSame($out, Safe::intArrFilter($strings));
        $this->assertSame(0, Safe::intArrFilter(''));

        $strings = ['55wb', '577', 'sss66zz'];
        $out = '55,577,0';

        $this->assertSame($out, Safe::intArrFilter($strings));
    }

    public function testStrArrFilter()
    {
        $strings = '5wb,577,sss66zz';
        $out = "'5wb','577','sss66zz'";

        $this->assertSame($out, Safe::strArrFilter($strings));
        $this->assertSame('', Safe::strArrFilter(''));

        $strings = ['55wb', '577', 'sss66zz'];
        $out = "'55wb','577','sss66zz'";

        $this->assertSame($out, Safe::strArrFilter($strings));
    }

    public function testLimitTime()
    {
        $this->assertNull(Safe::limitTime([], 0));

        $this->assertNull(Safe::limitTime(['abc'], 0));

        $time = date('Y-m-d');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'You can only before '.$time.' 08:10:00 or after '.$time.' 08:30:00 to access this.'
        );

        Safe::limitTime(['08:10', '08:30'], strtotime('08:15'));
    }

    public function testLimitTime2()
    {
        $time = date('Y-m-d');
        $time2 = date('Y-m-d', time() + 86400);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'You can only before '.$time.' 08:30:00 or after '.$time2.' 08:10:00 to access this.'
        );

        Safe::limitTime(['08:30', '08:10'], strtotime('10:50'));
    }

    public function testLimitTime3()
    {
        $this->assertNull(Safe::limitTime(['08:10', '08:30'], strtotime('08:05')));
        $this->assertNull(Safe::limitTime(['08:10', '08:30'], strtotime('08:35')));
    }

    public function testLimitIp()
    {
        $this->assertNull(Safe::limitIp('', []));

        $this->assertNull(Safe::limitIp('', [0]));

        $this->assertNull(Safe::limitIp('127.0.0.5', ['127.0.0.1']));
    }

    public function testLimitIp2()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'You IP 127.0.0.1 are banned,you cannot access this.'
        );

        Safe::limitIp('127.0.0.1', ['127.0.0.1']);
    }

    public function testLimitAgent()
    {
        $this->assertNull(Safe::limitAgent());
    }

    public function testLimitAgent2()
    {
        $_SERVER['HTTP_X_FORWARDED_FOR'] = 1;

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Proxy Connection denied.Your request was forbidden due to the '.
            'administrator has set to deny all proxy connection.'
        );

        Safe::limitAgent();

        unset($_SERVER['HTTP_X_FORWARDED_FOR']);
    }

    public function testLimitAgent3()
    {
        $_SERVER['HTTP_VIA'] = 1;

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Proxy Connection denied.Your request was forbidden due to the '.
            'administrator has set to deny all proxy connection.'
        );

        Safe::limitAgent();

        unset($_SERVER['HTTP_VIA']);
    }

    public function testLimitAgent4()
    {
        $_SERVER['HTTP_PROXY_CONNECTION'] = 1;

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Proxy Connection denied.Your request was forbidden due to the '.
            'administrator has set to deny all proxy connection.'
        );

        Safe::limitAgent();

        unset($_SERVER['HTTP_PROXY_CONNECTION']);
    }

    public function testLimitAgent5()
    {
        $_SERVER['HTTP_USER_AGENT_VIA'] = 1;

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Proxy Connection denied.Your request was forbidden due to the '.
            'administrator has set to deny all proxy connection.'
        );

        Safe::limitAgent();

        unset($_SERVER['HTTP_USER_AGENT_VIA']);
    }

    public function testCleanJs()
    {
        $strings = "i a <script></script> <body> <span onmouse='alert(5);'></span>".
            '<span window. xxx>'.
            '<script>window</script> here';
        $out = 'i a  here';

        $this->assertSame($out, Safe::cleanJs($strings));

        $strings = 'i a <span javascript:></span> here';
        $out = 'i a <span ></span> here';

        $this->assertSame($out, Safe::cleanJs($strings));
    }

    public function testText()
    {
        $strings = "i a <script></script> \n\r<body> <span onmouse='alert(5);'> here";
        $out = 'iahere';

        $this->assertSame($out, Safe::text($strings));
    }

    public function testText2()
    {
        $strings = "i a <script></script> \n\r<body> <span onmouse='alert(5);'> here";
        $out = 'i a  here';

        $this->assertSame($out, Safe::text($strings, false));
    }

    public function testStrip()
    {
        $strings = "i a <script></script> <body> <span onmouse='alert(5);'> here";
        $out = 'i a    here';

        $this->assertSame($out, Safe::strip($strings));
    }

    public function testHtmlView()
    {
        $strings = "i a \n here";
        $out = 'i a <br />
 here';

        $this->assertSame($out, Safe::htmlView($strings));
    }

    public function testCustomHtmlspecialchars()
    {
        $strings = 'i a < here';
        $out = 'i a &lt; here';

        $this->assertSame($out, Safe::customHtmlspecialchars($strings));

        $strings = ['i a < here', 'i a > here'];
        $out = ['i a &lt; here', 'i a &gt; here'];

        $this->assertSame($out, Safe::customHtmlspecialchars($strings));
    }

    public function testUnHtmlSpecialchars()
    {
        $strings = 'i a &lt; here';
        $out = 'i a < here';

        $this->assertSame($out, Safe::unHtmlspecialchars($strings));

        $strings = ['i a &lt; here', 'i a &gt; here'];
        $out = ['i a < here', 'i a > here'];

        $this->assertSame($out, Safe::unHtmlspecialchars($strings));
    }

    public function testShortCheck()
    {
        $strings = 'i a # > here';
        $out = 'i a  &gt; here';

        $this->assertSame($out, Safe::shortCheck($strings));

        $strings = 'i a # > here';
        $out = '';

        $this->assertSame($out, Safe::shortCheck($strings, 5));
    }

    public function testLongCheck()
    {
        $strings = 'i a # > here';
        $out = 'i a # &gt; here';

        $this->assertSame($out, Safe::longCheck($strings));

        $strings = 'i a # > here';
        $out = '';

        $this->assertSame($out, Safe::longCheck($strings, 5));
    }

    public function testBigCheck()
    {
        $strings = 'i a <script  # > here';
        $out = 'i a  # > here';

        $this->assertSame($out, Safe::bigCheck($strings));

        $strings = 'i <script a # > here';
        $out = ' ';

        $this->assertSame($out, Safe::bigCheck($strings, 5));
    }

    /**
     * @api(
     *     title="签名算法支持",
     *     description="",
     *     note="",
     * )
     */
    public function testSignature()
    {
        $query = [
            'foo'   => 'bar',
            'hello' => 'world',
        ];

        $signature = Safe::signature($query, '123456');

        $this->assertSame('dc6cfa1e1f6eaf29c73622f4d4c54be57d545c1d7c377dade88faccb5a79d2d8', $signature);
    }

    /**
     * @api(
     *     title="签名算法支持忽略字段",
     *     description="",
     *     note="",
     * )
     */
    public function testSignatureWithIgnore()
    {
        $query = [
            'foo'       => 'bar',
            'hello'     => 'world',
            'signature' => 'dc6cfa1e1f6eaf29c73622f4d4c54be57d545c1d7c377dade88faccb5a79d2d8',
            'timestamp' => 1541312367,
        ];

        $signature = Safe::signature($query, '123456', ['signature', 'timestamp']);

        $this->assertSame('dc6cfa1e1f6eaf29c73622f4d4c54be57d545c1d7c377dade88faccb5a79d2d8', $signature);
    }

    /**
     * @api(
     *     title="签名算法支持子数组",
     *     description="",
     *     note="",
     * )
     */
    public function testSignatureWithSubArray()
    {
        $query = [
            'foo'   => 'bar',
            'hello' => 'world',
            'sub'   => [
                'hello' => 'world',
            ],
        ];

        $signature = Safe::signature($query, '123456');

        $this->assertSame('2bd98c89629fae202c680b33430eb9c909b25f4e8a8dca91752fabd1e14735d1', $signature);
    }
}
