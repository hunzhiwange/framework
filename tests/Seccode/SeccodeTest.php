<?php

declare(strict_types=1);

namespace Tests\Seccode;

use Leevel\Seccode\Seccode;
use Tests\TestCase;

/**
 * @api(
 *     title="Seccode",
 *     zh-CN:title="验证码",
 *     zh-TW:title="驗證碼",
 *     path="component/seccode",
 *     zh-CN:description="
 * QueryPHP 提供的验证组件，扩展包内定义了一些常见用法方便使用，可以满足大部分常用场景。
 *
 * ## 配置
 *
 * 验证码带有默认的配置参数，支持自定义配置。
 *
 * |  参数  | 默认值   | 描述  |
 * | ------------ | ------------ | ------------ |
 * | width |  160 | 验证码宽度  |
 * | height  |  60 | 验证码高度  |
 * | tilt  |  true | 随机倾斜度  |
 * | color  | true  | 随机颜色  |
 * | size  |  true | 随机大小  |
 * | font_path |   | 英文字体路径  |
 * | chinese_font_path |   |  中文字体路径 |
 * ",
 * note="你可以根据不同场景灵活运用，以满足产品需求。",
 * )
 */
class SeccodeTest extends TestCase
{
    protected function setUp(): void
    {
        $this->markTestSkipped('Skip seccode when \PHP_VERSION_ID >= 80000.');

        if (!function_exists('imagettftext')) {
            $this->markTestSkipped('Function imagettftext is not exists.');
        }
    }

    protected function tearDown(): void
    {
        $dirnames = [
            __DIR__.'/fontEmpty',
        ];

        foreach ($dirnames as $val) {
            if (is_dir($val)) {
                rmdir($val);
            }
        }
    }

