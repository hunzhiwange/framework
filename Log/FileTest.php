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

namespace Tests\Log;

use Leevel\Filesystem\Fso;
use Leevel\Log\File;
use Tests\TestCase;

/**
 * file test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.10
 *
 * @version 1.0
 */
class FileTest extends TestCase
{
    public function testBaseUse()
    {
        $file = new File([
            'path' => __DIR__,
        ]);

        $filePath = __DIR__.'/info/'.date('Y-m-d H').'.log';

        $data = [
            0 => [
                0 => 'info',
                1 => '[2018-06-10 12:03]hello',
                2 => [
                    0 => 'hello',
                    1 => 'world',
                ],
            ],
        ];

        if (is_file($filePath)) {
            unlink($filePath);
        }

        $file->save($data);

        $this->assertTrue(is_file($filePath));

        Fso::deleteDirectory(dirname($filePath), true);
    }
}
