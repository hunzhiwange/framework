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

namespace Tests\Encryption;

use Leevel\Encryption\Safe;
use Tests\TestCase;

/**
 * @api(
 *     title="安全过滤",
 *     path="component/encryption/safe",
 *     description="可以对用户输入数据进行过滤。",
 * )
 */
class SafeTest extends TestCase
{
    /**
     * @api(
     *     title="custom_addslashes 添加模式转义和移除魔术方法转义",
     *     description="",
     *     note="",
     * )
     */
    public function testBaseUse(): void
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
     *     title="deep_replace 深度过滤",
     *     description="",
     *     note="",
     * )
     */
    public function testDeepReplace(): void
    {
        $strings = 'You should eat fruits, vegetables, and fiber every day.';
        $out = 'You should eat fruits, vegetables, and fiber every .';

        $this->assertSame($out, Safe::deepReplace(['shoule', 'day'], $strings));
    }

    /**
     * @api(
     *     title="filter_script 过滤 script",
     *     description="",
     *     note="",
     * )
     */
    public function testFilterScript(): void
    {
        $strings = '<script>hello world.';
        $out = '&lt;script>hello world.';

        $this->assertSame($out, Safe::filterScript($strings));
    }

    /**
     * @api(
     *     title="clean_hex 过滤十六进制字符串",
     *     description="",
     *     note="",
     * )
     */
    public function testCleanHex(): void
    {
        $strings = '0x63hello 0x6f world.';
        $out = '0hello 0 world.';

        $this->assertSame($out, Safe::cleanHex($strings));
    }

    /**
     * @api(
     *     title="str_filter 字符过滤",
     *     description="",
     *     note="",
     * )
     */
    public function testStrFilter(): void
    {
        $strings = 'This is some <b>bold</b> text.';
        $out = 'This is some &lt;b&gt;bold&lt;/b&gt; text.';
        $this->assertSame($out, Safe::strFilter($strings));

        $strings = ['This is some <b>bold</b> text.'];
        $out = ['This is some &lt;b&gt;bold&lt;/b&gt; text.'];
        $this->assertSame($out, Safe::strFilter($strings));
    }

    /**
     * @api(
     *     title="html_filter HTML 过滤",
     *     description="",
     *     note="",
     * )
     */
    public function testHtmlFilter(): void
    {
        $strings = "foo bar<script>.<span onclick='alert(5);'>yes</span>.";
        $out = 'foo bar&lt;script&gt;.<span >yes</span>.';
        $this->assertSame($out, Safe::htmlFilter($strings));

        $strings = ["foo bar<script>.<span onclick='alert(5);'>yes</span>."];
        $out = ['foo bar&lt;script&gt;.<span >yes</span>.'];
        $this->assertSame($out, Safe::htmlFilter($strings));
    }

    /**
     * @api(
     *     title="html_view 字符 HTML 安全显示",
     *     description="",
     *     note="",
     * )
     */
    public function testHtmlView(): void
    {
        $strings = "i a \n here";
        $out = 'i a <br />
 here';

        $this->assertSame($out, Safe::htmlView($strings));
    }

    /**
     * @api(
     *     title="clean_js 过滤 JavaScript",
     *     description="",
     *     note="",
     * )
     */
    public function testCleanJs(): void
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

    /**
     * @api(
     *     title="text 字符串文本化",
     *     description="",
     *     note="",
     * )
     */
    public function testText(): void
    {
        $strings = "i a <script></script> \n\r<body> <span onmouse='alert(5);'> here";
        $out = 'iahere';

        $this->assertSame($out, Safe::text($strings));
    }

    public function testText2(): void
    {
        $strings = "i a <script></script> \n\r<body> <span onmouse='alert(5);'> here";
        $out = 'i a  here';

        $this->assertSame($out, Safe::text($strings, false));
    }

    /**
     * @api(
     *     title="strip 字符过滤 JS 和 HTML 标签",
     *     description="",
     *     note="",
     * )
     */
    public function testStrip(): void
    {
        $strings = "i a <script></script> <body> <span onmouse='alert(5);'> here";
        $out = 'i a    here';

        $this->assertSame($out, Safe::strip($strings));
    }

    /**
     * @api(
     *     title="custom_htmlspecialchars 字符 HTML 安全实体",
     *     description="",
     *     note="",
     * )
     */
    public function testCustomHtmlspecialchars(): void
    {
        $strings = 'i a < here';
        $out = 'i a &lt; here';

        $this->assertSame($out, Safe::customHtmlspecialchars($strings));

        $strings = ['i a < here', 'i a > here'];
        $out = ['i a &lt; here', 'i a &gt; here'];

        $this->assertSame($out, Safe::customHtmlspecialchars($strings));
    }

    /**
     * @api(
     *     title="un_htmlspecialchars 字符 HTML 实体还原",
     *     description="",
     *     note="",
     * )
     */
    public function testUnHtmlSpecialchars(): void
    {
        $strings = 'i a &lt; here';
        $out = 'i a < here';

        $this->assertSame($out, Safe::unHtmlspecialchars($strings));

        $strings = ['i a &lt; here', 'i a &gt; here'];
        $out = ['i a < here', 'i a > here'];

        $this->assertSame($out, Safe::unHtmlspecialchars($strings));
    }
}
