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
        ];

        foreach ($dirnames as $val) {
            if (is_dir($val)) {
                rmdir($val);
            }
        }
    }

    public function testBaseUse()
    {
        $this->assertTrue(true);

        $seccode = new Seccode([
            'background_path' => __DIR__.'/background',
            'font_path'       => __DIR__.'/font',
        ]);

        $file = __DIR__.'/baseuse.png';

        $seccode->display('abc', $file);

        $this->assertTrue(is_file($file));

        $info = getimagesize($file);

        $data = <<<'eot'
array (
  0 => 160,
  1 => 60,
  2 => 3,
  3 => 'width="160" height="60"',
  'bits' => 8,
  'mime' => 'image/png',
)
eot;

        $this->assertSame(
            $data,
            $this->varExport(
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
}
