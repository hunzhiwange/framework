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

namespace Tests\Cache\Pieces;

use Leevel\Cache\File;
use Leevel\Cache\IBlock;
use Leevel\Cache\ICache;

/**
 * test4.
 */
class Test4 implements IBlock
{
    public function handle(array $params = []): array
    {
        return $params;
    }

    public function cache(): ICache
    {
        return new File([
            'path' => __DIR__.'/cacheLoad',
        ]);
    }

    public static function key(array $params = []): string
    {
        return 'test4';
    }
}
