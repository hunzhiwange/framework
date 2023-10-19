<?php

declare(strict_types=1);

namespace Tests\Encryption;

use Leevel\Encryption\Helper;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '安全过滤',
    'path' => 'component/encryption/helper',
    'zh-CN:description' => <<<'EOT'
可以对用户输入数据进行过滤。
EOT,
])]
final class HelperTest extends TestCase
{
    #[Api([
        'zh-CN:title' => 'custom_addslashes 添加模式转义和移除魔术方法转义',
    ])]
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

    #[Api([
        'zh-CN:title' => 'deep_replace 深度过滤',
    ])]
    public function testDeepReplace(): void
    {
        $strings = 'You should eat fruits, vegetables, and fiber every day.';
        $out = 'You should eat fruits, vegetables, and fiber every .';

        static::assertSame($out, Helper::deepReplace(['not found', 'day'], $strings));
    }

    #[Api([
        'zh-CN:title' => 'filter_script 过滤 script',
    ])]
    public function testFilterScript(): void
    {
        $strings = '<script>hello world.';
        $out = '&lt;script>hello world.';

        static::assertSame($out, Helper::filterScript($strings));
    }

    #[Api([
        'zh-CN:title' => 'clean_hex 过滤十六进制字符串',
    ])]
    public function testCleanHex(): void
    {
        $strings = '0x63hello 0x6f world.';
        $out = '0hello 0 world.';

        static::assertSame($out, Helper::cleanHex($strings));
    }

    #[Api([
        'zh-CN:title' => 'str_filter 字符过滤',
    ])]
    public function testStrFilter(): void
    {
        $strings = 'This is some <b>bold</b> text.';
        $out = 'This is some &lt;b&gt;bold&lt;/b&gt; text.';
        static::assertSame($out, Helper::strFilter($strings));

        $strings = ['This is some <b>bold</b> text.'];
        $out = ['This is some &lt;b&gt;bold&lt;/b&gt; text.'];
        static::assertSame($out, Helper::strFilter($strings));
    }

    #[Api([
        'zh-CN:title' => 'html_filter HTML 过滤',
    ])]
    public function testHtmlFilter(): void
    {
        $strings = "foo bar<script>.<span onclick='alert(5);'>yes</span>.";
        $out = 'foo bar&lt;script&gt;.<span >yes</span>.';
        static::assertSame($out, Helper::htmlFilter($strings));

        $strings = ["foo bar<script>.<span onclick='alert(5);'>yes</span>."];
        $out = ['foo bar&lt;script&gt;.<span >yes</span>.'];
        static::assertSame($out, Helper::htmlFilter($strings));
    }

    #[Api([
        'zh-CN:title' => 'html_view 字符 HTML 安全显示',
    ])]
    public function testHtmlView(): void
    {
        $strings = "i a \n here";
        $out = 'i a <br />
 here';

        static::assertSame($out, Helper::htmlView($strings));
    }

    #[Api([
        'zh-CN:title' => 'clean_js 过滤 JavaScript',
    ])]
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

    #[Api([
        'zh-CN:title' => 'text 字符串文本化',
    ])]
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

    #[Api([
        'zh-CN:title' => 'strip 字符过滤 JS 和 HTML 标签',
    ])]
    public function testStrip(): void
    {
        $strings = "i a <script></script> <body> <span onmouse='alert(5);'> here";
        $out = 'i a    here';

        static::assertSame($out, Helper::strip($strings));
    }

    #[Api([
        'zh-CN:title' => 'custom_htmlspecialchars 字符 HTML 安全实体',
    ])]
    public function testCustomHtmlspecialchars(): void
    {
        $strings = 'i a < here';
        $out = 'i a &lt; here';

        static::assertSame($out, Helper::customHtmlspecialchars($strings));

        $strings = ['i a < here', 'i a > here'];
        $out = ['i a &lt; here', 'i a &gt; here'];

        static::assertSame($out, Helper::customHtmlspecialchars($strings));
    }

    #[Api([
        'zh-CN:title' => 'un_htmlspecialchars 字符 HTML 实体还原',
    ])]
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
