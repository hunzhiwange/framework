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

namespace Tests\Seccode;

use Leevel\Seccode\Seccode;
use Tests\TestCase;

/**
 * seccode test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.11
 *
 * @version 1.0
 */
class SeccodeTest extends TestCase
{
    protected function setUp()
    {
        // for mac php
        if (!function_exists('imagettftext')) {
            $this->markTestSkipped('Function imagettftext is not exists.');
        }
    }

    protected function tearDown()
    {
        $dirnames = [
            __DIR__.'/backgroundEmpty',
            __DIR__.'/backgroundEmpty2',
            __DIR__.'/fontEmpty2',
            __DIR__.'/parentDirWriteable',
        ];

        foreach ($dirnames as $val) {
            if (is_dir($val)) {
                rmdir($val);
            }
        }
    }

    public function testBaseUse()
    {
        $seccode = new Seccode([
            'background_path' => __DIR__.'/background',
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

    public function testChinese()
    {
        $seccode = new Seccode([
            'background_path'         => __DIR__.'/background',
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

    public function testSetOption()
    {
        $seccode = new Seccode([
            'background_path' => __DIR__.'/background',
            'font_path'       => __DIR__.'/font',
        ]);

        $seccode->setOption('width', 200);
        $seccode->setOption('height', 100);

        $file = __DIR__.'/setoption.png';

        $seccode->display('hello', $file);

        $this->assertTrue(is_file($file));

        $info = getimagesize($file);

        $data = <<<'eot'
{
    "0": 200,
    "1": 100,
    "2": 3,
    "3": "width=\"200\" height=\"100\"",
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
     * @param string $type
     */
    public function testAutoCode(string $type)
    {
        $seccode = new Seccode([
            'background_path'         => __DIR__.'/background',
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

    public function getAutoCodeData()
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

    public function testAutoCodeSizeException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Code must be greater than 0.'
        );

        $seccode = new Seccode([
            'background_path' => __DIR__.'/background',
            'font_path'       => __DIR__.'/font',
        ]);

        $seccode->display(0);
    }

    public function testAutoCodeTypeException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Code type must be these alpha_num,alpha_num_lowercase,alpha_num_uppercase,alpha,alpha_lowercase,alpha_uppercase,num,chinese.'
        );

        $seccode = new Seccode([
            'background_path' => __DIR__.'/background',
            'font_path'       => __DIR__.'/font',
        ]);

        $seccode->display(4, '', true, 'notExistsType');
    }

    public function testAutoMakeOutDirIfNotExist()
    {
        $seccode = new Seccode([
            'background_path' => __DIR__.'/background',
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

    public function testMinWidthAndMinHeight()
    {
        $seccode = new Seccode([
            'background_path' => __DIR__.'/background',
            'font_path'       => __DIR__.'/font',
        ]);

        $seccode->setOption('width', 2);
        $seccode->setOption('height', 2);

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

    public function testMaxWidthAndMaxHeight()
    {
        $seccode = new Seccode([
            'background_path' => __DIR__.'/background',
            'font_path'       => __DIR__.'/font',
        ]);

        $seccode->setOption('width', 1200);
        $seccode->setOption('height', 1200);

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

    public function testWithBackgroundDefault()
    {
        $seccode = new Seccode([
            'background'      => false,
            'font_path'       => __DIR__.'/font',
        ]);

        $file = __DIR__.'/backgroundDefault.png';

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

    public function testWithoutRandColor()
    {
        $seccode = new Seccode([
            'background'      => false,
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

    public function testBackgroundPathException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Background path  is not exists.'
        );

        $seccode = new Seccode();

        $seccode->display();
    }

    public function testBackgroundPathException2()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Background path for/bar/background_path_not_exist is not exists.'
        );

        $seccode = new Seccode([
            'background_path' => 'for/bar/background_path_not_exist',
        ]);

        $seccode->display();
    }

    public function testFontPathException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Font path  is not exits.'
        );

        $dirname = __DIR__.'/backgroundEmpty';

        mkdir($dirname, 0777);

        $seccode = new Seccode([
            'background_path' => $dirname,
        ]);

        $seccode->display();

        rmdir($dirname);
    }

    public function testFontPathException2()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Font files not found.'
        );

        $dirname = __DIR__.'/backgroundEmpty2';
        $dirname2 = __DIR__.'/fontEmpty2';

        mkdir($dirname, 0777);
        mkdir($dirname2, 0777);

        $seccode = new Seccode([
            'background_path' => $dirname,
            'font_path'       => $dirname2,
        ]);

        $seccode->display();

        rmdir($dirname);
        rmdir($dirname2);
    }

    public function testParentDirWriteableException()
    {
        $file = __DIR__.'/parentDirWriteable/sub/hello.png';
        $sourcePath = dirname($file);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf('Unable to create the %s directory.', $sourcePath)
        );

        $seccode = new Seccode([
            'background'      => false,
            'font_path'       => __DIR__.'/font',
            'color'           => false,
        ]);

        // 设置目录只读
        // 7 = 4+2+1 分别代表可读可写可执行
        mkdir(dirname($sourcePath), 0444);

        if (is_writable(dirname($sourcePath))) {
            $this->markTestSkipped('Mkdir with chmod is invalid.');
        }

        $seccode->display('ABCD', $file);
    }
}
