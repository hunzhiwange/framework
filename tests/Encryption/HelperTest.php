<?php

declare(strict_types=1);

namespace Tests\Encryption;

use Leevel\Encryption\Helper;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="安全过滤",
 *     path="component/encryption/helper",
 *     zh-CN:description="可以对用户输入数据进行过滤。",
 * )
 *
 * @internal
 */
final class HelperTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="custom_addslashes 添加模式转义和移除魔术方法转义",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $strings = "O'Reilly?";
        $out = "O\\'Reilly?";

        static::assertSame($out, Helper::customAddslashes($strings));

        static::assertSame($strings, Helper::customStripslashes($out));

        $arrays = ["O'Reilly?" => "O'Reilly?"];
        $outs = ["O\\'Reilly?" => "O\\'Reilly?"];

        static::assertSame($outs, Helper::customAddslashes($arrays));

        static::assertSame($arrays, Helper::customStripslashes($outs));
    }

    /**
     * @api(
     *     zh-CN:title="deep_replace 深度过滤",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testDeepReplace(): void
    {
        $strings = 'You should eat fruits, vegetables, and fiber every day.';
        $out = 'You should eat fruits, vegetables, and fiber every .';

        static::assertSame($out, Helper::deepReplace(['not found', 'day'], $strings));
    }

    /**
     * @api(
     *     zh-CN:title="filter_script 过滤 script",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testFilterScript(): void
    {
        $strings = '<script>hello world.';
        $out = '&lt;script>hello world.';

        static::assertSame($out, Helper::filterScript($strings));
    }

    /**
     * @api(
     *     zh-CN:title="clean_hex 过滤十六进制字符串",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testCleanHex(): void
    {
        $strings = '0x63hello 0x6f world.';
        $out = '0hello 0 world.';

        static::assertSame($out, Helper::cleanHex($strings));
    }

    /**
     * @api(
     *     zh-CN:title="str_filter 字符过滤",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testStrFilter(): void
    {
        $strings = 'This is some <b>bold</b> text.';
        $out = 'This is some &lt;b&gt;bold&lt;/b&gt; text.';
        static::assertSame($out, Helper::strFilter($strings));

        $strings = ['This is some <b>bold</b> text.'];
        $out = ['This is some &lt;b&gt;bold&lt;/b&gt; text.'];
        static::assertSame($out, Helper::strFilter($strings));
    }

    /**
     * @api(
     *     zh-CN:title="html_filter HTML 过滤",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testHtmlFilter(): void
    {
        $strings = "foo bar<script>.<span onclick='alert(5);'>yes</span>.";
        $out = 'foo bar&lt;script&gt;.<span >yes</span>.';
        static::assertSame($out, Helper::htmlFilter($strings));

        $strings = ["foo bar<script>.<span onclick='alert(5);'>yes</span>."];
        $out = ['foo bar&lt;script&gt;.<span >yes</span>.'];
        static::assertSame($out, Helper::htmlFilter($strings));
    }

    /**
     * @api(
     *     zh-CN:title="html_view 字符 HTML 安全显示",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testHtmlView(): void
    {
        $strings = "i a \n here";
        $out = 'i a <br />
 here';

        static::assertSame($out, Helper::htmlView($strings));
    }

    /**
     * @api(
     *     zh-CN:title="clean_js 过滤 JavaScript",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testCleanJs(): void
    {
        $strings = "i a <script></script> <body> <span onmouse='alert(5);'></span>".
            '<span window. xxx>'.
            '<script>window</script> here';
        $out = 'i a  here';

        static::assertSame($out, Helper::cleanJs($strings));

        $strings = 'i a <span javascript:></span> here';
        $out = 'i a <span ></span> here';

        static::assertSame($out, Helper::cleanJs($strings));
    }

    /**
     * @api(
     *     zh-CN:title="text 字符串文本化",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testText(): void
    {
        $strings = "i a <script></script> \n\r<body> <span onmouse='alert(5);'> here";
        $out = 'iahere';

        static::assertSame($out, Helper::text($strings));
    }

    public function testText2(): void
    {
        $strings = "i a <script></script> \n\r<body> <span onmouse='alert(5);'> here";
        $out = 'i a  here';

        static::assertSame($out, Helper::text($strings, false));
    }

    /**
     * @api(
     *     zh-CN:title="strip 字符过滤 JS 和 HTML 标签",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testStrip(): void
    {
        $strings = "i a <script></script> <body> <span onmouse='alert(5);'> here";
        $out = 'i a    here';

        static::assertSame($out, Helper::strip($strings));
    }

    /**
     * @api(
     *     zh-CN:title="custom_htmlspecialchars 字符 HTML 安全实体",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testCustomHtmlspecialchars(): void
    {
        $strings = 'i a < here';
        $out = 'i a &lt; here';

        static::assertSame($out, Helper::customHtmlspecialchars($strings));

        $strings = ['i a < here', 'i a > here'];
        $out = ['i a &lt; here', 'i a &gt; here'];

        static::assertSame($out, Helper::customHtmlspecialchars($strings));
    }

    /**
     * @api(
     *     zh-CN:title="un_htmlspecialchars 字符 HTML 实体还原",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testUnHtmlSpecialchars(): void
    {
        $strings = 'i a &lt; here';
        $out = 'i a < here';

        static::assertSame($out, Helper::unHtmlspecialchars($strings));

        $strings = ['i a &lt; here', 'i a &gt; here'];
        $out = ['i a < here', 'i a > here'];

        static::assertSame($out, Helper::unHtmlspecialchars($strings));
    }

    public function testNotFound(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Class "Leevel\\Encryption\\Helper\\NotFound" not found');

        static::assertTrue(Helper::notFound());
    }
}
