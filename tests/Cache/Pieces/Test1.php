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

namespace Tests\Cache\Pieces;

use Leevel\Cache\File;
use Leevel\Cache\IBlock;
use Leevel\Cache\ICache;

/**
 * test1.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.29
 *
 * @version 1.0
 */
class Test1 implements IBlock
{
    public function handle(array $params = []): array
    {
        return ['foo' => 'bar'];
    }

    public function cache(): ICache
    {
        return new File([
            'path' => __DIR__.'/cacheLoad',
        ]);
    }

    public static function key(array $params = []): string
    {
        return 'test1';
    }
}
