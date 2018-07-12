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
    public function testBaseUse()
    {
    }

    public function testBackgroundPathException()
    {
        $this->expectException(\InvalidArgumentException::class);

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
        if (!function_exists('imagettftext')) {
            $this->markTestSkipped('Gd is not exists.');
        }

        $this->expectException(\InvalidArgumentException::class);

        $seccode = new Seccode([
            'background_path' => __DIR__.'/background',
        ]);

        $seccode->display();
    }
}