    /**
     * @api(
     *     zh-CN:title="display 验证码基本使用",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $seccode = new Seccode([
            'font_path'       => __DIR__.'/font',
        ]);

        $file = __DIR__.'/baseuse.png';

        $seccode->display('abc', $file);

        $this->assertTrue(is_file($file));

        $info = getimagesize($file);

        $data = <<<'eot'
            {
                "0": 160,
                "1": 60,
                "2": 3,
                "3": "width=\"160\" height=\"60\"",
                "bits": 8,
                "mime": "image\/png"
            }
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $info
            )
        );

        unlink($file);
    }

    /**
     * @api(
     *     zh-CN:title="验证码支持中文",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testChinese(): void
    {
        $seccode = new Seccode([
            'font_path'               => __DIR__.'/font',
            'chinese_font_path'       => __DIR__.'/chinese', // 中文字体过于庞大，本地已经测试通过，这里用的英文的假字体，会乱码
        ]);

        $file = __DIR__.'/chinese.png';

        $seccode->display('中国', $file);

        $this->assertTrue(is_file($file));

        $info = getimagesize($file);

        $data = <<<'eot'
            {
                "0": 160,
                "1": 60,
                "2": 3,
                "3": "width=\"160\" height=\"60\"",
                "bits": 8,
                "mime": "image\/png"
            }
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $info
            )
        );

        unlink($file);
    }

    /**
     * @dataProvider getAutoCodeData
     *
     * @api(
     *     zh-CN:title="验证码支持随机生成",
     *     zh-CN:description="
     * **支持的类型**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Seccode\SeccodeTest::class, 'getAutoCodeData')]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testAutoCode(string $type): void
    {
        $seccode = new Seccode([
            'font_path'               => __DIR__.'/font',
            'chinese_font_path'       => __DIR__.'/chinese', // 中文字体过于庞大，本地已经测试通过，这里用的英文的假字体，会乱码
        ]);

        $file = __DIR__.'/autocode.'.$type.'.png';

        $seccode->display(4, $file, true, $type);

        $this->assertTrue(is_file($file));

        $info = getimagesize($file);

        $data = <<<'eot'
            {
                "0": 160,
                "1": 60,
                "2": 3,
                "3": "width=\"160\" height=\"60\"",
                "bits": 8,
                "mime": "image\/png"
            }
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $info
            )
        );

        unlink($file);
    }

    public function getAutoCodeData(): array
    {
        return [
            [Seccode::ALPHA_NUM],
            [Seccode::ALPHA_NUM_LOWERCASE],
            [Seccode::ALPHA_NUM_UPPERCASE],
            [Seccode::ALPHA],
            [Seccode::ALPHA_LOWERCASE],
            [Seccode::ALPHA_UPPERCASE],
            [Seccode::NUM],
            [Seccode::CHINESE],
        ];
    }

    public function testAutoCodeSizeException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Code must be greater than 0.'
        );

        $seccode = new Seccode([
            'font_path'       => __DIR__.'/font',
        ]);

        $seccode->display(0);
    }

    public function testAutoCodeTypeException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Code type must be these alpha_num,alpha_num_lowercase,alpha_num_uppercase,alpha,alpha_lowercase,alpha_uppercase,num,chinese.'
        );

        $seccode = new Seccode([
            'font_path'       => __DIR__.'/font',
        ]);

        $seccode->display(4, '', true, 'notExistsType');
    }

    public function testAutoMakeOutDirIfNotExist(): void
    {
        $seccode = new Seccode([
            'font_path'       => __DIR__.'/font',
        ]);

        $file = __DIR__.'/notexists/seccode.png';

        $seccode->display('hello', $file);

        $this->assertTrue(is_file($file));

        $info = getimagesize($file);

        $data = <<<'eot'
            {
                "0": 160,
                "1": 60,
                "2": 3,
                "3": "width=\"160\" height=\"60\"",
                "bits": 8,
                "mime": "image\/png"
            }
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $info
            )
        );

        unlink($file);
        rmdir(dirname($file));
    }

    /**
     * @api(
     *     zh-CN:title="验证码支持最小尺寸设置",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testMinWidthAndMinHeight(): void
    {
        $seccode = new Seccode([
            'font_path' => __DIR__.'/font',
            'width'     => 2,
            'height'    => 2,
        ]);

        $file = __DIR__.'/minWidthAndMinHeight.png';

        $seccode->display('A', $file);

        $this->assertTrue(is_file($file));

        $info = getimagesize($file);

        $data = <<<'eot'
            {
                "0": 16,
                "1": 16,
                "2": 3,
                "3": "width=\"16\" height=\"16\"",
                "bits": 8,
                "mime": "image\/png"
            }
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $info
            )
        );

        unlink($file);
    }

    /**
     * @api(
     *     zh-CN:title="验证码支持最大尺寸设置",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testMaxWidthAndMaxHeight(): void
    {
        $seccode = new Seccode([
            'font_path' => __DIR__.'/font',
            'width'     => 1200,
            'height'    => 1200,
        ]);

        $file = __DIR__.'/maxWidthAndMaxHeight.png';

        $seccode->display('IMAX', $file);

        $this->assertTrue(is_file($file));

        $info = getimagesize($file);

        $data = <<<'eot'
            {
                "0": 999,
                "1": 999,
                "2": 3,
                "3": "width=\"999\" height=\"999\"",
                "bits": 8,
                "mime": "image\/png"
            }
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $info
            )
        );

        unlink($file);
    }

    /**
     * @api(
     *     zh-CN:title="验证码随机颜色",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testWithoutRandColor(): void
    {
        $seccode = new Seccode([
            'font_path'       => __DIR__.'/font',
            'color'           => false,
        ]);

        $file = __DIR__.'/withoutRandColor.png';

        $seccode->display('ABCD', $file);

        $this->assertTrue(is_file($file));

        $info = getimagesize($file);

        $data = <<<'eot'
            {
                "0": 160,
                "1": 60,
                "2": 3,
                "3": "width=\"160\" height=\"60\"",
                "bits": 8,
                "mime": "image\/png"
            }
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $info
            )
        );

        unlink($file);
    }

    public function testFontPathException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Font path  is not exits.'
        );

        $seccode = new Seccode();
        $seccode->display();
    }

    public function testFontPathException2(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Font files not found.'
        );

        $dirname = __DIR__.'/fontEmpty';
        mkdir($dirname, 0777);

        $seccode = new Seccode([
            'font_path'       => $dirname,
        ]);
        $seccode->display();

        rmdir($dirname);
    }

    public function testDisplayImage(): void
    {
        $seccode = new Seccode([
            'font_path'       => __DIR__.'/font',
            'color'           => false,
        ]);

        $content = $this->obGetContents(fn () => $seccode->display('ABCD'));
        $this->assertTrue(!empty($content));
    }

    public function testMtRand(): void
    {
        $seccode = new Seccode([
            'font_path'       => __DIR__.'/font',
            'color'           => false,
        ]);

        $value = $this->invokeTestMethod($seccode, 'mtRand', ['3.3', '1.1']);
        $this->assertTrue(1 <= $value && $value <= 3);
    }
}
